<?php

namespace App\Http\Controllers;

use App\Services\PalevelApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    private PalevelApiService $apiService;

    public function __construct(PalevelApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function create($hostelId, $roomId)
    {
        try {
            // Get all rooms for this hostel to find the specific one
            // We use 'student' user_type to get availability info if needed
            $rooms = $this->apiService->getHostelRooms($hostelId, ['user_type' => 'student']);
            
            // Find the specific room
            $room = null;
            foreach ($rooms as $r) {
                $rId = $r['room_id'] ?? $r['id'] ?? '';
                if ($rId == $roomId) {
                    $room = $r;
                    break;
                }
            }

            if (!$room) {
                return redirect()->back()->with('error', 'Room not found');
            }
            
            // Get hostel details
            $hostel = $this->apiService->getHostel($hostelId);

            return view('student.booking', compact('room', 'hostel'));

        } catch (\Exception $e) {
            Log::error("Booking page load failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load booking page. Please try again.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|string',
            'check_in_date' => 'required|date|after:today',
            'duration_months' => 'required|integer|min:1',
            'payment_type' => 'required|in:full,booking_fee',
            'payment_method' => 'required|string'
        ]);

        try {
            $token = Session::get('palevel_token');
            if (!$token) {
                return redirect()->route('login')->with('error', 'Please login to book a room');
            }

            $bookingData = [
                'room_id' => $request->room_id,
                'check_in_date' => $request->check_in_date,
                'duration_months' => (int)$request->duration_months,
                'payment_type' => $request->payment_type,
                'payment_method' => $request->payment_method,
                'amount' => 0 // Backend calculates actual amount
            ];

            $this->apiService->createBooking($bookingData, $token);

            return redirect()->route('student.bookings')->with('success', 'Booking request submitted successfully!');

        } catch (\Exception $e) {
            Log::error("Booking submission failed: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to submit booking: ' . $e->getMessage());
        }
    }

    public function showPayment($bookingId)
    {
        try {
            // Get booking details from API
            $token = Session::get('palevel_token');
            if (!$token) {
                return redirect()->route('login')->with('error', 'Please login to make payment');
            }

            // Get booking details
            $booking = $this->apiService->getBooking($bookingId, $token);
            
            if (!$booking) {
                return redirect()->route('student.bookings')->with('error', 'Booking not found');
            }

            // Initiate PayChangu payment
            $paymentData = [
                'booking_id' => $bookingId,
                'amount' => $booking['amount'],
                'currency' => 'MWK',
                'return_url' => route('student.bookings'),
                'cancel_url' => route('student.bookings')
            ];

            $paymentResponse = $this->apiService->initiatePayChanguPayment($paymentData, $token);
            
            if (!$paymentResponse || !isset($paymentResponse['payment_url'])) {
                return back()->with('error', 'Failed to initiate payment. Please try again.');
            }

            $paymentUrl = $paymentResponse['payment_url'];

            return view('student.payment', compact('booking', 'paymentUrl'));

        } catch (\Exception $e) {
            Log::error("Payment page load failed: " . $e->getMessage());
            return redirect()->route('student.bookings')->with('error', 'Failed to load payment page: ' . $e->getMessage());
        }
    }

    // API Methods for AJAX calls
    public function apiCreate(Request $request)
    {
        try {
            $request->validate([
                'room_id' => 'required|string',
                'check_in_date' => 'required|date|after:today',
                'duration_months' => 'required|integer|min:1',
                'amount' => 'required|numeric|min:0',
                'payment_type' => 'required|in:full_payment,booking_fee',
                'payment_method' => 'required|string',
                'status' => 'required|string'
            ]);

            $token = Session::get('palevel_token');
            if (!$token) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $bookingData = [
                'room_id' => $request->room_id,
                'check_in_date' => $request->check_in_date,
                'duration_months' => (int)$request->duration_months,
                'amount' => (float)$request->amount,
                'payment_type' => $request->payment_type,
                'payment_method' => $request->payment_method,
                'status' => $request->status
            ];

            $booking = $this->apiService->createBooking($bookingData, $token);

            return response()->json([
                'success' => true,
                'data' => $booking,
                'message' => 'Booking created successfully'
            ], 201);

        } catch (\Exception $e) {
            Log::error("API booking creation failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiUserBookings(Request $request)
    {
        try {
            $token = Session::get('palevel_token');
            if (!$token) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $bookings = $this->apiService->getUserBookings($token);

            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);

        } catch (\Exception $e) {
            Log::error("API user bookings failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiUserGender(Request $request)
    {
        try {
            $token = Session::get('palevel_token');
            if (!$token) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            // Get user details from API
            $user = $this->apiService->getCurrentUser($token);
            $gender = $user['gender'] ?? '';

            return response($gender, 200);

        } catch (\Exception $e) {
            Log::error("API user gender failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user gender: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiUserDetails(Request $request)
    {
        try {
            $token = Session::get('palevel_token');
            if (!$token) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            // Get user details from API
            $user = $this->apiService->getCurrentUser($token);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            Log::error("API user details failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiInitiatePayment(Request $request)
    {
        try {
            $request->validate([
                'booking_id' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'email' => 'required|email',
                'phone_number' => 'nullable|string',
                'first_name' => 'nullable|string',
                'last_name' => 'nullable|string',
                'currency' => 'required|string'
            ]);

            $token = Session::get('palevel_token');
            if (!$token) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $paymentData = [
                'booking_id' => $request->booking_id,
                'amount' => (float)$request->amount,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'currency' => $request->currency
            ];

            $payment = $this->apiService->initiatePayChanguPayment($paymentData, $token);

            return response()->json([
                'success' => true,
                'data' => $payment,
                'message' => 'Payment initiated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("API payment initiation failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiVerifyPayment(Request $request)
    {
        try {
            $request->validate([
                'reference' => 'required|string'
            ]);

            $token = Session::get('palevel_token');
            if (!$token) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $reference = $request->reference;
            $payment = $this->apiService->verifyPayment($reference, $token);

            return response()->json([
                'success' => true,
                'data' => $payment,
                'message' => 'Payment verified successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("API payment verification failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
