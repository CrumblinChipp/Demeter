<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($smart_bins as $bin)
        @if($bin->is_registered == FALSE)
            <div class="bg-white rounded-xl shadow-sm border border-red-300 border-l-4 border-l-red-500 p-5 hover:shadow-md transition-all">

                {{-- Header --}}
                <div class="mb-4">
                    <h3 class="font-semibold text-gray-800 truncate">
                         Device Key: {{ $bin->device_key}}
                    </h3>

                    <p class="text-xs text-gray-500 mt-1">
                        Bin Name:
                        <span class="font-medium text-gray-700">
                            {{ $bin->name }} 
                        </span>
                    </p>
                </div>

                {{-- Bin Information --}}
                <div class="space-y-2 text-sm">

                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Building</span>
                        <span class="font-medium text-gray-700">
                            {{ $bin->building->name ?? 'Unassigned' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Waste Type</span>
                        <span class="font-medium text-gray-700 capitalize">
                            {{ $bin->waste_type ?? 'Unassigned' }}
                        </span>
                    </div>

                </div>

            </div>
        @endif
    @endforeach
</div>