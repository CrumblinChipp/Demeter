<div class="w-full">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Admin Settings</h1>
        <p class="text-gray-600">Manage campuses, buildings, bins, and system configurations.</p>
    </div>

    {{-- TOP TAB NAVIGATION --}}
    <div class="border-b border-gray-200 mb-6">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <li class="mr-2">
                <a href="{{ route('homepage', ['section' => 'admin', 'tab' => 'add-campus']) }}" 
                    class="inline-block p-4 rounded-t-lg border-b-2 {{ $activeTab == 'add-campus' ? 'text-emerald-600 border-emerald-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    Add Campus
                </a>
            </li>
            <li class="mr-2">
                <a href="{{ route('homepage', ['section' => 'admin', 'tab' => 'edit-campus']) }}" 
                    class="inline-block p-4 rounded-t-lg border-b-2 {{ $activeTab == 'edit-campus' ? 'text-emerald-600 border-emerald-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    Edit Campus
                </a>
            </li>
            <li class="mr-2">
                <a href="{{ route('homepage', ['section' => 'admin', 'tab' => 'edit-building']) }}" 
                    class="inline-block p-4 rounded-t-lg border-b-2 {{ $activeTab == 'edit-building' ? 'text-emerald-600 border-emerald-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    Edit Map Marker
                </a>
            </li>
        </ul>
    </div>

    {{-- TAB CONTENT AREA --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        @if($activeTab == 'add-campus')
            @include('admin.add-campus')
        @elseif($activeTab == 'edit-campus')
            @include('admin.edit-campus')
        @elseif($activeTab == 'edit-building')
            @include('admin.edit-map')
        @endif
    </div>
</div>