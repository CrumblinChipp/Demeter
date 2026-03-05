<div class="space-y-6">
    <div class="flex items-center justify-between border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">📝 Editing: {{ $campusToEdit->name }}</h2>
        
        {{-- Campus Selector for Editing --}}
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Switch Campus:</span>
            <select onchange="window.location.href='{{ route('homepage', ['section' => 'admin', 'tab' => 'edit-campus']) }}&campus=' + this.value"
                class="rounded border-gray-300 text-sm">
                @foreach($campuses as $c)
                    <option value="{{ $c->id }}" {{ $selectedCampus == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- The Main Form --}}
    <form action="{{ route('admin.campus.update', $campusToEdit->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        {{-- Pass through the tab and section so we stay here after save --}}
        <input type="hidden" name="section" value="admin">
        <input type="hidden" name="tab" value="edit-campus">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Campus Name</label>
                <input type="text" name="campus_name" value="{{ old('campus_name', $campusToEdit->name) }}" 
                      class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            {{-- Map Upload --}}
            <div class="space-y-4">
                <label class="block text-sm font-medium text-gray-700">Campus Map</label>
                
                {{-- Container for the Images --}}
                <div class="flex flex-wrap gap-4">
                    {{-- Existing Map --}}
                    @if($campusToEdit->map)
                        <div class="relative">
                            <span class="text-[10px] uppercase font-bold text-gray-400 block mb-1">Current Map:</span>
                            <img src="{{ asset('storage/' . $campusToEdit->map) }}" 
                                class="h-40 w-auto rounded-lg border shadow-sm" 
                                alt="Current Campus Map">
                        </div>
                    @endif

                    {{-- Live Preview Placeholder --}}
                    <div id="map-preview-container" class="hidden">
                        <span class="text-[10px] uppercase font-bold text-emerald-600 block mb-1">New Selection Preview:</span>
                        <img id="map-preview-element" src="#" 
                            class="h-40 w-auto rounded-lg border-2 border-emerald-500 shadow-sm" 
                            alt="New map preview">
                    </div>
                </div>

                {{-- File Input --}}
                <input type="file" name="map" id="map-input"
                      accept="image/*"
                      class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                
                <p class="text-xs text-gray-500 italic">Max size: 2MB. Recommended: PNG or JPG.</p>
            </div>
        </div>

        {{-- Buildings Section --}}
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <label class="block text-sm font-bold text-gray-700 mb-3">Buildings Managed by this Campus</label>
            
            {{-- The Container where new rows will appear --}}
            <div id="buildings-container" class="space-y-3">
                @foreach ($campusToEdit->buildings as $building)
                    <div class="flex items-center gap-2 building-row">
                        <input type="text" name="buildings[{{ $building->id }}]" 
                              value="{{ old('buildings.'.$building->id, $building->name) }}" 
                              class="flex-1 rounded-md border-gray-300 shadow-sm text-sm focus:ring-emerald-500 focus:border-emerald-500" 
                              required>
                        <button type="button" class="remove-building-btn text-red-400 hover:text-red-600 transition px-2 text-xl">&times;</button>
                    </div>
                @endforeach
            </div>

            <button type="button" id="add-building-btn" class="mt-4 inline-flex items-center text-sm text-emerald-600 font-semibold hover:text-emerald-700">
                <span class="mr-1">＋</span> Add New Building
            </button>
        </div>

        <button type="submit" class="w-full md:w-auto px-6 py-2 bg-emerald-600 text-white font-bold rounded-lg shadow-md hover:bg-emerald-700 transition">
            Save Changes
        </button>
    </form>

    {{-- Danger Zone --}}
    <div class="mt-12 pt-6 border-t border-red-100">
        <h3 class="text-red-700 font-bold mb-2">Danger Zone</h3>
        <form action="{{ route('admin.campus.destroy', $campusToEdit->id) }}" method="POST" onsubmit="return confirm('EXTREME WARNING: This will delete ALL data for this campus. Continue?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm text-red-600 hover:underline">Permanently Delete Campus</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('buildings-container');
    const addBtn = document.getElementById('add-building-btn');

    // 1. Add New Building Row
    addBtn.addEventListener('click', function() {
        const uniqueId = 'new_' + Date.now(); // Creates a timestamp-based ID for the backend
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 building-row animate-fadeIn'; // Optional: add a CSS fade class
        
        row.innerHTML = `
            <input type="text" name="buildings[${uniqueId}]" 
                    placeholder="Enter building name" 
                    class="flex-1 rounded-md border-gray-300 shadow-sm text-sm focus:ring-emerald-500 focus:border-emerald-500" 
                    required>
            <button type="button" class="remove-building-btn text-red-400 hover:text-red-600 transition px-2 text-xl">&times;</button>
        `;
        
        container.appendChild(row);
    });

    // 2. Remove Building Row (Event Delegation)
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-building-btn')) {
            const rows = container.querySelectorAll('.building-row');
            
            // Safety: Don't let them delete the last building
            if (rows.length > 1) {
                e.target.closest('.building-row').remove();
            } else {
                alert("A campus must have at least one building.");
            }
        }
    });
});

</script>