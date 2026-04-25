<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Smart Bins</h1>
    </div>

    {{-- TABLE CONTAINER --}}
<div class="overflow-x-auto bg-white shadow-sm border border-gray-200 rounded-lg">
    <table class="table w-full text-left">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="p-4">Bin Name</th>
                <th class="p-4 text-center">Status</th>
                <th class="p-4 text-center font-bold">Current Weight (kg)</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @foreach ($smart_bins as $bin)
            @php
                if ($bin->status >= 71) {
                    $colorClass = 'text-red-700';
                    $label = 'Full';
                } elseif ($bin->status >= 11) {
                    $colorClass = 'text-amber-600';
                    $label = 'Filled';
                } else {
                    $colorClass = 'text-green-700';
                    $label = 'Empty';
                }
                $strokeDash = $bin->status . ', 100';
            @endphp

            <tr class="hover:bg-gray-50 transition">
                {{-- Bin Name --}}
                <td class="p-4 font-medium whitespace-nowrap">
                    {{ $bin->name }}
                </td>

                {{-- Status --}}
                <td class="p-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        {{-- Smaller, cleaner circle --}}
                        <div class="relative w-8 h-8">
                            <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                                <path class="stroke-current text-gray-200"
                                      stroke-width="4"
                                      fill="none"
                                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                
                                <path class="stroke-current {{ $colorClass }}"
                                      stroke-width="4"
                                      stroke-dasharray="{{ $strokeDash }}"
                                      stroke-linecap="round"
                                      fill="none"
                                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium {{ $colorClass }}">
                            {{ $bin->status }}%
                        </span>
                    </div>
                </td>

                {{-- Weight --}}
                <td class="p-4 text-center font-bold bg-gray-50">
                    {{ number_format($bin->current_weight, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
