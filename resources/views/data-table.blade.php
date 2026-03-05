<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Waste Records</h1>
        <div class="flex justify-between items-center mt-4 mb-4">
            <button id="openWasteModal"
            class="bg-green-600 text-white px-5 py-2 rounded-lg shadow hover:bg-green-700 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Waste Entry
            </button>
        </div>

        @include('waste-modal')
    </div>

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
                @foreach ($wastes as $waste)
                @php
                    // Using the _kg columns we found in your migration earlier
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
                @endforeach
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