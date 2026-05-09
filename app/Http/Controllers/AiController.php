<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\WasteEntry;
use App\Models\Building;
use App\Models\Bin;
use App\Models\Campus;
use App\Models\Pivot;
use Illuminate\Support\Facades\DB;

class AiController
{
    private const MAX_HISTORY = 30;

    public function ask(Request $request)
    {
        if ($request->input('action') === 'clear') {
            session()->forget('ai_history');
            return response()->json([
                'status' => 'success',
                'answer' => 'Chat history cleared. Ask me anything!',
            ]);
        }

        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $question = $request->input('question');
        $history = session('ai_history', []);

        $systemPrompt = $this->buildSystemPrompt();

        $history[] = [
            'role' => 'user',
            'parts' => [['text' => $question]],
        ];

        try {
            $answer = $this->callGemini($systemPrompt, $history);
        } catch (\Exception $e) {
            \Log::error('AI processing error: ' . $e->getMessage());
            $answer = "Sorry, I encountered an internal error. Please try again.";
        }

        $history[] = [
            'role' => 'model',
            'parts' => [['text' => $answer]],
        ];

        if (count($history) > self::MAX_HISTORY) {
            $history = array_slice($history, -self::MAX_HISTORY);
        }

        session(['ai_history' => $history]);

        return response()->json([
            'status' => 'success',
            'answer' => $answer,
        ]);
    }

    private function buildSystemPrompt(): string
    {
        return <<<PROMPT
You are Demeter AI, a smart waste management assistant for a university campus waste monitoring system called "Demeter".
Your job is to answer questions about waste collection data, bin statuses, building waste output, and campus sustainability.

CRITICAL INSTRUCTION: You DO NOT have the database context loaded automatically. 
Instead, you have access to TOOLS (functions). 
If the user asks a question about waste data, building waste, or bin statuses, you MUST use a tool to query the database.
Only answer using the data returned from the tools.

Rules:
- Answer concisely and clearly.
- Format numbers nicely (e.g., "12.50 kg").
- Be friendly and professional.
- Keep responses under 200 words unless the user asks for detail.
- If asked to do something outside your capabilities or unrelated to waste management, politely decline.
PROMPT;
    }

    private function callGemini(string $systemPrompt, array &$history): string
    {
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) return "Gemini API key is not configured.";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        // Define our tools (Structured RAG)
        $tools = [
            [
                'functionDeclarations' => [
                    [
                        'name' => 'get_waste_summary',
                        'description' => 'Aggregate waste data by date range, building, or type. e.g., How much waste did we collect last week?',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'building_name' => ['type' => 'STRING', 'description' => 'Optional building name.'],
                                'days' => ['type' => 'INTEGER', 'description' => 'Number of past days to query.'],
                                'waste_type' => ['type' => 'STRING', 'description' => 'Optional type: residual, recyclable, biodegradable, infectious']
                            ]
                        ]
                    ],
                    [
                        'name' => 'get_building_ranking',
                        'description' => 'Rank buildings by total waste output. e.g., Top 3 dirtiest or cleanest buildings this month.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'days' => ['type' => 'INTEGER', 'description' => 'Number of past days.'],
                                'limit' => ['type' => 'INTEGER', 'description' => 'Number of top buildings to return.'],
                                'order' => ['type' => 'STRING', 'description' => 'Sort order: "desc" for most waste (dirtiest), "asc" for least waste (cleanest).']
                            ]
                        ]
                    ],
                    [
                        'name' => 'compare_periods',
                        'description' => 'Compare waste between two time periods. e.g., Is waste increasing compared to last month?',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'period1_days' => ['type' => 'INTEGER', 'description' => 'Days for period 1 (e.g. 30 for last month)'],
                                'period2_days' => ['type' => 'INTEGER', 'description' => 'Days for period 2 (e.g. 60 for the month before last)'],
                                'building_name' => ['type' => 'STRING', 'description' => 'Optional building name']
                            ],
                            'required' => ['period1_days', 'period2_days']
                        ]
                    ],
                    [
                        'name' => 'get_collection_history',
                        'description' => 'Fetch pivot table records (collection events) for a specific bin. e.g., Show me the collection history for Bin 5.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'bin_name' => ['type' => 'STRING', 'description' => 'The name or device key of the bin.']
                            ],
                            'required' => ['bin_name']
                        ]
                    ],
                    [
                        'name' => 'register_bin',
                        'description' => 'Trigger bin registration. Admin only. e.g., Register the unmatched bin DEV-007 to Engineering.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'device_key' => ['type' => 'STRING', 'description' => 'The device key of the unmatched bin (e.g., DEV-007)'],
                                'building_name' => ['type' => 'STRING', 'description' => 'The name of the building to assign to.']
                            ],
                            'required' => ['device_key', 'building_name']
                        ]
                    ],
                    [
                        'name' => 'get_campus_overview',
                        'description' => 'Get a general overview of the campus, buildings, and total waste collected campus-wide.',
                    ],
                    [
                        'name' => 'predict_full_bins',
                        'description' => 'Predict which bins will be full soon based on current status (over 70%).',
                    ],
                    [
                        'name' => 'get_bin_ranking',
                        'description' => 'Rank individual bins by the total sum of waste collected in their history. e.g., Which bin has the highest or lowest sum of weights?',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'limit' => ['type' => 'INTEGER', 'description' => 'Number of top bins to return (default 5).'],
                                'order' => ['type' => 'STRING', 'description' => 'Sort order: "desc" for highest weights, "asc" for lowest weights.']
                            ]
                        ]
                    ],
                    [
                        'name' => 'get_bin_status',
                        'description' => 'Get the current status (fullness percentage, weight, type) of smart bins.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'building_name' => ['type' => 'STRING', 'description' => 'Optional building filter.']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $maxLoops = 4;
        $loopCount = 0;

        while ($loopCount < $maxLoops) {
            $loopCount++;

            $payload = [
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]]
                ],
                'contents' => $history,
                'tools' => $tools
            ];

            $response = Http::withoutVerifying()->timeout(30)->post($url, $payload);

            if (!$response->successful()) {
                \Log::error('Gemini API Error: ' . $response->status() . ' - ' . $response->body());
                if ($response->status() === 429) return 'I\'m being rate-limited. Please wait a minute!';
                if ($response->status() === 503) return 'The AI model is experiencing high demand. Please try again.';
                return 'Sorry, the AI service returned an error (HTTP ' . $response->status() . ').';
            }

            $data = $response->json();
            $part = $data['candidates'][0]['content']['parts'][0] ?? null;

            if (!$part) {
                return 'Sorry, I could not generate a response.';
            }

            if (isset($part['functionCall'])) {
                $functionCall = $part['functionCall'];
                $functionName = $functionCall['name'];
                $args = $functionCall['args'] ?? [];

                if (empty($args)) {
                    $functionCall['args'] = new \stdClass();
                }

                $history[] = [
                    'role' => 'model',
                    'parts' => [['functionCall' => $functionCall]]
                ];

                $functionResult = "";
                try {
                    if ($functionName === 'get_waste_summary') {
                        $functionResult = $this->tool_get_waste_summary($args);
                    } elseif ($functionName === 'get_building_ranking') {
                        $functionResult = $this->tool_get_building_ranking($args);
                    } elseif ($functionName === 'compare_periods') {
                        $functionResult = $this->tool_compare_periods($args);
                    } elseif ($functionName === 'get_collection_history') {
                        $functionResult = $this->tool_get_collection_history($args);
                    } elseif ($functionName === 'register_bin') {
                        $functionResult = $this->tool_register_bin($args);
                    } elseif ($functionName === 'predict_full_bins') {
                        $functionResult = $this->tool_predict_full_bins();
                    } elseif ($functionName === 'get_bin_ranking') {
                        $functionResult = $this->tool_get_bin_ranking($args);
                    } elseif ($functionName === 'get_bin_status') {
                        $functionResult = $this->tool_get_bin_status($args);
                    } elseif ($functionName === 'get_campus_overview') {
                        $functionResult = $this->tool_get_campus_overview();
                    } else {
                        $functionResult = "Error: Unknown function {$functionName}";
                    }
                } catch (\Exception $e) {
                    $functionResult = "Error executing tool: " . $e->getMessage();
                }

                $history[] = [
                    'role' => 'function',
                    'parts' => [
                        [
                            'functionResponse' => [
                                'name' => $functionName,
                                'response' => [
                                    'result' => $functionResult
                                ]
                            ]
                        ]
                    ]
                ];
            } else {
                return $part['text'] ?? 'Sorry, no text was returned.';
            }
        }

        return "I had to think for too long. Could you ask that differently?";
    }

    // ==========================================
    // TOOL IMPLEMENTATIONS (STRUCTURED RAG)
    // ==========================================

    private function tool_get_campus_overview()
    {
        $campuses = Campus::with('buildings')->get();
        $res = [];
        foreach ($campuses as $campus) {
            $bNames = $campus->buildings->pluck('name')->join(', ');
            $res[] = "Campus: {$campus->name}, Buildings: {$bNames}";
        }
        $totalWaste = WasteEntry::where('date', '>=', now()->subDays(30)->toDateString())->get()->sum('total_weight');
        $res[] = "Total waste collected campus-wide in last 30 days: {$totalWaste}kg";
        return implode("\n", $res);
    }

    private function tool_get_waste_summary($args)
    {
        $days = $args['days'] ?? 30;
        $buildingName = $args['building_name'] ?? null;
        $wasteType = $args['waste_type'] ?? null;

        $query = WasteEntry::where('date', '>=', now()->subDays((int)$days)->toDateString());

        if (!empty($buildingName)) {
            $building = Building::where('name', 'LIKE', "%{$buildingName}%")->first();
            if ($building) {
                $query->where('building_id', $building->id);
            } else {
                return "Building '{$buildingName}' not found. Ask the user to clarify.";
            }
        }

        $entries = $query->get();
        if ($wasteType) {
            $col = strtolower($wasteType) . '_kg';
            $sum = $entries->sum($col);
            return "Total {$wasteType} waste in last {$days} days: {$sum}kg" . ($buildingName ? " for {$buildingName}" : " (All Buildings)");
        }

        $total = $entries->sum('total_weight');
        $bio = $entries->sum('biodegradable_kg');
        $res = $entries->sum('residual_kg');
        $rec = $entries->sum('recyclable_kg');
        $inf = $entries->sum('infectious_kg');

        return "Waste Summary (Last {$days} days)" . ($buildingName ? " for {$buildingName}" : " (All Buildings)") . ":\nTotal: {$total}kg\nBiodegradable: {$bio}kg\nResidual: {$res}kg\nRecyclable: {$rec}kg\nInfectious: {$inf}kg";
    }

    private function tool_get_building_ranking($args)
    {
        $days = $args['days'] ?? 30;
        $limit = $args['limit'] ?? 3;
        $order = $args['order'] ?? 'desc';

        $buildings = Building::all();
        $rankings = [];

        foreach ($buildings as $b) {
            $total = WasteEntry::where('building_id', $b->id)
                ->where('date', '>=', now()->subDays((int)$days)->toDateString())
                ->get()
                ->sum('total_weight');
            $rankings[] = ['name' => $b->name, 'total' => $total];
        }

        if (strtolower($order) === 'asc') {
            usort($rankings, fn($a, $b) => $a['total'] <=> $b['total']);
            $orderText = "least waste (cleanest)";
        } else {
            usort($rankings, fn($a, $b) => $b['total'] <=> $a['total']);
            $orderText = "most waste (dirtiest)";
        }

        $top = array_slice($rankings, 0, $limit);

        $res = ["Top {$limit} buildings with {$orderText} (Last {$days} days):"];
        foreach ($top as $i => $r) {
            $res[] = ($i + 1) . ". {$r['name']} - {$r['total']}kg";
        }
        return implode("\n", $res);
    }

    private function tool_compare_periods($args)
    {
        $p1 = $args['period1_days'] ?? 30;
        $p2 = $args['period2_days'] ?? 60;
        $bName = $args['building_name'] ?? null;

        $q1 = WasteEntry::where('date', '>=', now()->subDays((int)$p1)->toDateString());
        $q2 = WasteEntry::where('date', '>=', now()->subDays((int)$p2)->toDateString())
                        ->where('date', '<', now()->subDays((int)$p1)->toDateString());

        if (!empty($bName)) {
            $b = Building::where('name', 'LIKE', "%{$bName}%")->first();
            if ($b) {
                $q1->where('building_id', $b->id);
                $q2->where('building_id', $b->id);
            } else {
                return "Building '{$bName}' not found.";
            }
        }

        $sum1 = $q1->get()->sum('total_weight');
        $sum2 = $q2->get()->sum('total_weight');

        return "Comparison" . ($bName ? " for {$bName}" : " (All Buildings)") . ":\nRecent {$p1} days: {$sum1}kg\nPrevious period (from {$p2} days ago to {$p1} days ago): {$sum2}kg";
    }

    private function tool_get_collection_history($args)
    {
        $binName = $args['bin_name'] ?? '';
        $bin = Bin::where('name', 'LIKE', "%{$binName}%")->orWhere('device_key', 'LIKE', "%{$binName}%")->first();
        if (!$bin) return "Bin '{$binName}' not found.";

        $pivots = Pivot::where('bin_id', $bin->bin_id)->orderBy('entry_date', 'desc')->limit(10)->get();
        if ($pivots->isEmpty()) return "No collection history found for Bin '{$bin->name}'.";

        $res = ["Collection history for Bin '{$bin->name}' (last 10 events):"];
        foreach ($pivots as $p) {
            $date = $p->entry_date ? $p->entry_date->toDateString() : 'Unknown Date';
            $res[] = "Date: {$date}, Weight Collected: {$p->weight}kg";
        }
        return implode("\n", $res);
    }

    private function tool_register_bin($args)
    {
        $deviceKey = $args['device_key'] ?? '';
        $buildingName = $args['building_name'] ?? '';

        $bin = Bin::where('device_key', $deviceKey)->first();
        if (!$bin) return "Unmatched bin with device key '{$deviceKey}' not found.";
        if ($bin->is_registered) return "Bin '{$deviceKey}' is already registered.";

        $building = Building::where('name', 'LIKE', "%{$buildingName}%")->first();
        if (!$building) return "Building '{$buildingName}' not found. Cannot register bin.";

        $bin->is_registered = true;
        $bin->building_id = $building->id;
        $bin->save();

        return "Success! Bin '{$deviceKey}' has been registered to {$building->name}.";
    }

    private function tool_predict_full_bins()
    {
        $bins = Bin::where('status', '>', 70)->with('building')->get();
        if ($bins->isEmpty()) return "No bins are currently near full capacity.";

        $res = ["Bins predicted to be full soon (over 70% capacity):"];
        foreach ($bins as $bin) {
            $bName = $bin->building ? $bin->building->name : 'Unknown';
            $res[] = "Bin '{$bin->name}' ({$bName}) is at {$bin->status}% capacity ({$bin->current_weight}kg / {$bin->capacity}kg). Needs attention soon.";
        }
        return implode("\n", $res);
    }

    private function tool_get_bin_status($args)
    {
        $buildingName = $args['building_name'] ?? null;
        
        $query = Bin::with('building');
        if (!empty($buildingName)) {
            $building = Building::where('name', 'LIKE', "%{$buildingName}%")->first();
            if ($building) {
                $query->where('building_id', $building->id);
            } else {
                return "Building '{$buildingName}' not found.";
            }
        }
        
        $bins = $query->get();
        if ($bins->isEmpty()) {
            return "No bins found.";
        }
        
        $res = [];
        foreach ($bins as $bin) {
            $bName = $bin->building ? $bin->building->name : 'Unknown';
            $res[] = "Bin '{$bin->name}' ({$bName}): Type={$bin->waste_type}, Fill={$bin->status}%, Weight={$bin->current_weight}kg / {$bin->capacity}kg max.";
        }
        return implode("\n", $res);
    }

    private function tool_get_bin_ranking($args)
    {
        $limit = $args['limit'] ?? 5;
        $order = strtolower($args['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        
        $rankings = Pivot::select('bin_id', DB::raw('SUM(weight) as total'))
            ->groupBy('bin_id')
            ->orderBy('total', $order)
            ->limit($limit)
            ->get();

        if ($rankings->isEmpty()) {
            return "No bin collection history available to rank.";
        }

        $orderText = $order === 'asc' ? 'lowest' : 'highest';
        $res = ["Top {$limit} bins with the {$orderText} historical collection weights:"];
        foreach ($rankings as $i => $r) {
            $bin = Bin::with('building')->where('bin_id', $r->bin_id)->first();
            $bName = $bin && $bin->building ? $bin->building->name : 'Unknown Building';
            $binName = $bin ? $bin->name : 'Unknown Bin';
            $res[] = ($i + 1) . ". {$binName} ({$bName}) - {$r->total}kg";
        }
        return implode("\n", $res);
    }
}
