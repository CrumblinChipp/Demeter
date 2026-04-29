<div class="flex justify-between items-center">
    <h1 class="text-3xl font-bold">Campus Map</h1>
</div>
<div class="flex flex-col lg:flex-row gap-6">
    {{-- Left Side: The Interactive Map --}}
    <div class="lg: relative bg-gray-200 rounded-xl shadow-inner overflow-hidden border-4 border-white">
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
                                    $bgColor = 'bg-red-600';
                                    $label = 'Full';
                                } elseif ($maxFill >= 11) {
                                    $bgColor = 'bg-amber-500';
                                    $label = 'Filled';
                                } else {
                                    $bgColor = 'bg-green-600';
                                    $label = 'Empty';
                                }

                            } else {
                                $maxFill = null;
                                $bgColor = 'bg-gray-500';
                                $label = 'No bins';
                            }
                        @endphp
                        {{-- The Actual Marker --}}
                        <div class="absolute group cursor-pointer building-marker"
                            data-building='@json($building->smart_bins)'
                            data-name="{{ $building->name }}"
                            data-id="{{ $building->id }}"
                            style="left: {{ $building->map_x_percent }}%; 
                                top: {{ $building->map_y_percent }}%; 
                                transform: translate(-50%, -50%);">

                            @php
                                if ($bins->count()) {
                                    $maxFill = $bins->max('status');

                                    if ($maxFill >= 71) {
                                        $color = '#dc2626'; // red
                                    } elseif ($maxFill >= 11) {
                                        $color = '#f59e0b'; // amber
                                    } else {
                                        $color = '#16a34a'; // green
                                    }

                                } else {
                                    $maxFill = null;
                                    $color = '#6b7280'; // gray
                                }

                                $radius = 16;
                                $circumference = 2 * pi() * $radius;
                                $offset = $maxFill !== null 
                                    ? $circumference - ($maxFill / 100) * $circumference 
                                    : $circumference;
                            @endphp

                            <div class="relative w-12 h-12">
                                <svg class="w-full h-full transform -rotate-90">

                                    <circle
                                        cx="24"
                                        cy="24"
                                        r="{{ $radius }}"
                                        stroke="#e5e7eb"
                                        stroke-width="4"
                                        fill="transparent"
                                    />

                                    <circle
                                        cx="24"
                                        cy="24"
                                        r="{{ $radius }}"
                                        stroke="{{ $color }}"
                                        stroke-width="4"
                                        fill="transparent"
                                        stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $offset }}"
                                        stroke-linecap="round"
                                    />
                                </svg>

                                <div class="absolute inset-0 flex items-center justify-center text-xs font-bold">
                                    {{ is_null($maxFill) ? '?' : $maxFill . '%' }}
                                </div>
                            </div>
                        </div>

                        <div id="bin-tooltip"
                            class="hidden fixed z-50 bg-white border border-gray-200 rounded-lg shadow-lg p-3 text-xs pointer-events-none min-w-[160px]">
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
</div>

<script>
window.addEventListener("load", () => {
    const tooltip = document.getElementById("bin-tooltip");

    document.querySelectorAll(".building-marker").forEach(marker => {

        marker.addEventListener("mouseenter", () => {
            const bins = JSON.parse(marker.dataset.building);
            const name = marker.dataset.name;

            let html = `<div class="font-bold text-gray-800 mb-2">${name}</div>`;

            if (bins.length === 0) {
                html += `<div class="text-gray-500 italic">No bin installed</div>`;
            } else {
                html += `
                    <table class="w-full text-xs">
                        <tbody>
                            ${bins.map(bin => {

                                let color = 'text-green-600';
                                if (bin.status >= 71) color = 'text-red-600';
                                else if (bin.status >= 11) color = 'text-amber-500';

                                return `
                                    <tr>
                                        <td class="pr-4">${bin.waste_type}</td>
                                        <td class="text-right font-semibold ${color}">
                                            ${bin.status}%
                                        </td>
                                    </tr>
                                `;
                            }).join("")}
                        </tbody>
                    </table>
                `;
            }

            tooltip.innerHTML = html;
            tooltip.classList.remove("hidden");
        });

        marker.addEventListener("mousemove", (e) => {
            tooltip.style.left = (e.clientX + 12) + "px";
            tooltip.style.top = (e.clientY + 12) + "px";
        });

        marker.addEventListener("mouseleave", () => {
            tooltip.classList.add("hidden");
        });

        marker.addEventListener("click", () => {
            const buildingId = marker.dataset.id;

            // Redirect to bin section with filter
            window.location.href = `/homepage?section=bin&building=${buildingId}`;
        });

    });
    

});
</script>
