<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Smart Bins</h1>
    </div>

    {{-- FILTER --}}
    <div class="flex items-center gap-2">
        <label class="text-sm font-medium text-gray-600">Building:</label>
        <select id="buildingFilter"
            class="bg-gray-50 text-gray-900 text-sm rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 p-2">
            
            <option value="">All Buildings</option>

            @foreach ($campus->buildings as $b)
                <option value="{{ $b->id }}">
                    {{ $b->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- BIN CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($smart_bins as $bin)
            @if($bin->is_registered == TRUE)
                @php
                    if ($bin->status >= 71) {
                        $colorClass = 'text-red-600';
                        $borderColor = 'border-red-400';
                        $bgAccent = 'bg-red-50';
                        $label = 'Full';
                        $dotColor = 'bg-red-500';
                    } elseif ($bin->status >= 11) {
                        $colorClass = 'text-amber-600';
                        $borderColor = 'border-amber-400';
                        $bgAccent = 'bg-amber-50';
                        $label = 'Filled';
                        $dotColor = 'bg-amber-500';
                    } else {
                        $colorClass = 'text-green-600';
                        $borderColor = 'border-green-400';
                        $bgAccent = 'bg-green-50';
                        $label = 'Empty';
                        $dotColor = 'bg-green-500';
                    }
                    $strokeDash = $bin->status . ', 100';
                @endphp

                <div class="bin-item bg-white rounded-xl shadow-sm border {{ $borderColor }} border-l-4 p-5 hover:shadow-md transition-all cursor-pointer"
                    data-building="{{ $bin->building_id }}"
                    onclick="toggleBinDetail({{ $bin->bin_id }})">
                    
                    {{-- Header Row --}}
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-800 truncate">{{ $bin->name }}</h3>
                            <p class="text-xs text-gray-500 truncate">{{ $bin->building->name ?? '—' }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="relative w-12 h-12 flex-shrink-0">
                                <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                                    <path class="stroke-current text-gray-200"
                                        stroke-width="3.5" fill="none"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    <path class="stroke-current {{ $colorClass }}"
                                        stroke-width="3.5"
                                        stroke-dasharray="{{ $strokeDash }}"
                                        stroke-linecap="round" fill="none"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center text-[9px] font-bold {{ $colorClass }}">
                                    {{ $bin->status }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Row --}}
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div class="{{ $bgAccent }} rounded-lg p-2">
                            <div class="text-xs text-gray-500">Status</div>
                            <div class="text-sm font-semibold {{ $colorClass }} flex items-center justify-center gap-1">
                                <span class="w-2 h-2 rounded-full {{ $dotColor }}"></span>
                                {{ $label }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-2">
                            <div class="text-xs text-gray-500">Weight</div>
                            <div class="text-sm font-semibold text-gray-800">{{ number_format($bin->current_weight, 1) }} kg</div>
                        </div>
                    </div>

                    {{-- Details Row (always visible) --}}
                    <div class="mt-3 pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 text-xs text-gray-500">
                        <div>
                            <span class="font-medium text-gray-600">Type:</span> 
                            {{ $bin->waste_type ?? '—' }}
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Installed:</span> 
                            {{ $bin->installed_at ? $bin->installed_at->format('M d, Y') : '—' }}
                        </div>
                    </div>

                    {{-- Expandable History (hidden by default) --}}
                    <div id="bin-detail-{{ $bin->bin_id }}" class="hidden mt-3 pt-3 border-t border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Recent Weight History</h4>
                        
                        @if($bin->wasteEntries && $bin->wasteEntries->count())
                            <div class="space-y-1 max-h-32 overflow-y-auto">
                                {{-- We access the pivot data specifically --}}
                                @foreach($bin->wasteEntries->take(10) as $entry)
                                    <div class="flex justify-between text-xs px-2 py-1 bg-gray-50 rounded">
                                        <span class="text-gray-600">
                                            {{ \Carbon\Carbon::parse($entry->pivot->entry_date)->format('M d, Y') }}
                                        </span>
                                        <span class="font-medium text-gray-800">
                                            {{ number_format($entry->pivot->weight, 2) }} kg
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 italic">No history yet</p>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

<script>
function toggleBinDetail(binId) {
    const el = document.getElementById('bin-detail-' + binId);
    if (el) el.classList.toggle('hidden');
}

document.addEventListener("DOMContentLoaded", () => {
    const buildingFilter = document.getElementById("buildingFilter");
    const bins = document.querySelectorAll(".bin-item");

    function applyFilter(buildingId) {
        bins.forEach(bin => {
            const binBuilding = bin.dataset.building;
            if (!buildingId || binBuilding === buildingId) {
                bin.style.display = "";
            } else {
                bin.style.display = "none";
            }
        });
    }

    buildingFilter.addEventListener("change", () => {
        const buildingId = buildingFilter.value;
        applyFilter(buildingId);

        const url = new URL(window.location);
        url.searchParams.set('section', 'bin');
        if (buildingId) {
            url.searchParams.set('building', buildingId);
        } else {
            url.searchParams.delete('building');
        }
        window.history.pushState({}, '', url);
    });

    // Load filter from URL
    const params = new URLSearchParams(window.location.search);
    const initialBuilding = params.get("building");
    if (initialBuilding) {
        buildingFilter.value = initialBuilding;
        applyFilter(initialBuilding);
    }
});
</script>
