<div class="max-w-4xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6 flex items-center gap-2">
        <span class="bg-emerald-100 p-2 rounded-lg">🏫</span> Add New Campus
    </h1>

    <form action="{{ route('admin.campus.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Left Side: Basic Info & Map --}}
            <div>
                <div class="mb-6">
                    <label for="campus_name" class="block text-gray-700 font-bold mb-2 text-sm uppercase tracking-wide">Campus Name</label>
                    <input type="text" id="campus_name" name="campus_name" value="{{ old('campus_name') }}" placeholder="e.g. Main Campus" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                </div>

                <div class="mb-4">
                    <label for="campus_map" class="block text-gray-700 font-bold mb-2 text-sm uppercase tracking-wide">Campus Map Image</label>
                    <div class="relative group border-2 border-dashed border-gray-300 rounded-xl p-4 hover:border-emerald-500 transition-colors bg-gray-50">
                        <input type="file" id="campus_map" name="campus_map" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(event)">
                        <div id="preview-container" class="text-center">
                            <img id="map-preview" class="hidden w-full h-48 object-cover rounded-lg mb-2 shadow-sm">
                            <div id="upload-placeholder">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                <p class="mt-1 text-sm text-gray-600">Click to upload map</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Building List --}}
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                <label class="block text-gray-700 font-bold mb-4 text-sm uppercase tracking-wide">Buildings Directory</label>
                <div id="buildings-container" class="space-y-3 max-h-80 overflow-y-auto pr-2">
                    <div class="flex items-center gap-2 building-row">
                        <div class="bg-white border rounded-lg px-3 py-2 flex-grow flex items-center shadow-sm">
                            <span class="text-gray-400 mr-2 text-xs">1.</span>
                            <input type="text" name="buildings[]" placeholder="Building Name" required
                                class="w-full outline-none text-gray-700 bg-transparent">
                        </div>
                        <button type="button" onclick="removeRow(this)" class="remove-btn hidden text-gray-400 hover:text-red-500 transition-colors">&times;</button>
                    </div>
                </div>
                
                <button type="button" id="add-building-btn" class="mt-4 flex items-center gap-2 text-emerald-600 font-semibold text-sm hover:text-emerald-700 transition-colors">
                    <span class="bg-emerald-100 rounded-full w-5 h-5 flex items-center justify-center">+</span>
                    Add Another Building
                </button>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transform active:scale-95 transition-all">
                Create Campus
            </button>
        </div>
    </form>
</div>

<script>

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('buildings-container');
        const addBtn = document.getElementById('add-building-btn');

        addBtn.addEventListener('click', () => {
            const rowCount = container.querySelectorAll('.building-row').length + 1;
            const newRow = document.createElement('div');
            newRow.className = 'flex items-center gap-2 building-row animate-fadeIn';
            newRow.innerHTML = `
                <div class="bg-white border rounded-lg px-3 py-2 flex-grow flex items-center shadow-sm">
                    <span class="text-gray-400 mr-2 text-xs">${rowCount}.</span>
                    <input type="text" name="buildings[]" placeholder="Building Name" required
                        class="w-full outline-none text-gray-700 bg-transparent">
                </div>
                <button type="button" onclick="removeRow(this)" class="remove-btn text-gray-400 hover:text-red-500 text-2xl transition-colors">&times;</button>
            `;
            container.appendChild(newRow);
            toggleRemoveButtons();
        });
    });

    // Remove a row
    function removeRow(btn) {
        btn.closest('.building-row').remove();
        toggleRemoveButtons();
        
        // Update numbers
        document.querySelectorAll('.building-row').forEach((row, index) => {
            row.querySelector('.text-xs').textContent = (index + 1) + '.';
        });
    }

    // Ensure you can't delete the last building
    function toggleRemoveButtons() {
        const rows = document.querySelectorAll('.building-row');
        rows.forEach(row => {
            const btn = row.querySelector('.remove-btn');
            if(rows.length > 1) btn.classList.remove('hidden');
            else btn.classList.add('hidden');
        });
    }

    // Map Image Preview
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('map-preview');
            const placeholder = document.getElementById('upload-placeholder');
            output.src = reader.result;
            output.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
