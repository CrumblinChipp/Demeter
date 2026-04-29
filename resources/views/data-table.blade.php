<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Waste Records</h1>
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

    {{-- TABLE --}}
    <div class="overflow-x-auto bg-white shadow-sm border border-gray-200 rounded-lg">
        <table class="table w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="p-4">Date</th>
                    <th class="p-4">Building</th>
                    <th class="p-4 text-center text-emerald-700">Residual</th>
                    <th class="p-4 text-center text-blue-700">Recyclable</th>
                    <th class="p-4 text-center text-green-700">Bio</th>
                    <th class="p-4 text-center text-red-700">Infectious</th>
                    <th class="p-4 text-center font-bold">Total (kg)</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @foreach ($wastes as $waste)

                @php
                    $totalWeight = $waste->residual_kg + $waste->recyclable_kg + 
                                $waste->biodegradable_kg + $waste->infectious_kg;
                @endphp
                <tr class="waste-item hover:bg-gray-50 transition"
                    data-building="{{ $waste->building_id }}">
                    <td class="p-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($waste->date)->format('M d, Y') }}</td>
                    <td class="p-4 font-medium">{{ $waste->building->name }}</td>

                    <td class="p-4 text-center">{{ number_format($waste->residual_kg, 2) }}</td>
                    <td class="p-4 text-center">{{ number_format($waste->recyclable_kg, 2) }}</td>
                    <td class="p-4 text-center">{{ number_format($waste->biodegradable_kg, 2) }}</td>
                    <td class="p-4 text-center">{{ number_format($waste->infectious_kg, 2) }}</td>
                    <td class="p-4 text-center font-bold bg-gray-50">{{ number_format($totalWeight, 2) }}</td>

                    <td class="p-4 text-right">
                        <form method="POST" action="{{ route('waste.destroy', $waste->id) }}" onsubmit="return confirm('Permanently delete this entry?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 transition p-2">
                                🗑
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- PAGINATION & ROWS PER PAGE --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 py-4">
        <div class="text-sm text-gray-600">
            <form method="GET" action="{{ route('homepage') }}" class="flex items-center gap-2">
                <input type="hidden" name="section" value="data">
                <input type="hidden" name="campus" value="{{ $selectedCampus }}">
                <span>Rows per page:</span>
                <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded px-2 py-1 focus:ring-emerald-500">
                    <option value="20" @selected(request('per_page', 20) == 20)>20</option>
                    <option value="50" @selected(request('per_page') == 50)>50</option>
                    <option value="100" @selected(request('per_page') == 100)>100</option>
                </select>
            </form>
        </div>

        <div>
            {{ $wastes->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const buildingFilter = document.getElementById("buildingFilter");
    const wastes = document.querySelectorAll(".waste-item");

    function applyFilter(buildingId) {

        wastes.forEach(waste => {
            const wasteBuilding = waste.dataset.building;

            if (!buildingId || wasteBuilding === buildingId) {
                waste.style.display = "table-row";
            } else {
                waste.style.display = "none";
            }
        });
    }

    buildingFilter.addEventListener("change", () => {
        const buildingId = buildingFilter.value;

        // Apply filter
        applyFilter(buildingId);

        const url = new URL(window.location);
        url.searchParams.set('section', 'data');

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
