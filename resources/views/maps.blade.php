@php
    // 1. Define the palette at the TOP of the file
    $colorPalette = [
        'bg-red-600', 'bg-blue-600', 'bg-emerald-600', 'bg-amber-500', 
        'bg-purple-600', 'bg-pink-600', 'bg-indigo-600', 'bg-cyan-600',
        'bg-orange-500', 'bg-lime-500', 'bg-rose-600'
    ];
    
    // Safety check for the count() function
    $paletteCount = count($colorPalette);
@endphp

<div class="flex flex-col lg:flex-row gap-6">
    {{-- Left Side: The Interactive Map --}}
    <div class="lg:w-3/4 relative bg-gray-200 rounded-xl shadow-inner overflow-hidden border-4 border-white">
        @if($campus && $campus->map)
            <div class="relative inline-block w-full">
                <img src="{{ asset('storage/' . $campus->map) }}" 
                    alt="{{ $campus->name }} Map" 
                    class="w-full h-auto block">

                {{-- The Markers Layer --}}
                <div class="absolute inset-0">
                    @foreach($campus->buildings as $building)
                        @php
                            // Calculate the color once per building
                            $colorClass = $colorPalette[$building->id % $paletteCount];
                        @endphp

                        <div class="absolute group cursor-help"
                            style="left: {{ $building->map_x_percent }}%; 
                                    top: {{ $building->map_y_percent }}%; 
                                    transform: translate(-50%, -50%);">
                            
                            {{-- 1. The Pulse Effect --}}
                            <div class="absolute inset-0 rounded-full {{ $colorClass }} animate-ping opacity-25"></div>
                            
                            {{-- 2. The Actual Marker --}}
                            <div class="relative w-4 h-4 {{ $colorClass }} border-2 border-white rounded-full shadow-md group-hover:scale-125 transition-transform"></div>

                            {{-- 3. Tooltip/Label --}}
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity z-50">
                                <div class="bg-gray-900 text-white text-xs py-1 px-3 rounded-lg whitespace-nowrap shadow-xl">
                                    {{ $building->name }}
                                </div>
                                {{-- Tooltip Arrow --}}
                                <div class="w-2 h-2 bg-gray-900 rotate-45 mx-auto -mt-1"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="h-96 flex flex-col items-center justify-center text-gray-400">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 20l-5.447-2.724A2 2 0 013 15.382V6.418a2 2 0 011.106-1.789L9 2m0 18l6-3m-6 3V2m6 15l4.553 2.276A2 2 0 0021 17.418V8.418a2 2 0 00-1.106-1.789L15 4m0 13V4m0 0L9 2"></path></svg>
                <p>No map image available for {{ $campus->name ?? 'this campus' }}.</p>
            </div>
        @endif
    </div>

    {{-- Right Side: Directory --}}
    <div class="lg:w-1/4">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 border-b pb-3 mb-4">Building Directory</h3>
            <ul class="space-y-2">
                @forelse($campus->buildings as $building)
                    @php
                        // 3. Use the pre-calculated count and the correct variable name
                        $colorClass = $colorPalette[$building->id % $paletteCount];
                    @endphp
                    
                    <li class="flex items-center text-sm text-gray-600 group">
                        {{-- The Dot --}}
                        <span class="w-3 h-3 {{ $colorClass }} rounded-full mr-2 border border-white shadow-sm transition-transform group-hover:scale-125"></span>
                        {{ $building->name }}
                    </li>
                @empty
                    <li class="text-sm text-gray-400 italic">No buildings marked yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>