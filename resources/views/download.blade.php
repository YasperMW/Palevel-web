<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PaLevel - Find Your Perfect Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Anta&display=swap');

        .anta-font {
            font-family: 'Anta', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #07746B 0%, #0DDAC9 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .logo-container {
            animation: fadeInScale 1.5s ease-out;
        }

        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .slide-up {
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="gradient-bg min-h-screen overflow-x-hidden">
    <div id="app" class="min-h-screen flex flex-col">
        <!-- Navigation Bar -->
        <nav class="relative z-50 bg-white/10 backdrop-blur-md border-b border-white/20">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg overflow-hidden">
                            <img src="{{ asset('images/PaLevel Logo-White.png') }}" alt="PaLevel"
                                class="w-full h-full object-contain p-1">
                        </div>
                        <span class="text-white font-bold text-xl anta-font">PaLevel</span>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('landing') }}#features"
                            class="text-white/90 hover:text-white font-medium transition-colors duration-200 relative group">
                            Features
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-white transition-all duration-200 group-hover:w-full"></span>
                        </a>
                        <a href="{{ route('landing') }}#about"
                            class="text-white/90 hover:text-white font-medium transition-colors duration-200 relative group">
                            About
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-white transition-all duration-200 group-hover:w-full"></span>
                        </a>
                        <a href="{{ route('landing') }}#contact"
                            class="text-white/90 hover:text-white font-medium transition-colors duration-200 relative group">
                            Contact
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-white transition-all duration-200 group-hover:w-full"></span>
                        </a>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="{{ route('login') }}"
                            class="text-white/90 hover:text-white font-medium transition-colors duration-200">
                            Sign In
                        </a>
                        <a href="{{ route('register.choice') }}"
                            class="bg-white text-teal-700 px-6 py-2 rounded-full font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-lg">
                            Get Started
                        </a>
                        <a href="{{ route('download.app') }}"
                            class="bg-gray-800 text-teal-100 px-6 py-2 rounded-full font-semibold hover:bg-gray-100 hover:text-gray-800 transform hover:scale-105 transition-all duration-300 shadow-lg">
                            Download App
                        </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button x-data="{ open: false }" @click="open = !open"
                            class="text-white/90 hover:text-white transition-colors">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div x-data="{ open: false }" x-show="open" @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="md:hidden mt-4 pt-4 border-t border-white/20">
                    <div class="flex flex-col space-y-4">
                        <a href="{{ route('landing') }}#features"
                            class="text-white/90 hover:text-white font-medium transition-colors duration-200">Features</a>
                        <a href="{{ route('landing') }}#about"
                            class="text-white/90 hover:text-white font-medium transition-colors duration-200">About</a>
                        <a href="{{ route('landing') }}#contact"
                            class="text-white/90 hover:text-white font-medium transition-colors duration-200">Contact</a>
                        <div class="flex flex-col space-y-3 pt-4 border-t border-white/20">
                            <a href="{{ route('login') }}"
                                class="text-white/90 hover:text-white font-medium transition-colors duration-200 text-center">
                                Sign In
                            </a>
                            <a href="{{ route('register.choice') }}"
                                class="bg-white text-teal-700 px-6 py-2 rounded-full font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-lg text-center">
                                Get Started
                            </a>
                            <a href="{{ route('download.app') }}"
                                class="bg-gray-800 text-teal-100 px-6 py-2 rounded-full font-semibold hover:bg-gray-100 hover:text-gray-800 transform hover:scale-105 transition-all duration-300 shadow-lg text-center">
                                Download App
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <main class="flex-1 flex items-center justify-center px-6 py-12">
            <div class="max-w-6xl mx-auto text-center">
                <!-- Logo Container -->
                <div class="logo-container mb-12">
                    <div
                        class="glass-effect w-32 h-32 md:w-40 md:h-40 rounded-full flex items-center justify-center mx-auto shadow-2xl float-animation p-4">
                        <img src="{{ asset('images/PaLevel Logo-White.png') }}" alt="PaLevel"
                            class="w-full h-full object-contain">
                    </div>
                </div>

                <!-- App Name and Tagline -->
                <div class="slide-up mb-16">
                    <h1 class="text-5xl md:text-7xl font-bold text-white anta-font mb-4 tracking-wider"
                        style="text-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                        PaLevel
                    </h1>
                    <p class="text-xl md:text-2xl text-white/90 font-medium mb-8">
                        Download PaLevel from the App Store or Play Store
                    </p>
                    <div class="flex flex-col md:flex-row justify-center items-center gap-6">
                        <a href="#"
                            class="bg-white text-teal-700 px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center gap-3">
                            <i class="fab fa-apple fa-2x"></i>
                            <span class="text-lg">Download on the App Store</span>
                        </a>
                        <a href="#"
                            class="bg-white text-teal-700 px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center gap-3">
                            <i class="fab fa-google-play fa-2x"></i>
                            <span class="text-lg">Get it on Google Play</span>
                        </a>
                    </div>
                </div>
        </main>
    </div>
</body>