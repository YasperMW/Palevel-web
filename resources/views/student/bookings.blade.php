@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Teal Header - Matching Flutter App -->
    <header class="bg-gradient-to-r from-teal-600 to-teal-400 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-24">
                <!-- Logo and Title Section -->
                <div class="flex items-center space-x-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20">
                        <img src="{{ asset('images/PaLevel Logo-White.png') }}" alt="PaLevel" class="h-10 w-auto">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">My Bookings</h1>
                        <p class="text-teal-100">Manage your accommodation bookings</p>
                    </div>
                </div>
                
                <!-- Notification Icon -->
                <div class="relative">
                    <button class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20 hover:bg-white/20 transition-colors duration-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        @if(isset($unreadNotifications) && $unreadNotifications > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                            </span>
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ $error }}
            </div>
        @endif

        <!-- Booking Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-teal-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ count($bookings ?? []) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Confirmed</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $confirmedBookings ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $pendingBookings ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Cancelled</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $cancelledBookings ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Your Bookings</h2>
                    <div class="flex items-center space-x-2">
                        <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="all">All Status</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                
                @if(isset($bookings) && count($bookings) > 0)
                    <div class="space-y-4">
                        @foreach($bookings as $booking)
                            <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $booking['hostel_name'] ?? 'Hostel Name' }}</h3>
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                                @if($booking['status'] === 'confirmed') bg-green-100 text-green-800
                                                @elseif($booking['status'] === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($booking['status'] ?? 'Unknown') }}
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                Room: {{ $booking['room_number'] ?? 'Not specified' }}
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($booking['start_date'])->format('M d, Y') }} - {{ \Carbon\Carbon::parse($booking['end_date'])->format('M d, Y') }}
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                MWK {{ number_format($booking['total_amount'], 2) }}
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Booked {{ \Carbon\Carbon::parse($booking['created_at'])->diffForHumans() }}
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col space-y-2 ml-4">
                                        <button class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 text-sm font-medium transition-colors duration-200">
                                            View Details
                                        </button>
                                        @if($booking['status'] === 'pending')
                                            <button class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 text-sm font-medium transition-colors duration-200">
                                                Cancel Booking
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-500 text-lg">No bookings found</p>
                        <p class="text-gray-400 text-sm mt-2">Start by booking a hostel from the home page</p>
                        <a href="{{ route('student.home') }}" class="inline-flex items-center px-4 py-2 mt-4 bg-teal-600 text-white rounded-lg hover:bg-teal-700 text-sm font-medium transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Browse Hostels
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection
