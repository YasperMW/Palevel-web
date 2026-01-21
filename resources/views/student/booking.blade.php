@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Complete Your Booking</h1>
            <p class="mt-2 text-gray-600">Review details and confirm your stay.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Hostel & Room Info -->
            <div class="p-6 border-b border-gray-200 bg-teal-50">
                <h2 class="text-xl font-semibold text-gray-900">{{ $hostel['name'] ?? 'Hostel' }}</h2>
                <p class="text-gray-600">{{ $hostel['address'] ?? '' }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Room Number</span>
                        <p class="text-lg font-bold text-gray-900">{{ $room['room_number'] ?? '' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Type</span>
                        <p class="text-lg font-bold text-gray-900">{{ ucfirst($room['type'] ?? $room['room_type'] ?? '') }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Price</span>
                        <p class="text-lg font-bold text-teal-600">MWK {{ number_format($room['price_per_month'] ?? 0) }}<span class="text-sm font-normal text-gray-500">/mo</span></p>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="p-6">
                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('booking.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room['room_id'] ?? $room['id'] ?? '' }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Check-in Date -->
                        <div>
                            <label for="check_in_date" class="block text-sm font-medium text-gray-700 mb-1">Check-in Date</label>
                            <input type="date" name="check_in_date" id="check_in_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_months" class="block text-sm font-medium text-gray-700 mb-1">Duration (Months)</label>
                            <input type="number" name="duration_months" id="duration_months" required min="1" value="1"
                                   class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Payment Type -->
                        <div>
                            <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-1">Payment Type</label>
                            <select name="payment_type" id="payment_type" class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                                <option value="full">Full Payment</option>
                                <option value="booking_fee">Booking Fee Only</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500" id="payment_hint">Pay full rent or just a booking fee to reserve.</p>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                                <option value="airtel_money">Airtel Money</option>
                                <option value="mpamba">Mpamba</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <button type="submit" class="w-full bg-teal-600 text-white px-6 py-3 rounded-lg font-bold text-lg hover:bg-teal-700 transition-colors shadow-sm">
                            Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
