<div class="w-full">

    {{-- TOP TAB NAVIGATION --}}
    <div class="border-b border-gray-200 mb-6">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">

            <li class="mr-2">
                <a href="{{ route('homepage', ['section' => 'admin', 'tab' => 'add-bin', 'bin' => 'unmatched']) }}" 
                    class="inline-block p-4 rounded-t-lg border-b-2 {{ $binTab == 'unmatched' ? 'text-emerald-600 border-emerald-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    Unmatched Bins
                </a>
            </li>

            <li class="mr-2">
                <a href="{{ route('homepage', ['section' => 'admin', 'tab' => 'add-bin', 'bin' => 'register']) }}" 
                    class="inline-block p-4 rounded-t-lg border-b-2 {{ $binTab == 'register' ? 'text-emerald-600 border-emerald-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    Register Bin
                </a>
            </li>

        </ul>
    </div>

    {{-- TAB CONTENT AREA --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">

        @if($binTab == 'unmatched')
            @include('admin.unmatched')

        @elseif($binTab == 'register')
            @include('admin.register-bin')

        @endif

    </div>

</div>