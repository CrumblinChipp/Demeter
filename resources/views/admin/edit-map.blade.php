<div class="mb-6 flex items-center justify-between bg-blue-50 p-4 rounded-lg border border-blue-100">
    <div>
        <h2 class="text-xl font-bold text-blue-900">Map Marker Configuration</h2>
        <p class="text-sm text-blue-700">Currently editing: <strong>{{ $campus->name }}</strong></p>
    </div>
    
    <select onchange="window.location.href='{{ route('homepage', ['section' => 'admin', 'tab' => 'edit-building']) }}&campus=' + this.value"
            class="rounded-md border-gray-300 shadow-sm text-sm">
        @foreach($campuses as $c)
            <option value="{{ $c->id }}" {{ $selectedCampus == $c->id ? 'selected' : '' }}>
                {{ $c->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="p-4 max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold my-6 text-gray-800">Campus Map: {{ $campus->name ?? 'Select Campus' }}</h2>

    <div class="flex flex-col lg:flex-row gap-8">
        
        <div id="map-container" class="relative bg-gray-100 border border-gray-300 shadow-xl rounded-lg overflow-hidden lg:w-3/4">
            
            @if($campus && $campus->map)
                <img 
                    id="campus-map" 
                    src="{{ asset('storage/' . $campus->map) }}" 
                    alt="{{ $campus->name }} Map" 
                    class="w-full h-auto cursor-crosshair"
                >
            @else
                <div class="h-96 flex items-center justify-center text-gray-500">
                    No map image uploaded for this campus.
                </div>
            @endif

            <div id="marker-layer" class="absolute inset-0 pointer-events-none">
                
                @foreach($buildings as $building)
                    @if(isset($building->map_x_percent) && isset($building->map_y_percent))
                    <div 
                        class="absolute w-4 h-4 rounded-full shadow-lg border-2 border-white bg-red-600 transition-all duration-200 cursor-pointer group permanent-marker pointer-events-auto"
                        style="
                            left: {{ $building->map_x_percent }}%;
                            top: {{ $building->map_y_percent }}%;
                            transform: translate(-50%, -50%);
                        "
                        data-building-id="{{ $building->id }}"
                        data-building-name="{{ $building->name }}"
                        data-x-percent="{{ $building->map_x_percent }}"
                        data-y-percent="{{ $building->map_y_percent }}"
                    >
                        <span 
                            class="absolute whitespace-nowrap -bottom-6 left-1/2 transform -translate-x-1/2 text-xs font-semibold bg-gray-800 text-white p-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        >
                            {{ $building->name }}
                        </span>
                    </div>
                    @endif
                @endforeach
            </div>
            
        </div> 

        <div id="config-panel" class="lg:w-1/4 p-6 bg-white shadow-xl rounded-lg border border-gray-200">
            <h3 id="form-title" class="text-xl font-semibold mb-4 text-gray-700">Set Marker Coordinates</h3>
            
            <form id="marker-form" class="space-y-4">
                <input type="hidden" id="building-id" value=""> 
                <input type="hidden" id="marker-x">
                <input type="hidden" id="marker-y">
                
                <div>
                    <label for="building-select" class="block text-sm font-medium text-gray-700">Select Building to Mark</label>
                    <select id="building-select" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="" selected>--- Select a building ---</option>
                        
                        {{-- Loop over buildings that DON'T have coordinates yet --}}
                        @foreach($buildings as $building)
                            @if(is_null($building->map_x_percent))
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <p id="current-coords" class="text-sm text-gray-600 hidden">
                    Coordinates: <span id="x-display">0</span>, <span id="y-display">0</span>
                </p>

                <div class="flex gap-4">
                    <button type="submit" id="save-button" class="flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" disabled>
                        Save Marker Position
                    </button>
                    <button type="button" id="delete-button" class="w-24 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 hidden">
                        Remove Mark
                    </button>
                </div>
                <button type="button" id="cancel-button" class="w-full py-1 text-sm text-gray-500 hover:text-gray-700 hidden">
                    Cancel
                </button>
                
                <p id="instruction" class="text-sm text-gray-500 italic mt-4">
                    1. Select a building above.<br>
                    2. Click on the map to place its marker.
                </p>
            </form>
        </div>
      </div> 
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const campusMap = document.getElementById('campus-map');
        const markerLayer = document.getElementById('marker-layer');
        const markerForm = document.getElementById('marker-form');
        
        // Form Inputs & Selects
        const buildingSelect = document.getElementById('building-select');
        const buildingIdInput = document.getElementById('building-id');
        const markerXInput = document.getElementById('marker-x');
        const markerYInput = document.getElementById('marker-y');
        const xDisplay = document.getElementById('x-display');
        const yDisplay = document.getElementById('y-display');
        const currentCoords = document.getElementById('current-coords');

        // Buttons & UI
        const formTitle = document.getElementById('form-title');
        const saveButton = document.getElementById('save-button');
        const deleteButton = document.getElementById('delete-button');
        const cancelButton = document.getElementById('cancel-button');
        const instruction = document.getElementById('instruction');

        // State Variables
        let currentMarkerElement = null;
        let selectedMarkerElement = null;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;


        // --- UTILITY & STATE MANAGEMENT ---

        function resetFormState() {
            buildingIdInput.value = '';
            buildingSelect.disabled = false;  
            saveButton.disabled = true;
            
            // UI Reset
            formTitle.textContent = 'Set Marker Coordinates';
            saveButton.textContent = 'Save Marker Position';
            deleteButton.classList.add('hidden');
            cancelButton.classList.add('hidden');
            currentCoords.classList.add('hidden');
            instruction.innerHTML = '1. Select a building above.<br>2. Click on the map to place its marker.';


            // Clear active selection highlight
            if (selectedMarkerElement) {
                selectedMarkerElement.classList.remove('ring-4', 'ring-blue-500', 'ring-opacity-50');
                selectedMarkerElement = null;
            }
            
            // Remove temporary placement marker
            if (currentMarkerElement) {
                currentMarkerElement.remove();
                currentMarkerElement = null;
            }
        }

        function createMarkerElement(id, xPercent, yPercent, name, isTemporary = false) {
            const marker = document.createElement('div');
            
            marker.className = `
                absolute w-4 h-4 rounded-full shadow-lg border-2 pointer-events-auto
                transition-all duration-200 cursor-pointer 
                ${isTemporary ? 'bg-yellow-400 border-yellow-700 animate-pulse' : 'bg-red-600 border-white hover:bg-red-800'}
            `;
            
            marker.style.left = `${xPercent}%`;
            marker.style.top = `${yPercent}%`;
            marker.style.transform = 'translate(-50%, -50%)';
            
            if (!isTemporary) {
                // Permanent markers need the ID and name for editing
                marker.dataset.buildingId = id;
                marker.dataset.name = name;
                marker.dataset.xPercent = xPercent;
                marker.dataset.yPercent = yPercent;
                marker.classList.add('permanent-marker');
                marker.classList.add('group');
                
                const label = document.createElement('span');
                label.textContent = name;
                label.className = `
                    absolute whitespace-nowrap -bottom-6 left-1/2 transform -translate-x-1/2 
                    text-xs font-semibold bg-gray-800 text-white p-1 rounded-md opacity-0 
                    group-hover:opacity-100 transition-opacity duration-300
                `;
                marker.appendChild(label);
            }

            return marker;
        }

        // Listener 1: Handle Map Click for placing/moving marker
        campusMap.addEventListener('click', (event) => {
              const targetBuildingId = buildingIdInput.value;

            if (!targetBuildingId) {
                    alert('Please select a building to mark, or click an existing marker to edit.');
                    return;
                }
            
            const isEditing = !!selectedMarkerElement;

            // Calculate percentage coordinates
            const x = event.offsetX;
            const y = event.offsetY;
            const imageWidth = campusMap.offsetWidth;
            const imageHeight = campusMap.offsetHeight;
            const xPercentage = (x / imageWidth) * 100;
            const yPercentage = (y / imageHeight) * 100;

            // 1. Update form fields
            markerXInput.value = xPercentage.toFixed(2);
            markerYInput.value = yPercentage.toFixed(2);
            xDisplay.textContent = xPercentage.toFixed(2);
            yDisplay.textContent = yPercentage.toFixed(2);
            currentCoords.classList.remove('hidden');

            // 2. Update temporary marker display
            if (currentMarkerElement) {
                currentMarkerElement.remove();
            }
            
            // Get the name for the temporary marker
            const name = isEditing 
                ? selectedMarkerElement.dataset.name 
                : buildingSelect.options[buildingSelect.selectedIndex].text;

            currentMarkerElement = createMarkerElement(
                isEditing ? buildingIdInput.value : null, 
                xPercentage, 
                yPercentage, 
                name, 
                true
            );
            markerLayer.appendChild(currentMarkerElement);
            
            // 3. Enable save button
            saveButton.disabled = false;
            saveButton.textContent = isEditing ? 'Update Marker Position' : 'Save Marker Position';
        });

        // Listener 2: Handle Selecting a NEW building from the dropdown
        buildingSelect.addEventListener('change', () => {
            
            // 1. Immediately reset the form state to clean up any previous markers/edits
            resetFormState(); 
            
            const selectedId = buildingSelect.value;

            if (!selectedId) {
              resetFormState();
                return; 
            }

            // 2. Set the hidden input ID. This is the ID we intend to save a marker for.
            buildingIdInput.value = selectedId;

            // 3. Update instructions to guide the user
            const selectedName = buildingSelect.options[buildingSelect.selectedIndex].text;
            instruction.innerHTML =`<strong>${selectedName}</strong> selected. Now, click on the map to place the marker.`;
        });
        
        // Listener 3: Handle Permanent Marker Click for EDITING/REMOVING
        markerLayer.addEventListener('click', (event) => {
            const markerEl = event.target.closest('.permanent-marker');
            if (markerEl) {
                // Prevent the map click listener from firing immediately after selecting
                event.stopPropagation(); 
                resetFormState();

                const id = markerEl.dataset.buildingId;
                const name = markerEl.dataset.name;
                const x = markerEl.dataset.xPercent;
                const y = markerEl.dataset.yPercent;

                // Set State
                buildingIdInput.value = id;
                markerXInput.value = x; 
                markerYInput.value = y;
                selectedMarkerElement = markerEl; 
                buildingSelect.disabled = true;

                // UI Update
                formTitle.textContent = `Editing: ${name}`;
                saveButton.textContent = 'Update Marker Position';
                saveButton.disabled = true;
                deleteButton.classList.remove('hidden');
                cancelButton.classList.remove('hidden');
                instruction.innerHTML = `To move **${name}**, click a new location on the map.`;

                // Highlight and make temporary marker
                markerEl.classList.add('ring-4', 'ring-blue-500', 'ring-opacity-50');
                
                // Show the temporary (yellow) marker over the permanent (red) one for clarity
                currentMarkerElement = createMarkerElement(id, parseFloat(x), parseFloat(y), name, true);
                markerLayer.appendChild(currentMarkerElement);
            }
        });

        // Listener 4: Handle Cancel Button
        cancelButton.addEventListener('click', resetFormState);

        // Listener 5: Handle Delete Button (Removing marker coordinates)
        deleteButton.addEventListener('click', async () => {
            const id = buildingIdInput.value;
            const name = selectedMarkerElement.dataset.name;

            if (!confirm(`Are you sure you want to remove the marker location for "${name}"? This will NOT delete the building record, only its map location.`)) {
                return;
            }

            // --- API CALL: DELETE (Logically) ---
            try {
                console.log(`Sending DELETE (PUT with null) request for Building ID: ${id}`);
                
                const response = await fetch(`/buildings/${id}/coordinates`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    // Send null coordinates to clear the marker location
                    body: JSON.stringify({
                        _method: 'PUT',
                        map_x_percent: null, 
                        map_y_percent: null, 
                    }),
                });
                
                if (!response.ok) throw new Error('Delete location failed.');

                // Successful Deletion
                if (selectedMarkerElement) {
                    selectedMarkerElement.remove();
                }
                
                // Add the building back to the select dropdown
                const option = new Option(name, id);
                buildingSelect.appendChild(option);

                alert(`Marker location for "${name}" removed successfully.`);
                resetFormState();

            } catch (error) {
                console.error('Error removing marker location:', error);
                alert('Failed to remove marker location. Check console for details.');
            }
        });


        // Listener 6: Handle Form Submission (Update Coordinates)
        markerForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const buildingId = buildingIdInput.value; 
            const name = selectedMarkerElement 
                ? selectedMarkerElement.dataset.name 
                : buildingSelect.options[buildingSelect.selectedIndex].text;
            
            if (!buildingId || saveButton.disabled) return;

            const markerData = {
                _method: 'PUT',
                map_x_percent: parseFloat(markerXInput.value),
                map_y_percent: parseFloat(markerYInput.value),
            };
            
            const url = `/buildings/${buildingId}/coordinates`;

            // --- CALL: POST ---
            try {
                console.log(`UPDATE request to ${url} with data:`, markerData);
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    body: JSON.stringify(markerData),
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`API failed: ${errorText}`);
                }
                
                const updatedBuilding = await response.json();

                // 1. Remove old/temporary marker element
                if (selectedMarkerElement) {
                    selectedMarkerElement.remove();
                } else if (currentMarkerElement) {
                    currentMarkerElement.remove();
                }

                // 2. Remove the building from the select dropdown
                document.querySelector(`#building-select option[value="${buildingId}"]`)?.remove();
                
                // 3. Render the new permanent marker
                const permanentMarker = createMarkerElement(
                    updatedBuilding.id, 
                    updatedBuilding.map_x_percent, 
                    updatedBuilding.map_y_percent, 
                    name, 
                    false
                );
                markerLayer.appendChild(permanentMarker);

                showToast(`Marker for "${name}" saved successfully!`);
                resetFormState(); 

            } catch (error) {
                console.error('Error saving/updating marker:', error);
                showToast('Failed to save Marker','error');
            }
        });

        resetFormState(); 
    });
</script>