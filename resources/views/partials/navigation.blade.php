@php
    $currentUser = Session::get('palevel_user');
@endphp

<!-- Modern Navigation Header Matching Flutter App -->
<header class="bg-gradient-to-r from-teal-600 to-teal-400 shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('landing') }}" class="flex items-center space-x-3 group">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-2 border border-white/20 group-hover:bg-white/20 transition-colors duration-200">
                        <img src="{{ asset('images/PaLevel Logo-White.png') }}" alt="PaLevel" class="h-8 w-auto">
                    </div>
                    <span class="text-white font-bold text-xl">PaLevel</span>
                </a>
                
                <!-- User Type Badge -->
                <span class="hidden sm:inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-white/20 backdrop-blur-sm text-white border border-white/30">
                    {{ ucfirst($currentUser['user_type'] ?? 'user') }}
                </span>
            </div>

        

            <!-- Right Side Actions -->
            <div class="flex items-center space-x-3">
                <!-- Notifications -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-white hover:bg-white/10 rounded-lg transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    
                    <!-- Notifications Dropdown -->
                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <div class="p-4 text-center text-gray-500">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <p class="text-sm">No new notifications</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 p-2 text-white hover:bg-white/10 rounded-lg transition-colors duration-200">
                        <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white font-semibold border border-white/30">
                            {{ strtoupper(substr($currentUser['first_name'] ?? 'U', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-medium">{{ $currentUser['first_name'] ?? 'User' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- User Dropdown -->
                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">
                        <div class="p-4 border-b border-gray-200">
                            <p class="text-sm font-semibold text-gray-900">{{ $currentUser['first_name'] ?? 'User' }} {{ $currentUser['last_name'] ?? '' }}</p>
                            <p class="text-xs text-gray-500">{{ $currentUser['email'] ?? '' }}</p>
                        </div>
                        <div class="py-2">
                            <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile Settings
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Settings
                            </a>
                            <div class="border-t border-gray-200 mt-2 pt-2">
                                <form action="{{ route('logout') }}" method="POST" class="block">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden">
                    <button x-data="{ open: false }" @click="open = !open" class="p-2 text-white hover:bg-white/10 rounded-lg transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div x-data="{ open: false }" x-show="open" @click.away="open = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="lg:hidden bg-white/95 backdrop-blur-sm border-t border-white/20">
            <div class="px-4 py-3 space-y-1">
                @if($currentUser['user_type'] === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="mobile-nav-link">Dashboard</a>
                    <a href="{{ route('admin.students') }}" class="mobile-nav-link">Students</a>
                    <a href="{{ route('admin.landlords') }}" class="mobile-nav-link">Landlords</a>
                    <a href="{{ route('admin.hostels') }}" class="mobile-nav-link">Hostels</a>
                    <a href="{{ route('admin.bookings') }}" class="mobile-nav-link">Bookings</a>
                    <a href="{{ route('admin.payments') }}" class="mobile-nav-link">Payments</a>
                @elseif($currentUser['user_type'] === 'landlord')
                    <a href="{{ route('landlord.dashboard') }}" class="mobile-nav-link">Dashboard</a>
                    <a href="{{ route('hostels.index') }}" class="mobile-nav-link">My Hostels</a>
                    <a href="{{ route('landlord.hostels.create') }}" class="mobile-nav-link mobile-nav-link--primary">Add Hostel</a>
                    <a href="{{ route('hostels.index') }}" class="mobile-nav-link">Analytics</a>
                @else
                    <a href="{{ route('student.home') }}" class="mobile-nav-link">Home</a>
                    <a href="{{ route('student.bookings') }}" class="mobile-nav-link">My Bookings</a>
                @endif
            </div>
        </div>
    </div>
</header>

<!-- Second Layer Navigation -->
<nav class="bg-white shadow-sm border-b border-gray-200 sticky top-16 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex space-x-4 justify-center overflow-x-auto">
            @if($currentUser['user_type'] === 'admin')
                <!-- Admin Navigation -->
                <a href="{{ route('admin.dashboard') }}" class="second-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.students') }}" class="second-nav-link {{ request()->routeIs('admin.students') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Students</span>
                </a>
                <a href="{{ route('admin.landlords') }}" class="second-nav-link {{ request()->routeIs('admin.landlords') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Landlords</span>
                </a>
                <a href="{{ route('admin.hostels') }}" class="second-nav-link {{ request()->routeIs('admin.hostels') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Hostels</span>
                </a>
                <a href="{{ route('admin.bookings') }}" class="second-nav-link {{ request()->routeIs('admin.bookings') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span>Bookings</span>
                </a>
                <a href="{{ route('admin.payments') }}" class="second-nav-link {{ request()->routeIs('admin.payments') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Payments</span>
                </a>
                
            @elseif($currentUser['user_type'] === 'landlord')
                <!-- Landlord Navigation -->
                <a href="{{ route('landlord.dashboard') }}" class="second-nav-link {{ request()->routeIs('landlord.dashboard') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('hostels.index') }}" class="second-nav-link {{ request()->routeIs('hostels.index') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>My Hostels</span>
                </a>
                <a href="{{ route('landlord.hostels.create') }}" class="second-nav-link {{ request()->routeIs('landlord.hostels.create') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Add Hostel</span>
                </a>
                <a href="{{ route('hostels.index') }}" class="second-nav-link {{ request()->routeIs('hostels.analytics') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Analytics</span>
                </a>
                
            @else
                <!-- Student/Tenant Navigation -->
                <a href="{{ route('student.home') }}" class="second-nav-link {{ request()->routeIs('student.home') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Home</span>
                </a>
                <a href="{{ route('student.bookings') }}" class="second-nav-link {{ request()->routeIs('student.bookings') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    <span>My Bookings</span>
                </a>
            @endif
        </div>
    </div>
</nav>



<style>
.nav-link {
    @apply flex items-center space-x-2 px-4 py-2 text-sm font-medium text-white hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200;
}

.nav-link--primary {
    @apply bg-white/20 text-white hover:bg-white/30 shadow-lg;
}

.nav-link:hover {
    @apply transform scale-105;
}

.nav-link svg {
    @apply text-white;
}

.second-nav-link {
    @apply flex flex-col items-center justify-center px-4 py-3 text-teal-600 hover:text-teal-800 hover:bg-teal-50 transition-all duration-200 min-w-[100px] h-24 border-b-4 border-transparent text-sm font-medium;
}

.second-nav-link svg {
    @apply w-6 h-6 mb-1.5 transition-colors duration-200;
}

.second-nav-link:hover {
    @apply transform scale-105;
}

.second-nav-link.active {
    @apply border-teal-600 bg-teal-50 text-teal-800 font-bold;
}

.second-nav-link--primary {
    @apply border-teal-600 bg-teal-50 text-teal-800 font-bold;
}

.mobile-nav-link {
    @apply flex items-center space-x-3 px-4 py-3 text-sm font-medium text-white hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200;
}

.mobile-nav-link--primary {
    @apply bg-white/20 text-white hover:bg-white/30 shadow-lg;
}

.mobile-nav-link:hover {
    @apply transform translate-x-1;
}

.mobile-nav-link svg {
    @apply text-white;
}
</style>
