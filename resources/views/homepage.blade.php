<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Demeter</title>

<meta name="csrf-token" content="{{ csrf_token() }}">


<script src="https://cdn.tailwindcss.com"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    .sidebar {
        background: #142d1b;
        color: #cfe4d5;
        min-height: 100vh;
    }
    .card {
        background: white;
        border-radius: 6px;
        box-shadow: 0 0 0 1px rgba(0,0,0,0.03);
    }

    
</style>
<body>
    <aside class=" bg-gradient-to-br from-emerald-600 to-emerald-700
      text-white flex items-center
        fixed top-0 left-0 z-50 w-full h-14
        md:flex-col md:items-center md:justify-start
        md:w-72 md:h-screen md:fixed
        md:pt-0
        ">

    {{-- Logo --}}
    <div class=" mt-4 flex items-center gap-3 md:mb-8">
        <div class="h-8 w-8 rounded-full bg-green-700 
        flex items-center justify-center text-white font-bold">D</div>

        <div class="hidden md:block">
            <div class="text-white font-semibold">DEMETER</div>
        </div>
    </div>
    {{-- Navigation --}}
    <a href="{{ route('homepage', ['section' => 'dashboard']) }}"
        data-nav="dashboard"
    class="nav-item w-full flex flex-col md:flex-row items-center gap-1 px-3 py-2 rounded-md transition
    {{ $currentSection == 'dashboard' ? 'bg-green-900/50 text-white' : 
        'text-gray-200 hover:bg-green-900/30' }}">
        <span>🏠︎</span>
        <span class="hidden md:inline text-2xl">Dashboard</span>
    </a>

    {{-- Maps --}}
    <a href="{{ route('homepage', ['section' => 'map']) }}"
        data-nav="maps"
    class="nav-item w-full flex flex-col md:flex-row items-center gap-1 px-3 py-2 rounded-md transition
    {{ $currentSection == 'map' ? 'bg-green-900/50 text-white' : 
        'text-gray-200 hover:bg-green-900/30' }}">
        <span>⚲</span>
        <span class="hidden md:inline text-2xl">Map</span>
    </a>

    {{-- Data --}}
    <a href="{{ route('homepage', ['section' => 'data']) }}"
        data-nav="data"
    class="nav-item w-full flex flex-col md:flex-row items-center gap-1 px-3 py-2 rounded-md transition
    {{ $currentSection == 'data' ? 'bg-green-900/50 text-white' : 
        'text-gray-200 hover:bg-green-900/30' }}">
        <span>𝄜</span>
        <span class="hidden md:inline text-2xl">Data</span>
    </a>

    {{-- Admin Setting --}}
    <a href="{{ route('homepage', ['section' => 'admin']) }}"
        data-nav="admin"
    class="nav-item w-full flex flex-col md:flex-row items-center gap-1 px-3 py-2 rounded-md transition
    {{ $currentSection == 'admin' ? 'bg-green-900/50 text-white' : 
        'text-gray-200 hover:bg-green-900/30' }}">
        <span>⚙</span>
        <span class="hidden md:inline text-2xl">Admin Settings</span>
    </a>
    <form action="{{ route('logout') }}" method="POST" class="w-full md:w-auto">
        @csrf
        <button type="submit" class="mt-4 flex items-center justify-center gap-0
                w-full px-7 py-0 md:py-3 bg-red-500 hover:bg-red-600 text-white font-semibold
                text-sm rounded-lg transition-all duration-300 hover:-translate-y-0.5 shadow-md">
            <span>⏻</span>
            <span class="hidden md:inline">Logout</span>
        </button>
    </form>
</aside>

    <main class="transition-all duration-300 pt-14 md:pt-0 md:ml-72 p-12 min-h-screen bg-gray-50">
        
        @if($currentSection == 'dashboard')
            <section data-section="dashboard" class="content-section">
                @include('dashboard') 
            </section>

        @elseif($currentSection == 'map')
            <section data-section="map" class="content-section">
                @include('maps')
            </section>

        @elseif($currentSection == 'data')
            <section data-section="data" class="content-section">
                @include('data-table')
            </section>

        @elseif($currentSection == 'admin')
            <section data-section="admin" class="content-section">
                @include('admin-setting')
            </section>
        @endif

    </main>

    <script>
        function switchSection(sectionId) {
            // 1. Update the URL hash
            window.location.hash = sectionId;

            // 2. Hide all sections and show the active one
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.add('hidden');
            });
            const activeSection = document.querySelector(`[data-section="${sectionId}"]`);
            if (activeSection) activeSection.classList.remove('hidden');

            // 3. Update Sidebar Styling (Visual Feedback)
            document.querySelectorAll('.nav-item').forEach(nav => {
                if (nav.getAttribute('data-nav') === sectionId) {
                    nav.classList.add('bg-green-900/50', 'text-white'); // Active styles
                    nav.classList.remove('text-gray-200');
                } else {
                    nav.classList.remove('bg-green-900/50', 'text-white');
                    nav.classList.add('text-gray-200');
                }
            });
        }

        // Handle browser Back/Forward buttons
        window.addEventListener('hashchange', () => {
            const currentHash = window.location.hash.replace('#', '') || 'dashboard';
            switchSection(currentHash);
        });
    function submitFilterForm() {
        document.getElementById('globalFilterForm').submit();
    }
    </script>
</body>

