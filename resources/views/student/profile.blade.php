@extends('layouts.app')

@section('title', 'Student Profile')

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
                        <h1 class="text-2xl font-bold text-white">Profile</h1>
                        <p class="text-teal-100">Manage your personal information</p>
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

        @if(isset($success))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ $success }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="text-center">
                            <div class="mx-auto h-24 w-24 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-12 h-12 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ Auth::user()->name ?? 'Student Name' }}</h2>
                            <p class="text-gray-500">{{ Auth::user()->email ?? 'student@example.com' }}</p>
                            <div class="mt-4 flex items-center justify-center space-x-2">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800">
                                    Student
                                </span>
                                @if(Auth::user()->email_verified_at ?? false)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Verified
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Member Since</span>
                                    <span class="text-sm font-medium text-gray-900">{{ Auth::user()->created_at ? \Carbon\Carbon::parse(Auth::user()->created_at)->format('M Y') : 'Unknown' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Total Bookings</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $totalBookings ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status</span>
                                    <span class="text-sm font-medium text-green-600">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                            <button class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                                Edit Profile
                            </button>
                        </div>
                        
                        <form class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" value="{{ Auth::user()->name ?? '' }}" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                           placeholder="Enter your first name">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" value="{{ Auth::user()->last_name ?? '' }}" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                           placeholder="Enter your last name">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" value="{{ Auth::user()->email ?? '' }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                       placeholder="Enter your email address">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" value="{{ Auth::user()->phone ?? '' }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                       placeholder="Enter your phone number">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">University</label>
                                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                        <option>Select University</option>
                                        <option>UNIMA</option>
                                        <option>MUST</option>
                                        <option>LUANAR</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
                                    <input type="text" value="{{ Auth::user()->student_id ?? '' }}" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                           placeholder="Enter your student ID">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                                <textarea rows="4" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                          placeholder="Tell us about yourself">{{ Auth::user()->bio ?? '' }}</textarea>
                            </div>
                            
                            <div class="flex items-center justify-end space-x-4">
                                <button type="button" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors duration-200">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium transition-colors duration-200">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Security Settings</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-teal-100 rounded-lg p-2">
                                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Change Password</h4>
                                        <p class="text-sm text-gray-500">Update your password regularly</p>
                                    </div>
                                </div>
                                <button class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                                    Change
                                </button>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-100 rounded-lg p-2">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Two-Factor Authentication</h4>
                                        <p class="text-sm text-gray-500">Add an extra layer of security</p>
                                    </div>
                                </div>
                                <button class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                                    Enable
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
