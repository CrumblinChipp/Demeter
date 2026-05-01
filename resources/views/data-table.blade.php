<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Waste Records</h1>
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('homepage') }}" 
          class="flex flex-wrap items-end gap-4 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <input type="hidden" name="section" value="data">
        <input type="hidden" name="campus" value="{{ $selectedCampus }}">

        {{-- Building Filter --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Building</label>
            <select name="building"
                class="bg-gray-50 text-gray-900 text-sm rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 p-2 min-w-[160px]">
                <option value="">All Buildings</option>
                @foreach ($campus->buildings as $b)
                    <option value="{{ $b->id }}" @selected(request('building') == $b->id)>
                        {{ $b->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Date From --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="bg-gray-50 text-gray-900 text-sm rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 p-2">
        </div>

        {{-- Date To --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="bg-gray-50 text-gray-900 text-sm rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 p-2">
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-2">
            <button type="submit" 
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all">
                Apply
            </button>
            <a href="{{ route('homepage', ['section' => 'data', 'campus' => $selectedCampus]) }}" 
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-all">
                Clear
            </a>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="overflow-x-auto bg-white shadow-sm border border-gray-200 rounded-lg">
        <table class="table w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="p-4">Date</th>
                    <th class="p-4">Building</th>
                    <th class="p-4 text-center text-emerald-700">Residual</th>
                    <th class="p-4 text-center text-blue-700">Recyclable</th>
                    <th class="p-4 text-center text-green-700">Bio</th>
                    <th class="p-4 text-center text-red-700">Infectious</th>
                    <th class="p-4 text-center font-bold">Total (kg)</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($wastes as $waste)

                @php
                    $totalWeight = $waste->residual_kg + $waste->recyclable_kg + 
                                $waste->biodegradable_kg + $waste->infectious_kg;
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($waste->date)->format('M d, Y') }}</td>
                    <td class="p-4 font-medium">{{ $waste->building->name }}</td>

                    <td class="p-4 text-center">{{ number_format($waste->residual_kg, 2) }}</td>
                    <td class="p-4 text-center">{{ number_format($waste->recyclable_kg, 2) }}</td>
                    <td class="p-4 text-center">{{ number_format($waste->biodegradable_kg, 2) }}</td>
                    <td class="p-4 text-center">{{ number_format($waste->infectious_kg, 2) }}</td>
                    <td class="p-4 text-center font-bold bg-gray-50">{{ number_format($totalWeight, 2) }}</td>

                    <td class="p-4 text-right">
                        <form method="POST" action="{{ route('waste.destroy', $waste->id) }}" onsubmit="return confirm('Permanently delete this entry?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 transition p-2">
                                🗑
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-8 text-center text-gray-400">No waste entries found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION & ROWS PER PAGE --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 py-4">
        <div class="text-sm text-gray-600">
            <form method="GET" action="{{ route('homepage') }}" class="flex items-center gap-2">
                <input type="hidden" name="section" value="data">
                <input type="hidden" name="campus" value="{{ $selectedCampus }}">
                <span>Rows per page:</span>
                <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded px-2 py-1 focus:ring-emerald-500">
                    <option value="20" @selected(request('per_page', 20) == 20)>20</option>
                    <option value="50" @selected(request('per_page') == 50)>50</option>
                    <option value="100" @selected(request('per_page') == 100)>100</option>
                </select>
            </form>
        </div>

        <div>
            {{ $wastes->links() }}
        </div>
    </div>
</div>
