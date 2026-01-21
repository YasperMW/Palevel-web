<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PalevelApiService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('palevel.api_url');
        $this->timeout = config('palevel.api_timeout');
    }

    private function makeRequest(string $method, string $endpoint, array $data = [], array $headers = [])
    {
        try {
            // Ensure base URL doesn't have trailing slash and endpoint starts with slash
            $baseUrl = rtrim($this->baseUrl, '/');
            $endpoint = '/' . ltrim($endpoint, '/');
            $url = "{$baseUrl}{$endpoint}";
            
            Log::info("Making API request: {$method} {$url}", [
                'data_count' => count($data)
            ]);
            
            $defaultHeaders = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            $headers = array_merge($defaultHeaders, $headers);

            // Try with shorter timeout first
            $timeout = min($this->timeout, 10); // Cap at 10 seconds
            
            $response = Http::timeout($timeout)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for development
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->withHeaders($headers)
                ->{$method}($url, $data);

            if ($response->successful()) {
                Log::info("API request successful: {$method} {$url}");
                return $response->json();
            }

            Log::error("Palevel API Error: {$method} {$url}", [
                'status' => $response->status(),
                'response' => $response->body(),
                'data' => $data
            ]);

            throw new \Exception("API request failed: {$response->status()} - {$response->body()}");

        } catch (\Exception $e) {
            Log::error("Palevel API Exception: {$method} {$endpoint}", [
                'error' => $e->getMessage(),
                'data' => $data,
                'base_url' => $this->baseUrl
            ]);
            throw $e;
        }
    }

    public function authenticate(string $email, string $password)
    {
        return $this->makeRequest('POST', '/authenticate/', [
            'email' => $email,
            'password' => $password
        ]);
    }

    public function sendOtp(string $email, string $firstName, string $lastName, string $phone, string $userType)
    {
        return $this->makeRequest('POST', '/send-otp/', [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone_number' => $phone,
            'user_type' => $userType
        ]);
    }

    public function verifyOtp(string $email, string $code)
    {
        return $this->makeRequest('POST', '/verify-otp/', [
            'email' => $email,
            'otp' => $code
        ]);
    }

    public function createUserWithId(array $userData, $nationalIdImage = null)
    {
        if ($nationalIdImage) {
            // Use multipart request for file upload
            $multipartData = [];
            
            foreach ($userData as $key => $value) {
                $multipartData[] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }
            
            $multipartData[] = [
                'name' => 'national_id_image',
                'contents' => fopen($nationalIdImage->getPathname(), 'r'),
                'filename' => $nationalIdImage->getClientOriginalName()
            ];
            
            return $this->makeMultipartRequest('POST', '/create_user_with_id/', $multipartData);
        }
        
        return $this->makeRequest('POST', '/create_user_with_id/', $userData);
    }

    public function verifyToken(string $token)
    {
        return $this->makeRequest('POST', '/verify_token/', [
            'token' => $token
        ]);
    }

    public function googleAuthenticate(array $data)
    {
        // Construct payload for Python backend /auth/firebase/signin
        $payload = [
            'id_token' => $data['id_token'],
            'email' => $data['email'],
            'display_name' => $data['display_name'] ?? null,
            'photo_url' => $data['photo_url'] ?? null
        ];
        
        return $this->makeRequest('POST', '/auth/firebase/signin', $payload);
    }

    public function createUser(array $userData)
    {
        // Check if there's a file upload (for landlord ID document)
        if (isset($userData['national_id_image']) && $userData['national_id_image'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $userData['national_id_image'];
            
            // Prepare multipart data for file upload
            $multipartData = [];
            
            // Add form fields
            foreach ($userData as $key => $value) {
                if ($key !== 'national_id_image') {
                    $multipartData[] = [
                        'name' => $key,
                        'contents' => $value
                    ];
                }
            }
            
            // Add file
            $multipartData[] = [
                'name' => 'national_id_image',
                'contents' => fopen($file->getPathname(), 'r'),
                'filename' => $file->getClientOriginalName()
            ];
            
            return $this->makeMultipartRequest('POST', '/create_user_with_id/', $multipartData);
        }
        
        // Regular user creation without file
        return $this->makeRequest('POST', '/create_user/', $userData);
    }

    private function makeMultipartRequest(string $method, string $endpoint, array $multipartData = [])
    {
        try {
            $url = "{$this->baseUrl}{$endpoint}";
            
            Log::info("Making multipart API request: {$method} {$url}");
            
            // Use shorter timeout for file uploads
            $timeout = min($this->timeout, 30); // Cap at 30 seconds for file uploads
            
            $response = Http::timeout($timeout)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for development
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->asMultipart()
                ->{$method}($url, $multipartData);

            if ($response->successful()) {
                Log::info("Multipart API request successful: {$method} {$url}");
                return $response->json();
            }

            Log::error("Palevel API Multipart Error: {$method} {$url}", [
                'status' => $response->status(),
                'response' => $response->body(),
                'data' => array_map(fn($item) => $item['name'], $multipartData)
            ]);

            throw new \Exception("API request failed: {$response->status()} - {$response->body()}");

        } catch (\Exception $e) {
            Log::error("Palevel API Multipart Exception: {$method} {$endpoint}", [
                'error' => $e->getMessage(),
                'data' => array_map(fn($item) => $item['name'], $multipartData),
                'base_url' => $this->baseUrl
            ]);
            throw $e;
        }
    }

    public function completeRoleSelection(array $data, string $token)
    {
        // Check if there's a file upload (for landlord ID document)
        if (isset($data['national_id_image']) && $data['national_id_image'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $data['national_id_image'];
            
            // Prepare multipart data
            $multipartData = [];
            
            // Add form fields
            foreach ($data as $key => $value) {
                if ($key !== 'national_id_image') {
                    $multipartData[] = [
                        'name' => $key,
                        'contents' => $value
                    ];
                }
            }
            
            // Add file
            $multipartData[] = [
                'name' => 'national_id_image',
                'contents' => fopen($file->getPathname(), 'r'),
                'filename' => $file->getClientOriginalName()
            ];
            
            return $this->makeMultipartRequest('POST', '/auth/role-selection-with-id', $multipartData, [
                'Authorization' => "Bearer {$token}"
            ]); 
        }
        
        // Regular JSON request
        return $this->makeRequest('POST', '/auth/role-selection', $data, [
            'Authorization' => "Bearer {$token}"
        ]);
    }

    public function getUserProfile(?string $email = null, ?string $userId = null)
    {
        $params = [];
        if ($email) $params['email'] = $email;
        if ($userId) $params['user_id'] = $userId;

        $endpoint = '/user/profile/?' . http_build_query($params);
        return $this->makeRequest('GET', $endpoint);
    }

    public function getAllHostels()
    {
        $token = session('palevel_token');
        $headers = [];
        
        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";
        }
        
        // Add debug logging
        Log::info("Fetching all hostels", [
            'has_token' => !empty($token),
            'endpoint' => '/hostels/all-hostels'
        ]);
        
        $response = $this->makeRequest('GET', '/hostels/all-hostels', [], $headers);
        
        // Check if response indicates authentication issue
        if (isset($response['detail']) && str_contains($response['detail'], 'Not authenticated')) {
            Log::error("Authentication failed for hostels", [
                'token_length' => strlen($token ?? ''),
                'response' => $response
            ]);
            throw new \Exception('Authentication failed. Please log in again.');
        }
        
        return $response;
    }

    public function getHostel(string $hostelId)
    {
        return $this->makeRequest('GET', "/hostels/{$hostelId}");
    }

    public function getLandlordHostels(string $token)
    {
        return $this->makeRequest('GET', '/hostels/', [], [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
    }

    public function getHostelRooms(string $hostelId, array $params = [])
    {
        return $this->makeRequest('GET', '/rooms', ['hostel_id' => $hostelId] + $params);
    }

    public function createHostel(array $hostelData, string $token)
    {
        return $this->makeRequest('POST', '/hostels/', $hostelData, [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
    }

    public function createRoom(array $roomData, string $token)
    {
        return $this->makeRequest('POST', '/rooms/', $roomData, [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
    }

    public function getLandlordStats(string $landlordEmail, string $token)
    {
        return $this->makeRequest('GET', "/landlord/{$landlordEmail}/stats/", [], [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
    }

    public function getStudentStats(string $studentId, string $token)
    {
        return $this->makeRequest('GET', "/student/{$studentId}/stats/", [], [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
    }

    public function getBookings(string $token, array $filters = [])
    {
        // Check if student_id is present (tenant dashboard)
        if (isset($filters['student_id'])) {
            return $this->getMyBookings($token);
        }
        
        // If it's a landlord (usually no student_id filter or specific landlord logic)
        // But checking if we are in landlord context is harder here.
        // Let's assume if no student_id, it might be landlord or admin.
        // For now, let's keep the original generic call for fallback, but 
        // ideally we should use specific endpoints.
        
        // However, based on backend analysis:
        // /bookings/my-bookings/ -> Tenant bookings
        // /bookings/landlord/ -> Landlord bookings
        
        // Let's try to infer or just map to what we know works.
        // If we want landlord bookings, we should use getLandlordBookings method.
        
        $endpoint = '/bookings/?' . http_build_query($filters);
        // Correctly pass headers as the 4th argument, leaving the 3rd argument (data) empty for GET requests
        return $this->makeRequest('GET', $endpoint, [], [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
    }

    public function getMyBookings(string $token)
    {
        // Get user from session to get user_id (optional logging context)
        $user = session('palevel_user');
        $userId = $user['user_id'] ?? null;
        
        $endpoint = '/bookings/my-bookings/';
        
        // Add debug logging
        Log::info("Fetching bookings for user", [
            'user_id' => $userId,
            'token' => substr($token, 0, 20) . '...', // Log first 20 chars for security
            'endpoint' => $endpoint
        ]);
        
        // Correctly pass headers as the 4th argument, leaving the 3rd argument (data) empty for GET requests
        $response = $this->makeRequest('GET', $endpoint, [], [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
        
        // Check if response indicates authentication issue
        if (isset($response['detail']) && str_contains($response['detail'], 'Not authenticated')) {
            Log::error("Authentication failed for bookings", [
                'user_id' => $userId,
                'token_length' => strlen($token),
                'response' => $response
            ]);
            throw new \Exception('Authentication failed. Please log in again.');
        }
        
        return $response;
    }

    public function getLandlordBookings(string $token)
    {
        return $this->makeRequest('GET', '/bookings/landlord/', [], [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
    }

    public function createBooking(array $bookingData, string $token)
    {
        return $this->makeRequest('POST', '/bookings/', $bookingData, [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
    }

    public function getPayments(string $token, array $filters = [])
    {
        $endpoint = '/payments/?' . http_build_query($filters);
        return $this->makeRequest('GET', $endpoint, [], [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
    }

    public function getNotifications(string $userId, string $token)
    {
        // Backend endpoint is /notifications
        // Pass user_id as query param in the data array
        $response = $this->makeRequest('GET', '/notifications', ['user_id' => $userId], [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
        
        // Extract notifications list if wrapped (backend returns {notifications: [], total: ...})
        if (isset($response['notifications']) && is_array($response['notifications'])) {
            return $response['notifications'];
        }
        
        return $response;
    }

    public function markNotificationAsRead(string $notificationId, string $token)
    {
        return $this->makeRequest('PUT', "/notifications/{$notificationId}/read", [], [
            'Authorization' => "Bearer {$token}"
        ]);
    }

    public function getHostelReviews(string $hostelId)
    {
        return $this->makeRequest('GET', "/reviews/hostel/{$hostelId}");
    }

    public function getHostelLandlord(string $hostelId)
    {
        return $this->makeRequest('GET', "/hostels/{$hostelId}/landlord");
    }

    public function getCurrentUser(string $token)
    {
        return $this->makeRequest('GET', '/users/me', [], [
            'Authorization' => 'Bearer ' . trim($token)
        ]);
    }

    public function getHostelBookings(string $hostelId)
    {
        return $this->makeRequest('GET', "/hostels/{$hostelId}/bookings");
    }
}
