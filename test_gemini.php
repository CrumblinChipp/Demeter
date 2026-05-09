<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$apiKey = config('services.gemini.api_key');
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key={$apiKey}";

$tools = [
    [
        'functionDeclarations' => [
            [
                'name' => 'get_waste_summary',
                'description' => 'Test tool',
            ]
        ]
    ]
];

$payload = [
    'contents' => [['role' => 'user', 'parts' => [['text' => 'hi']]]],
    'tools' => $tools
];

$response = Illuminate\Support\Facades\Http::withoutVerifying()->post($url, $payload);
echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
