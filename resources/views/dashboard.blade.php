<div class="flex justify-between items-center mb-4">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
</div>


{{-- Filter Section --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
    
    <form id="globalFilterForm" method="GET" action="{{ route('homepage') }}" class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
        
        {{-- Campus Select --}}
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600">Campus:</label>
            <select name="campus" onchange="submitFilterForm()"
                class="bg-gray-50 text-gray-900 text-sm rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 p-2">
                @foreach ($campuses as $c)
                    <option value="{{ $c->id }}" {{ $selectedCampus == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Time Range Select --}}
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600">Period:</label>
            <select name="days" onchange="submitFilterForm()"
                class="bg-gray-50 text-gray-900 text-sm rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 p-2">
                {{-- FIX: Variable name changed from $selectedRange to $selectedDays to match Controller --}}
                <option value="7"  {{ $selectedDays == 7 ? 'selected' : '' }}>Last Week</option>
                <option value="30" {{ $selectedDays == 30 ? 'selected' : '' }}>Last 30 Days</option>
                <option value="90" {{ $selectedDays == 90 ? 'selected' : '' }}>Last 90 Days</option>
            </select>
        </div>

        {{-- Hash Carrier --}}
        <input type="hidden" name="section" value="dashboard">
    </form>
</div>

<div id="dashboard-graphs">
    
    {{-- Top stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- HIGHEST --}}
        <div class="bg-white rounded-lg shadow-md p-6 text-center border-b-4 border-red-500">
            <div class="text-sm text-red-600 font-medium flex items-center justify-center gap-2">📉 Highest Waste</div>
            {{-- FIX: Accessing keys inside the $summary array --}}
            <div class="text-4xl text-gray-800 font-bold mt-3">
                {{ $summary['highest'] }} <span class="text-lg font-normal text-gray-500">kg</span>
            </div>
            <div class="text-xs text-gray-400 mt-2">Recorded on {{ $summary['highest_date'] }}</div>
        </div>

        {{-- LOWEST --}}
        <div class="bg-white rounded-lg shadow-md p-6 text-center border-b-4 border-emerald-500">
            <div class="text-sm text-emerald-600 font-medium flex items-center justify-center gap-2">📈 Lowest Waste</div>
            <div class="text-4xl text-gray-800 font-bold mt-3">
                {{ $summary['lowest'] }} <span class="text-lg font-normal text-gray-500">kg</span>
            </div>
            {{-- Note: We didn't pass lowest_date in controller, but you can add it if needed --}}
            <div class="text-xs text-gray-400 mt-2">Best recorded day</div>
        </div>

        {{-- AVERAGE --}}
        <div class="bg-white rounded-lg shadow-md p-6 text-center border-b-4 border-blue-500">
            <div class="text-sm text-blue-600 font-medium">Daily Average</div>
            <div class="text-4xl text-gray-800 font-bold mt-3">
                {{ $summary['average'] }} <span class="text-lg font-normal text-gray-500">kg</span>
            </div>
            <div class="text-xs text-gray-400 mt-2">Per day in this period</div>
        </div>
    </div>

    <h2 class="text-xl font-bold text-gray-700 mb-4 px-1">Waste Analytics</h2>

    {{-- Main Charts Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Line Chart --}}
        <div class="md:col-span-2 bg-white rounded-lg shadow-md p-4">
            <div class="mb-4 font-semibold text-gray-700">Overall Waste Trend</div>
            <div class="relative h-72 w-full">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        {{-- Donut Chart --}}
        <div class="md:col-span-1 bg-white rounded-lg shadow-md p-4 flex flex-col">
            <div class="mb-4 font-semibold text-gray-700">Composition</div>
            <div class="flex-1 flex items-center justify-center relative min-h-[250px]">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Per Building Analysis --}}
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-end mb-4">
            <div class="font-semibold text-gray-700">Waste by Building (Daily Breakdown)</div>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- The Chart --}}
            <div class="flex-1 h-80 relative">
                <canvas id="buildingLineChart"></canvas>
            </div>
            
            {{-- The Side Legend/Stats (Rendered via Blade for speed) --}}
            <div class="w-full lg:w-64 flex flex-col gap-2 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Contribution</h4>
                @foreach($buildingTotals as $name => $total)
                <div class="flex justify-between items-center p-2 bg-gray-50 rounded border border-gray-100">
                    <span class="text-sm font-medium text-gray-700 truncate w-32" title="{{ $name }}">{{ $name }}</span>
                    <span class="text-sm font-bold text-emerald-600">{{ number_format($total, 1) }} kg</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

<script>
    window.dashboardData = {
        labels: @json($dailyLabels),
        values: @json($dailyValues),
        composition: @json($composition),
        buildingDaily: @json($buildingDaily)
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Safety Check: Stop if data is missing
    if (!window.dashboardData) {
        console.error('Dashboard data is missing');
        return;
    }

    const data = window.dashboardData;
    const ctxLine = document.getElementById('lineChart');
    const ctxDonut = document.getElementById('donutChart');
    const ctxBuilding = document.getElementById('buildingLineChart');

    // Color Palette for Buildings (Emerald/Teal Theme)
    const chartColors = [
        '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', 
        '#ec4899', '#6366f1', '#14b8a6', '#f97316', '#84cc16'
    ];

    /* ----------------------------------------------------------------
     * CHART 1: Overall Waste Trend (Line Chart)
     * ---------------------------------------------------------------- */
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: data.labels, // Dates from PHP
                datasets: [{
                    label: 'Total Waste (kg)',
                    data: data.values,   // Totals from PHP
                    borderColor: '#059669', // Emerald-600
                    backgroundColor: 'rgba(5, 150, 105, 0.1)',
                    borderWidth: 2,
                    tension: 0.3, // Smooth curves
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' kg';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    /* ----------------------------------------------------------------
     * CHART 2: Waste Composition (Doughnut)
     * ---------------------------------------------------------------- */
    if (ctxDonut && data.composition) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Biodegradable', 'Residual', 'Recyclable', 'Infectious'],
                datasets: [{
                    data: [
                        data.composition.biodegradable || 0,
                        data.composition.residual || 0,
                        data.composition.recyclable || 0,
                        data.composition.infectious || 0
                    ],
                    backgroundColor: [
                        '#22c55e', // Green (Bio)
                        '#64748b', // Slate (Residual)
                        '#3b82f6', // Blue (Recyclable)
                        '#ef4444'  // Red (Infectious)
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20 }
                    }
                },
                cutout: '70%', // Thinner ring
            }
        });
    }

    /* ----------------------------------------------------------------
     * CHART 3: Per-Building Breakdown (Multi-Line)
     * ---------------------------------------------------------------- */
    if (ctxBuilding && data.buildingDaily) {
        
        // Transform the PHP grouped data into Chart.js datasets
        const buildingDatasets = [];
        let colorIndex = 0;

        // Iterate through each building name in the object
        for (const [buildingName, entries] of Object.entries(data.buildingDaily)) {
            
            // "Map" the data: ensure every date in the main labels has a value
            const buildingDataPoints = data.labels.map(dateLabel => {
                // Find the entry for this specific date
                const entry = entries.find(e => e.date === dateLabel);
                return entry ? entry.total : 0; // If found return total, else 0
            });

            buildingDatasets.push({
                label: buildingName,
                data: buildingDataPoints,
                borderColor: chartColors[colorIndex % chartColors.length],
                backgroundColor: 'transparent',
                borderWidth: 2,
                tension: 0.3,
                pointRadius: 2,
                pointHoverRadius: 5
            });

            colorIndex++;
        }

        new Chart(ctxBuilding, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: buildingDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { boxWidth: 10, usePointStyle: true }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' kg';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>