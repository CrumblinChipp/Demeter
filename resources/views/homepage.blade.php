<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Demeter</title>

<meta name="csrf-token" content="{{ csrf_token() }}">

@vite(['resources/css/app.css', 'resources/js/app.js'])

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
    <aside class="bg-gradient-to-b from-emerald-700 to-emerald-900
        text-white flex items-center
        fixed top-0 left-0 z-50 w-full h-14
        md:flex-col md:items-stretch md:justify-between
        md:w-64 md:h-screen md:fixed">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-4 md:border-b md:border-white/10 md:mb-2">
            <div class="h-9 w-9 rounded-lg bg-white/15
                flex items-center justify-center text-white font-bold text-lg">D</div>
            <div class="hidden md:block">
                <div class="text-white font-bold tracking-wide">DEMETER</div>
                <div class="text-emerald-300/70 text-[10px] uppercase tracking-widest">Waste Management</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex md:flex-col md:flex-1 md:px-3 md:space-y-1">

            {{-- Dashboard --}}
            <a href="{{ route('homepage', ['section' => 'dashboard', 'campus' => $selectedCampus]) }}" data-nav="dashboard"
                class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition
                {{ $currentSection == 'dashboard' ? 'bg-white/15 text-white' : 'text-emerald-100/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                </svg>
                <span class="hidden md:inline">Dashboard</span>
            </a>

            {{-- Map --}}
            <a href="{{ route('homepage', ['section' => 'map', 'campus' => $selectedCampus]) }}" data-nav="maps"
                class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition
                {{ $currentSection == 'map' ? 'bg-white/15 text-white' : 'text-emerald-100/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-13.757l3.012-.95A.75.75 0 0120.25 3.81v12.382a.75.75 0 01-.515.714l-3.72 1.178a.75.75 0 01-.45 0l-3.576-1.131a.75.75 0 00-.45 0l-3.72 1.178a.75.75 0 01-1.069-.682V5.067a.75.75 0 01.515-.714l3.72-1.178a.75.75 0 01.45 0l3.576 1.131a.75.75 0 00.45 0z"/>
                </svg>
                <span class="hidden md:inline">Map</span>
            </a>

            {{-- Bins --}}
            <a href="{{ route('homepage', ['section' => 'bin', 'campus' => $selectedCampus]) }}" data-nav="bin"
                class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition
                {{ $currentSection == 'bin' ? 'bg-white/15 text-white' : 'text-emerald-100/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
                <span class="hidden md:inline">Bins</span>
            </a>

            {{-- Data --}}
            <a href="{{ route('homepage', ['section' => 'data', 'campus' => $selectedCampus]) }}" data-nav="data"
                class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition
                {{ $currentSection == 'data' ? 'bg-white/15 text-white' : 'text-emerald-100/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 0v1.5c0 .621-.504 1.125-1.125 1.125"/>
                </svg>
                <span class="hidden md:inline">Data</span>
            </a>

            {{-- Admin Settings (only visible to admins) --}}
            @auth
            @if(auth()->user()->is_admin)
            <a href="{{ route('homepage', ['section' => 'admin', 'campus' => $selectedCampus]) }}" data-nav="admin"
                class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition
                {{ $currentSection == 'admin' ? 'bg-white/15 text-white' : 'text-emerald-100/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="hidden md:inline">Admin Settings</span>
            </a>
            @endif
            @endauth
        </nav>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="md:px-3 md:pb-4 md:border-t md:border-white/10 md:pt-3">
            @csrf
            <button type="submit" class="flex items-center justify-center gap-2
                    w-full px-4 py-2.5 bg-red-500/20 hover:bg-red-500/40 text-red-200 hover:text-white font-medium
                    text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                </svg>
                <span class="hidden md:inline">Logout</span>
            </button>
        </form>
    </aside>

    <main class="transition-all duration-300 pt-16 md:pt-0 md:ml-64 p-8 min-h-screen bg-gray-50">
        
        @if($currentSection == 'dashboard')
            <section data-section="dashboard" class="content-section">
                @include('dashboard') 
            </section>

        @elseif($currentSection == 'map')
            <section data-section="map" class="content-section">
                @include('maps')
            </section>

        @elseif($currentSection == 'bin')
            <section data-section="bin" class="content-section">
                @include('bin')
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

    {{-- AI Chat Widget --}}
    <div id="ai-chat-toggle"
         onclick="toggleChat()"
         class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full shadow-lg cursor-pointer flex items-center justify-center transition-all hover:scale-110">
        <svg id="chat-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 20.25V5.625A2.625 2.625 0 016.375 3h11.25A2.625 2.625 0 0120.25 5.625v8.25a2.625 2.625 0 01-2.625 2.625H7.5L3.75 20.25z"/>
        </svg>
        <svg id="chat-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </div>

    <div id="ai-chat-panel"
         class="fixed bottom-24 right-6 z-50 w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden hidden"
         style="max-height: 500px;">

        {{-- Chat Header --}}
        <div class="bg-emerald-600 text-white px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">D</div>
                <div>
                    <div class="font-semibold text-sm">Demeter AI</div>
                    <div class="text-emerald-200 text-xs">Ask anything about waste data</div>
                </div>
            </div>
            <button onclick="clearAiChat()" title="Clear chat history"
                    class="p-1.5 rounded-lg hover:bg-white/20 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
            </button>
        </div>

        {{-- Chat Messages --}}
        <div id="ai-chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3" style="min-height: 300px;">
            <div class="flex gap-2">
                <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold shrink-0">D</div>
                <div class="bg-gray-100 text-gray-700 text-sm px-3 py-2 rounded-xl rounded-tl-none max-w-[80%]">
                    Hi! I'm Demeter AI. Ask me anything about your campus waste data — like "Which building produces the most waste?" or "How many bins are full right now?"
                </div>
            </div>
        </div>

        {{-- Chat Input --}}
        <div class="border-t border-gray-100 p-3">
            <form id="ai-chat-form" onsubmit="sendAiMessage(event)" class="flex gap-2">
                <input id="ai-chat-input" type="text" placeholder="Ask a question..."
                       class="flex-1 text-sm bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                       autocomplete="off">
                <button type="submit" id="ai-send-btn"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
                    Send
                </button>
            </form>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-5 right-24 z-[100] flex flex-col gap-3"></div>

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
                    nav.classList.add('bg-white/15', 'text-white');
                    nav.classList.remove('text-emerald-100/70');
                } else {
                    nav.classList.remove('bg-white/15', 'text-white');
                    nav.classList.add('text-emerald-100/70');
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

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        
        // Create toast element
        const toast = document.createElement('div');
        
        // Set colors based on type
        const bgColor = type === 'success' ? 'bg-emerald-600' : 'bg-red-600';
        const icon = type === 'success' 
            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';

        toast.className = `${bgColor} text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-300 translate-y-10 opacity-0`;
        
        toast.innerHTML = `
            <span class="p-1 bg-white/20 rounded-full">${icon}</span>
            <span class="font-medium">${message}</span>
        `;

        container.appendChild(toast);

        // Animate In
        setTimeout(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
        }, 10);

        // Auto-remove after 4 seconds
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-10'); // Slide out effect
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // --- AI Chat Functions ---
    function toggleChat() {
        const panel = document.getElementById('ai-chat-panel');
        const iconOpen = document.getElementById('chat-icon-open');
        const iconClose = document.getElementById('chat-icon-close');

        panel.classList.toggle('hidden');
        iconOpen.classList.toggle('hidden');
        iconClose.classList.toggle('hidden');
    }

    function addChatMessage(content, isUser = false) {
        const container = document.getElementById('ai-chat-messages');

        const wrapper = document.createElement('div');
        wrapper.className = isUser ? 'flex gap-2 justify-end' : 'flex gap-2';

        if (isUser) {
            wrapper.innerHTML = `<div class="bg-emerald-600 text-white text-sm px-3 py-2 rounded-xl rounded-tr-none max-w-[80%]">${content}</div>`;
        } else {
            wrapper.innerHTML = `<div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold shrink-0">D</div><div class="bg-gray-100 text-gray-700 text-sm px-3 py-2 rounded-xl rounded-tl-none max-w-[80%] whitespace-pre-line">${content}</div>`;
        }

        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
        return wrapper;
    }

    async function sendAiMessage(e) {
        e.preventDefault();

        const input = document.getElementById('ai-chat-input');
        const btn = document.getElementById('ai-send-btn');
        const question = input.value.trim();

        if (!question) return;

        // Show user message
        addChatMessage(question, true);
        input.value = '';

        // Show typing indicator
        const typing = addChatMessage('Thinking...');
        btn.disabled = true;
        btn.textContent = '...';

        try {
            // Build headers — CSRF token is optional since /api/* is excluded
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            };
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) {
                headers['X-CSRF-TOKEN'] = csrfMeta.content;
            }

            console.log('Sending AI request...');

            const res = await fetch('/api/ai/ask', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ question }),
            });

            console.log('AI response status:', res.status);

            const data = await res.json();
            typing.remove();

            if (data.status === 'success') {
                addChatMessage(data.answer);
            } else {
                addChatMessage('Sorry, something went wrong. Please try again.');
                console.error('AI error response:', data);
            }
        } catch (err) {
            typing.remove();
            addChatMessage('Sorry, I could not reach the server. Please try again.');
            console.error('AI fetch error:', err);
        }

        btn.disabled = false;
        btn.textContent = 'Send';
    }

    // Clear the chat history (both server session and UI)
    async function clearAiChat() {
        try {
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            };
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) headers['X-CSRF-TOKEN'] = csrfMeta.content;

            await fetch('/api/ai/ask', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ action: 'clear' }),
            });
        } catch (err) {
            console.error('Failed to clear AI session:', err);
        }

        // Reset the chat UI
        const container = document.getElementById('ai-chat-messages');
        container.innerHTML = '';

        // Re-add the welcome message
        addChatMessage("Chat cleared! Ask me anything about your campus waste data.");
    }
    </script>
</body>

