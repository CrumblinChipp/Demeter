<div class="space-y-6">

    {{-- Header Section --}}
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Campus Map</h1>
            <p class="text-sm text-gray-500 mt-1">Click a building marker to view its bins</p>
        </div>

        {{-- Campus Switcher --}}
        <div class="flex items-center gap-3 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-2.5">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <select onchange="window.location.href=this.value"
                    class="bg-transparent text-gray-800 text-sm font-medium focus:outline-none cursor-pointer pr-6 appearance-none">
                @foreach($campuses as $c)
                    <option value="{{ route('homepage', ['section' => 'map', 'campus' => $c->id]) }}"
                            {{ $campus && $campus->id === $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap items-center gap-6 bg-white/80 backdrop-blur-sm rounded-xl border border-gray-100 shadow-sm px-5 py-3">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</span>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-green-500 shadow-sm shadow-green-200"></span>
            <span class="text-xs text-gray-600">Empty (0-10%)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm shadow-amber-200"></span>
            <span class="text-xs text-gray-600">Filling (11-70%)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500 shadow-sm shadow-red-200"></span>
            <span class="text-xs text-gray-600">Full (71-100%)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-gray-400"></span>
            <span class="text-xs text-gray-600">No bins</span>
        </div>
    </div>

    {{-- Map Container --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        @if($campus && $campus->map)
            <div class="relative inline-block w-full">
                <img src="{{ asset('storage/' . $campus->map) }}" 
                    alt="{{ $campus->name }} Map"
                    class="w-full h-auto block">

                {{-- The Markers Layer --}}
                <div class="absolute inset-0">
                    @foreach($campus->buildings as $building)
                        @php
                            $bins = $building->smart_bins;

                            if ($bins->count()) {
                                $maxFill = $bins->max('status');
                                if ($maxFill >= 71) {
                                    $color = '#dc2626';
                                    $glowColor = 'rgba(220, 38, 38, 0.4)';
                                    $label = 'Full';
                                } elseif ($maxFill >= 11) {
                                    $color = '#f59e0b';
                                    $glowColor = 'rgba(245, 158, 11, 0.4)';
                                    $label = 'Filling';
                                } else {
                                    $color = '#16a34a';
                                    $glowColor = 'rgba(22, 163, 74, 0.4)';
                                    $label = 'Empty';
                                }
                            } else {
                                $maxFill = null;
                                $color = '#6b7280';
                                $glowColor = 'rgba(107, 114, 128, 0.3)';
                                $label = 'No bins';
                            }

                            $radius = 16;
                            $circumference = 2 * pi() * $radius;
                            $offset = $maxFill !== null 
                                ? $circumference - ($maxFill / 100) * $circumference 
                                : $circumference;
                        @endphp

                        {{-- Building Marker --}}
                        <div class="absolute group cursor-pointer building-marker transition-transform duration-200 hover:scale-125"
                            data-building='@json($building->smart_bins)'
                            data-name="{{ $building->name }}"
                            data-id="{{ $building->id }}"
                            style="left: {{ $building->map_x_percent }}%; 
                                top: {{ $building->map_y_percent }}%; 
                                transform: translate(-50%, -50%);
                                filter: drop-shadow(0 0 6px {{ $glowColor }});">

                            <div class="relative w-12 h-12">
                                {{-- Progress Ring --}}
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="24" cy="24" r="{{ $radius }}"
                                        stroke="#e5e7eb" stroke-width="4" fill="white" fill-opacity="0.9"/>
                                    <circle cx="24" cy="24" r="{{ $radius }}"
                                        stroke="{{ $color }}" stroke-width="4" fill="transparent"
                                        stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $offset }}"
                                        stroke-linecap="round"
                                        class="transition-all duration-700"/>
                                </svg>

                                {{-- Percentage Label --}}
                                <div class="absolute inset-0 flex items-center justify-center text-[10px] font-bold"
                                     style="color: {{ $color }}">
                                    {{ is_null($maxFill) ? '?' : $maxFill . '%' }}
                                </div>
                            </div>

                            {{-- Building Name Label (shown on hover) --}}
                            <div class="absolute -bottom-5 left-1/2 -translate-x-1/2 whitespace-nowrap
                                        bg-gray-900/80 text-white text-[10px] font-medium px-2 py-0.5 rounded-md
                                        opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                                {{ $building->name }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="h-96 flex flex-col items-center justify-center text-gray-400 bg-gradient-to-br from-gray-50 to-gray-100">
                <svg class="w-20 h-20 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/>
                </svg>
                <p class="text-lg font-medium text-gray-400">No map uploaded</p>
                <p class="text-sm text-gray-300 mt-1">Upload a campus map in Admin Settings</p>
            </div>
        @endif
    </div>

    {{-- Tooltip (shared, positioned on hover) --}}
    <div id="bin-tooltip"
         class="hidden fixed z-50 bg-white/95 backdrop-blur-sm border border-gray-200 rounded-xl shadow-xl p-4 text-xs pointer-events-none min-w-[180px]">
    </div>
</div>

<script>
window.addEventListener("load", () => {
    const tooltip = document.getElementById("bin-tooltip");

    document.querySelectorAll(".building-marker").forEach(marker => {

        marker.addEventListener("mouseenter", () => {
            const bins = JSON.parse(marker.dataset.building);
            const name = marker.dataset.name;

            let html = `<div class="font-bold text-gray-900 text-sm mb-2 pb-2 border-b border-gray-100">${name}</div>`;

            if (bins.length === 0) {
                html += `<div class="text-gray-400 italic flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    No bins installed
                </div>`;
            } else {
                html += `<div class="space-y-1.5">`;
                bins.forEach(bin => {
                    let color = 'text-green-600 bg-green-50';
                    let dot = 'bg-green-500';
                    if (bin.status >= 71) { color = 'text-red-600 bg-red-50'; dot = 'bg-red-500'; }
                    else if (bin.status >= 11) { color = 'text-amber-600 bg-amber-50'; dot = 'bg-amber-500'; }

                    html += `
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-1.5 h-1.5 rounded-full shrink-0 ${dot}"></span>
                                <div class="min-w-0">
                                    <span class="text-gray-700 font-medium text-[11px] block truncate">${bin.name}</span>
                                    <span class="text-gray-400 capitalize text-[10px]">${bin.waste_type}</span>
                                </div>
                            </div>
                            <span class="font-bold ${color} px-1.5 py-0.5 rounded text-[11px] shrink-0">${bin.status}%</span>
                        </div>
                    `;
                });
                html += `</div>`;
            }

            tooltip.innerHTML = html;
            tooltip.classList.remove("hidden");
        });

        marker.addEventListener("mousemove", (e) => {
            tooltip.style.left = (e.clientX + 16) + "px";
            tooltip.style.top = (e.clientY + 16) + "px";
        });

        marker.addEventListener("mouseleave", () => {
            tooltip.classList.add("hidden");
        });

        marker.addEventListener("click", () => {
            const buildingId = marker.dataset.id;
            window.location.href = `/homepage?section=bin&campus={{ $campus->id }}&building=${buildingId}`;
        });
    });
});
</script>
