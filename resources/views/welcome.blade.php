<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ARTHEMIS - Waste Management Solution</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5));
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-10px); }
            60% { transform: translateX(-50%) translateY(-5px); }
        }

        .animate-float-8s { animation: float 8s ease-in-out infinite; }
        .animate-float-9s { animation: float 9s ease-in-out infinite 1s; }
        .animate-float-10s { animation: float 10s ease-in-out infinite 2s; }
        .animate-float-11s { animation: float 11s ease-in-out infinite 3s; }
        .animate-fadeInUp-1s { animation: fadeInUp 1s ease; }
        .animate-fadeInUp-1_2s { animation: fadeInUp 1.2s ease; }
        .animate-bounce-2s { animation: bounce 2s infinite; }

        .logo::before {
            content: '🌿';
            font-size: 28px;
        }

        .nav-link-underline::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #10b981;
            transition: width 0.3s ease;
        }

        .nav-link-underline:hover::after {
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="cloud cloud-1 hidden md:block absolute bg-white rounded-full opacity-90 z-20 w-[180px] h-[80px] top-20 left-12 animate-float-8s"></div>
    <div class="cloud cloud-2 hidden md:block absolute bg-white rounded-full opacity-90 z-20 w-[220px] h-[90px] top-40 right-24 animate-float-10s"></div>
    <div class="cloud cloud-3 hidden md:block absolute bg-white rounded-full opacity-90 z-20 w-[160px] h-[60px] bottom-40 left-5 animate-float-9s"></div>
    <div class="cloud cloud-4 hidden md:block absolute bg-white rounded-full opacity-90 z-20 w-[200px] h-[85px] bottom-40 right-40 animate-float-11s"></div>

    <nav id="navbar" class="fixed top-0 left-0 right-0 flex justify-between items-center px-6 md:px-16 py-5 bg-black/30 backdrop-blur-md z-[1000] transition-all duration-300">
        <a  class="logo flex items-center gap-3 text-white text-xl md:text-2xl font-bold tracking-widest no-underline">ARTHEMIS</a>
        
        <div class="md:hidden flex flex-col gap-1.5 p-2 cursor-pointer" onclick="toggleMenu()">
            <span class="w-7 h-0.5 bg-white rounded-full transition-all duration-300"></span>
            <span class="w-7 h-0.5 bg-white rounded-full transition-all duration-300"></span>
            <span class="w-7 h-0.5 bg-white rounded-full transition-all duration-300"></span>
        </div>

        <ul id="navLinks" class="nav-links hidden md:flex gap-10 items-center list-none md:static absolute top-[70px] left-0 right-0 flex-col md:flex-row bg-black/95 md:bg-transparent w-full md:w-auto p-10 md:p-0 transition-all duration-300">
            
            <li><a href="#features" class="nav-link-underline text-white hover:text-emerald-400 text-sm font-medium tracking-wider relative transition-colors duration-300">FEATURES</a></li>
            
            
            <li class="w-full md:w-auto flex flex-col md:flex-row gap-2 mt-4 md:mt-0">
                <a href="login" class="flex items-center justify-center gap-2 w-full px-7 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-lg transition-all duration-300 hover:-translate-y-0.5 shadow-md">
                    <span class="text-xl">👤</span> Login
                </a>
            </li>
        </ul>
    </nav>

    <section class="hero relative h-screen flex items-center text-white" style="min-height: 600px;">
        <div class="max-w-7xl mx-auto px-6 md:px-16 w-full">
            <div class="hero-content z-10 relative">
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-[72px] font-bold leading-tight mb-8 max-w-[800px] text-shadow-lg animate-fadeInUp-1s">
                    Elevate Your Environment:<br>
                    Redefining Waste for a<br>
                    <span class="text-emerald-400">Cleaner, Greener Tomorrow!</span>
                </h1>
                
                
                </a>
            </div>
        </div>
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce-2s">
            <span class="text-white text-3xl opacity-70">⌄</span>
        </div>
    </section>

    <section class="py-20 md:py-28 bg-white" id="features">
        <div class="max-w-7xl mx-auto px-6 md:px-16">
            <h2 class="text-center text-4xl md:text-5xl font-extrabold text-gray-800 mb-16">Why Choose ARTHEMIS?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-6xl mx-auto">
                
                <div class="p-8 bg-blue-50/70 rounded-2xl text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-emerald-200/50">
                    <div class="w-[70px] h-[70px] bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-full mx-auto mb-6 flex items-center justify-center text-3xl text-white">
                        📊
                    </div>
                    <h3 class="text-xl md:text-2xl font-semibold text-emerald-700 mb-4">Real-Time Tracking</h3>
                    <p class="text-gray-600 leading-relaxed">Monitor waste collection and management in real-time with our advanced dashboard system.</p>
                </div>

                <div class="p-8 bg-blue-50/70 rounded-2xl text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-emerald-200/50">
                    <div class="w-[70px] h-[70px] bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-full mx-auto mb-6 flex items-center justify-center text-3xl text-white">
                        🗺️
                    </div>
                    <h3 class="text-xl md:text-2xl font-semibold text-emerald-700 mb-4">Interactive Maps</h3>
                    <p class="text-gray-600 leading-relaxed">Visualize waste collection points and routes with our comprehensive mapping features.</p>
                </div>

                <div class="p-8 bg-blue-50/70 rounded-2xl text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-emerald-200/50">
                    <div class="w-[70px] h-[70px] bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-full mx-auto mb-6 flex items-center justify-center text-3xl text-white">
                        ♻️
                    </div>
                    <h3 class="text-xl md:text-2xl font-semibold text-emerald-700 mb-4">Sustainability Focus</h3>
                    <p class="text-gray-600 leading-relaxed">Promote recycling and proper waste segregation for a greener environment.</p>
                </div>

            </div>
        </div>
    </section>
    
    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('bg-black/95', 'py-4', 'shadow-xl');
                navbar.classList.remove('bg-black/30', 'py-5');
            } else {
                navbar.classList.remove('bg-black/95', 'py-4', 'shadow-xl');
                navbar.classList.add('bg-black/30', 'py-5');
            }
        });

        // Mobile menu toggle
        function toggleMenu() {
            const navLinks = document.getElementById('navLinks');
            
            if (navLinks.classList.contains('hidden') || navLinks.classList.contains('md:hidden')) {
                navLinks.classList.remove('hidden', 'md:hidden');
                navLinks.classList.add('flex', 'fixed', 'top-[70px]', 'left-0', 'flex-col', 'p-10', 'bg-black/95', 'shadow-2xl');
                navLinks.style.left = '0'; 
            } else {
                navLinks.classList.add('hidden', 'md:hidden');
                navLinks.classList.remove('flex', 'fixed', 'top-[70px]', 'left-0', 'flex-col', 'p-10', 'bg-black/95', 'shadow-2xl');
                navLinks.style.left = '-100%';
            }
        }
        
        document.querySelectorAll('#navLinks a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
                if (window.innerWidth < 768) {
                    const navLinks = document.getElementById('navLinks');
                    navLinks.classList.add('hidden', 'md:hidden');
                    navLinks.classList.remove('flex');
                    navLinks.style.left = '-100%'; 
                }
            });
        });
        
        function scrollToContact() {
            alert('Contact form coming soon!');
        }
    </script>
</body>
</html>