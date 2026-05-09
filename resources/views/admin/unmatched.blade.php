<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" x-data="{ openModal: false, activeBin: {} }">
    @foreach ($smart_bins as $bin)
        @if($bin->is_registered == FALSE)
            <div 
                {{-- Click event to fill data and open modal --}}
                @if($bin->is_detected)
                    @click= "openModal = true; activeBin = { 
                        name: '{{ $bin->name }}', 
                        key: '{{ $bin->device_key }}', 
                        weight: '{{ $bin->weight ?? 0 }}kg', 
                        status: '{{ $bin->status ?? 'Active' }}',
                        id: '{{ $bin->bin_id }}',
                    }"
                @endif
                class="rounded-xl shadow-sm border border-l-4 p-5 transition-all
                {{ $bin->is_detected 
                    ? 'bg-green-50 border-green-300 border-l-green-500 hover:shadow-md cursor-pointer' 
                    : 'bg-white border-red-300 border-l-red-500' }}"
            >
                {{-- Card Content (Same as previous) --}}
                <div class="mb-4">
                    <h3 class="font-semibold text-gray-800 truncate">Device Key: {{ $bin->device_key }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Bin Name: <span class="font-medium text-gray-700">{{ $bin->name }}</span></p>
                </div>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between"><span>Building</span><span class="font-medium">{{ $bin->building->name ?? 'Unassigned' }}</span></div>
                    <div class="flex justify-between"><span>Waste Type</span><span class="font-medium capitalize">{{ $bin->waste_type }}</span></div>
                </div>

                {{-- Visual Hint for Clickable items --}}
                @if($bin->is_detected)
                    <div class="mt-3 text-right">
                        <span class="text-[10px] font-bold text-green-600 uppercase tracking-widest">Click to Test the new Bin!</span>
                    </div>
                @endif
            </div>
        @endif
    @endforeach

    <!-- MODAL STRUCTURE -->
    <div 
        x-show="openModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        x-transition.opacity
    >
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openModal = false"></div>

        {{-- Modal Content --}}
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6" @click.stop>
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Bin Registration</h2>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Bin Name</p>
                            <p class="text-lg font-medium text-gray-800" x-text="activeBin.name"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Device Key</p>
                            <p class="text-lg font-mono text-gray-800" x-text="activeBin.key"></p>
                        </div>
                    </div>

                    <div class="border-t border-b border-gray-100 py-4 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Current Weight</p>
                            <p class="text-2xl font-bold text-green-600" x-text="activeBin.weight"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Status</p>
                            <span class="px-2 py-1 rounded-md bg-green-100 text-green-700 text-sm font-semibold" x-text="activeBin.status"></span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button @click="openModal = false" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <form action="{{ route('admin.bins.update') }}" method="POST" class="flex-1">
                        @csrf
                        @method('PUT')
                            
                        <input type="hidden" name="bin_id" :value="activeBin.id">
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-lg shadow-green-200 transition">
                            Register Bin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>