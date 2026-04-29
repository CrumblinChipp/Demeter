<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Smart Bins</h1>
    </div>

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

    {{-- TABLE CONTAINER --}}
    <div class="overflow-x-auto bg-white shadow-sm border border-gray-200 rounded-lg">

        <table class="table w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="p-4">Bin Name</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-center font-bold">Current Weight (kg)</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @foreach ($smart_bins as $bin)
                <tr class="bin-item hover:bg-gray-50 transition"
                    data-building="{{ $bin->building_id }}">
                @php
                    if ($bin->status >= 71) {
                        $colorClass = 'text-red-700';
                        $label = 'Full';
                    } elseif ($bin->status >= 11) {
                        $colorClass = 'text-amber-600';
                        $label = 'Filled';
                    } else {
                        $colorClass = 'text-green-700';
                        $label = 'Empty';
                    }
                    $strokeDash = $bin->status . ', 100';
                @endphp
                    {{-- Bin Name --}}
                    <td class="p-4 font-medium whitespace-nowrap">
                        {{ $bin->name }}
                    </td>

                    {{-- Status --}}
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Smaller, cleaner circle --}}
                            <div class="relative w-8 h-8">
                                <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                                    <path class="stroke-current text-gray-200"
                                        stroke-width="4"
                                        fill="none"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    
                                    <path class="stroke-current {{ $colorClass }}"
                                        stroke-width="4"
                                        stroke-dasharray="{{ $strokeDash }}"
                                        stroke-linecap="round"
                                        fill="none"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium {{ $colorClass }}">
                                {{ $bin->status }}%
                            </span>
                        </div>
                    </td>

                    {{-- Weight --}}
                    <td class="p-4 text-center font-bold bg-gray-50">
                        {{ number_format($bin->current_weight, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const buildingFilter = document.getElementById("buildingFilter");
    const bins = document.querySelectorAll(".bin-item");

    function applyFilter(buildingId) {

        bins.forEach(bin => {
            const binBuilding = bin.dataset.building;

            if (!buildingId || binBuilding === buildingId) {
                bin.style.display = "table-row";
            } else {
                bin.style.display = "none";
            }
        });
    }

    buildingFilter.addEventListener("change", () => {
        const buildingId = buildingFilter.value;

        // Apply filter
        applyFilter(buildingId);

        // Update URL WITHOUT reload
        const url = new URL(window.location);
        url.searchParams.set('section', 'bin');

        if (buildingId) {
            url.searchParams.set('building', buildingId);
        } else {
            url.searchParams.delete('building');
        }

        window.history.pushState({}, '', url);
    });

    // 🔥 Load filter from URL (important for map → bin)
    const params = new URLSearchParams(window.location.search);
    const initialBuilding = params.get("building");

    if (initialBuilding) {
        buildingFilter.value = initialBuilding;
        applyFilter(initialBuilding);
    }

});
</script>
