<div id="wasteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-[60] items-center justify-center p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Submit Waste Entry</h2>
            <button id="closeX" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <div class="space-y-4">
            {{-- Personal Info --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700">Full Name</label>
                <input id="entryName" type="text" placeholder="John Doe" class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-green-500 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Campus</label>
                    <select id="entryCampus" class="w-full border rounded-lg p-2.5">
                        <option value="" disabled selected>Select Campus</option>
                        @foreach ($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Building</label>
                    <select id="entryBuilding" class="w-full border rounded-lg p-2.5">
                        <option value="">Select Campus First</option>
                    </select>
                </div>
            </div>

            <hr class="my-4">

            {{-- Waste Weights --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-green-700">Biodegradable (kg)</label>
                    <input id="biodegradable_kg" type="number" step="1" class="w-full border rounded-lg p-2.5" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-blue-700">Recyclable (kg)</label>
                    <input id="recyclable_kg" type="number" step="1" class="w-full border rounded-lg p-2.5" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Residual (kg)</label>
                    <input id="residual_kg" type="number" step="1" class="w-full border rounded-lg p-2.5" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-red-700">Infectious (kg)</label>
                    <input id="infectious_kg" type="number" step="1" class="w-full border rounded-lg p-2.5" placeholder="0.00">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8">
            <button id="cancelMain" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">Cancel</button>
            <button id="submitMain" class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-md">Next Step</button>
        </div>
    </div>
</div>

<div id="confirmModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-[70] items-center justify-center p-4">
    <div class="bg-white rounded-xl p-8 w-full max-w-md shadow-2xl text-center">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h2 class="text-xl font-bold mb-2">Confirm Submission</h2>
        <p class="text-gray-600 mb-8">Are you sure you want to log this waste data? This will be added to the official reports.</p>

        <div class="flex justify-center gap-4">
            <button id="cancelConfirm" class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Go Back</button>
            <button id="confirmSubmit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold">Yes, Submit</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const wasteModal = document.getElementById('wasteModal');
    const confirmModal = document.getElementById('confirmModal');
    const campusSelect = document.getElementById('entryCampus');
    const buildingSelect = document.getElementById('entryBuilding');

    // 1. Toggle Modals
    document.getElementById('openWasteModal').onclick = () => wasteModal.classList.replace('hidden', 'flex');
    document.getElementById('cancelMain').onclick = () => wasteModal.classList.replace('flex', 'hidden');
    
    // Show Confirmation
    document.getElementById('submitMain').onclick = () => {
        confirmModal.classList.replace('hidden', 'flex');
    };

    document.getElementById('cancelConfirm').onclick = () => confirmModal.classList.replace('flex', 'hidden');

    // 2. Dynamic Building Dropdown
    campusSelect.onchange = async function() {
        const campusId = this.value;
        buildingSelect.innerHTML = '<option>Loading...</option>';

        try {
            const response = await fetch(`/api/campuses/${campusId}/buildings`);
            const buildings = await response.json();

            buildingSelect.innerHTML = '<option value="" disabled selected>Select Building</option>';
            buildings.forEach(b => {
                buildingSelect.innerHTML += `<option value="${b.id}">${b.name}</option>`;
            });
        } catch (error) {
            buildingSelect.innerHTML = '<option>Error loading buildings</option>';
        }
    };

    // 3. Final Submission
    document.getElementById('confirmSubmit').onclick = async function() {
        const data = {
            name: document.getElementById('entryName').value,
            campus_id: campusSelect.value,
            building_id: buildingSelect.value,
            biodegradable_kg: document.getElementById('biodegradable_kg').value || 0,
            recyclable_kg: document.getElementById('recyclable_kg').value || 0,
            residual_kg: document.getElementById('residual_kg').value || 0,
            infectious_kg: document.getElementById('infectious_kg').value || 0,
            _token: '{{ csrf_token() }}'
        };

        try {
            const response = await fetch('{{ route("waste.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                if (response.ok) {
                    showToast('Waste entry recorded successfully!', 'success');
                    
                    setTimeout(() => location.reload(), 1500);
                location.reload();
            }
        } catch (error) {
            showToast('Failed to save entry. Please check your data.', 'error');
        }
    };
});
</script>