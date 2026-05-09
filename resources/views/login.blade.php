<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DEMETER - Login & Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-[#0f2027] via-[#203a43] 
    to-[#2c5364] min-h-screen flex items-center justify-center p-5 font-sans">
    <div class="bg-white/95 rounded-[20px] shadow-2xl overflow-hidden w-full max-w-[900px] 
                flex flex-col md:flex-row min-h-[550px]">
        
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-800 p-10 md:w-1/2 flex flex-col 
                    justify-center items-center text-white text-center">
            <h1 class="text-4xl md:text-5xl font-bold uppercase tracking-[3px] mb-5">Demeter</h1>
            <p class="text-lg opacity-90 leading-relaxed">
                Welcome to our platform. Join us today and experience excellence in every interaction.
            </p>
            <div class="mt-8">
                <a href="/welcome" class="text-emerald-100 hover:text-white font-semibold flex items-center gap-2 transition-colors">
                    ← Back to Home
                </a>
            </div>
        </div>

        <div class="flex-1 p-10 flex flex-col justify-center">
            
            <div class="flex gap-5 mb-8 border-b-2 border-gray-200">
                <div id="tab-login" onclick="switchTab('login')" class="pb-2 cursor-pointer font-semibold transition-all border-b-4 border-emerald-500 text-emerald-600">
                    Log in
                </div>
                <div id="tab-register" onclick="switchTab('register')" class="pb-2 cursor-pointer font-semibold transition-all 
                                                                        border-b-4 border-transparent text-gray-400 hover:text-emerald-500">
                    Register
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div href="#login" id="login-form" class="fade-in block">
                <h2 class="text-emerald-800 text-3xl font-semibold mb-2">Welcome Back</h2>
                <p class="text-gray-500 text-sm mb-8">Please enter your credentials to login</p>

                <form  action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" required placeholder="your@email.com" value="{{ old('email') }}" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg text-sm focus:outline-none 
                            focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" required placeholder="Enter password"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg text-sm focus:outline-none 
                            focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                    </div>

                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <label for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-gradient-to-br from-emerald-500 to-emerald-700 
                                            text-white font-bold rounded-lg shadow-md hover:shadow-emerald-500/30 hover:-translate-y-0.5 transition-all active:translate-y-0">
                        Login
                    </button>
                </form>

                <div class="text-center mt-6">
                    <a href="#" class="text-emerald-500 text-sm font-semibold hover:text-emerald-700 transition-colors">Forgot password?</a>
                </div>
            </div>

            <div href="#register" id="register-form" class="fade-in hidden">
                <h2 class="text-emerald-800 text-3xl font-semibold mb-2 text-wrap">Create Account</h2>
                <p class="text-gray-500 text-sm mb-6">Please fill in the information below</p>

                <form  action="{{ route('register') }}"method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase">Full Name</label>
                            <input type="text" name="name" required placeholder="John Doe" value="{{ old('name') }}"
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg text-sm focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase">SR Code</label>
                            <input type="text" name="sr_code" required placeholder="SR-XXXX" value="{{ old('sr_code') }}"
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg text-sm focus:border-emerald-500 focus:outline-none">
                        </div>
                    </div>
                    {{-- Campus Select --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="campus" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">
                            Select Campus
                        </label>
                        
                        <div class="relative flex items-center group">
                            <!-- Optional Icon for Aesthetic -->
                            <div class="absolute left-3 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>

                            <select 
                                name="campus_id" 
                                id="campus"
                                class="block w-full pl-10 pr-10 py-2.5 text-sm text-slate-700 bg-white border border-slate-200 rounded-xl 
                                    appearance-none focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 
                                    transition-all duration-200 shadow-sm cursor-pointer"
                            >
                                @foreach ($campuses as $c)
                                    <option value="{{ $c->id }}" {{ $selectedCampus == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Custom Chevron for better look than default -->
                            <div class="absolute right-3 pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase">Email</label>
                        <input type="email" name="email" required placeholder="your@email.com"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg text-sm focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="password" name="password" required placeholder="Create password"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg text-sm focus:border-emerald-500 focus:outline-none">
                        <input type="password" name="password_confirmation" required placeholder="Confirm password"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg text-sm focus:border-emerald-500 focus:outline-none">
                    </div>
                        <div class="mt-4 flex items-start gap-2 text-xs text-gray-600">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the Terms & Conditions</label>
                    </div>

                    <button type="submit" class="mt-4 w-full py-3.5 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white font-bold 
                                            rounded-lg shadow-md hover:shadow-emerald-500/30 hover:-translate-y-0.5 transition-all">
                        Create Account
                    </button>
            </div>
                <div class="text-center mt-6 text-sm text-gray-500">
                    Already have an account? <a href="#" onclick="switchTab('login')" class="text-emerald-500 font-bold hover:underline">Login here</a>
                </div>
                </form>
            </div>
        </div>
    </div>

<script>
    function switchTab(tab) {
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const loginTab = document.getElementById('tab-login');
        const registerTab = document.getElementById('tab-register');

        if (tab === 'login') {
            // URL logic: update hash without reloading
            window.location.hash = 'login';

            loginForm.classList.replace('hidden', 'block');
            registerForm.classList.replace('block', 'hidden');
            
            loginTab.classList.add('border-emerald-500', 'text-emerald-600');
            loginTab.classList.remove('border-transparent', 'text-gray-400');
            
            registerTab.classList.add('border-transparent', 'text-gray-400');
            registerTab.classList.remove('border-emerald-500', 'text-emerald-600');
        } else {
            // URL logic: update hash without reloading
            window.location.hash = 'register';

            registerForm.classList.replace('hidden', 'block');
            loginForm.classList.replace('block', 'hidden');

            registerTab.classList.add('border-emerald-500', 'text-emerald-600');
            registerTab.classList.remove('border-transparent', 'text-gray-400');
            
            loginTab.classList.add('border-transparent', 'text-gray-400');
            loginTab.classList.remove('border-emerald-500', 'text-emerald-600');
        }
    }

    // This part handles the "Refresh" or "Direct Link" logic
    window.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash.replace('#', '');
        
        if (hash === 'register') {
            switchTab('register');
        } else {
            // Default to login if no hash or #login is present
            switchTab('login');
        }
    });
</script>
</body>
</html>
