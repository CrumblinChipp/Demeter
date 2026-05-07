# Bin Registration UI (Button-Based Flow)

```blade
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 p-6">

    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        Register Bin
    </h2>

    <form action="{{ route('bins.register') }}" method="POST">
        @csrf

        {{-- DEVICE KEY --}}
        <div class="mb-8">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Device Key
            </label>

            <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-gray-700 font-medium">
                {{ $selectedBin->device_key ?? 'No Device Selected' }}
            </div>
        </div>


        {{-- STEP 1: CAMPUS --}}
        <div class="mb-8">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">
                1. Select Campus
            </h3>

            <div class="flex flex-wrap gap-3">
                @foreach($allCampuses as $campus)
                    <button
                        type="button"
                        class="campus-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-emerald-500 hover:bg-emerald-50 transition-all"
                        data-campus-id="{{ $campus->id }}"
                    >
                        {{ $campus->name }}
                    </button>
                @endforeach
            </div>

            <input type="hidden" name="campus_id" id="selectedCampus">
        </div>


        {{-- STEP 2: BUILDING --}}
        <div class="mb-8 hidden" id="building-section">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">
                2. Select Building
            </h3>

            <div class="flex flex-wrap gap-3" id="building-container">

                @foreach($buildings as $building)
                    <button
                        type="button"
                        class="building-btn hidden px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500 hover:bg-blue-50 transition-all"
                        data-campus-id="{{ $building->campus_id }}"
                        data-building-id="{{ $building->id }}"
                    >
                        {{ $building->name }}
                    </button>
                @endforeach

            </div>

            <input type="hidden" name="building_id" id="selectedBuilding">
        </div>


        {{-- STEP 3: WASTE TYPE --}}
        <div class="mb-8 hidden" id="waste-section">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">
                3. Select Waste Type
            </h3>

            <div class="flex flex-wrap gap-3">

                <button type="button" class="waste-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-green-500 hover:bg-green-50" data-type="biodegradable">
                    Biodegradable
                </button>

                <button type="button" class="waste-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-yellow-500 hover:bg-yellow-50" data-type="recyclable">
                    Recyclable
                </button>

                <button type="button" class="waste-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-red-500 hover:bg-red-50" data-type="residual">
                    Residual
                </button>

                <button type="button" class="waste-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-purple-500 hover:bg-purple-50" data-type="infectious">
                    Infectious
                </button>

            </div>

            <input type="hidden" name="waste_type" id="selectedWasteType">
        </div>


        {{-- STEP 4: CAPACITY --}}
        <div class="mb-8 hidden" id="capacity-section">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">
                4. Enter Capacity
            </h3>

            <input
                type="number"
                step="0.1"
                min="1"
                name="capacity"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                placeholder="Enter capacity in KG"
                required
            >
        </div>


        {{-- CONFIRM BUTTON --}}
        <div class="pt-4 border-t border-gray-100">
            <button
                type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-lg transition-all"
            >
                Confirm Registration
            </button>
        </div>

    </form>
</div>


<script>

    const campusButtons = document.querySelectorAll('.campus-btn');
    const buildingButtons = document.querySelectorAll('.building-btn');
    const wasteButtons = document.querySelectorAll('.waste-btn');

    const selectedCampus = document.getElementById('selectedCampus');
    const selectedBuilding = document.getElementById('selectedBuilding');
    const selectedWasteType = document.getElementById('selectedWasteType');

    const buildingSection = document.getElementById('building-section');
    const wasteSection = document.getElementById('waste-section');
    const capacitySection = document.getElementById('capacity-section');


    // CAMPUS SELECTION
    campusButtons.forEach(button => {
        button.addEventListener('click', () => {

            campusButtons.forEach(btn => {
                btn.classList.remove('border-emerald-600', 'bg-emerald-100');
            });

            button.classList.add('border-emerald-600', 'bg-emerald-100');

            const campusId = button.dataset.campusId;

            selectedCampus.value = campusId;

            buildingSection.classList.remove('hidden');

            buildingButtons.forEach(building => {

                if (building.dataset.campusId === campusId) {
                    building.classList.remove('hidden');
                } else {
                    building.classList.add('hidden');
                }
            });
        });
    });


    // BUILDING SELECTION
    buildingButtons.forEach(button => {
        button.addEventListener('click', () => {

            buildingButtons.forEach(btn => {
                btn.classList.remove('border-blue-600', 'bg-blue-100');
            });

            button.classList.add('border-blue-600', 'bg-blue-100');

            selectedBuilding.value = button.dataset.buildingId;

            wasteSection.classList.remove('hidden');
        });
    });


    // WASTE TYPE SELECTION
    wasteButtons.forEach(button => {
        button.addEventListener('click', () => {

            wasteButtons.forEach(btn => {
                btn.classList.remove('border-gray-900', 'bg-gray-100');
            });

            button.classList.add('border-gray-900', 'bg-gray-100');

            selectedWasteType.value = button.dataset.type;

            capacitySection.classList.remove('hidden');
        });
    });

</script>
```

---

# Suggested Backend Logic

Inside your controller:

```php
Bin::where('bin_id', $binId)->update([
    'building_id' => $request->building_id,
    'waste_type' => $request->waste_type,
    'capacity' => $request->capacity,
    'is_registered' => true,
]);
```

This keeps the same hardware/device record while officially registering it into the system.
