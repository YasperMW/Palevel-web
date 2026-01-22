<?php

namespace App\Http\Middleware;

use App\Services\PalevelApiService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class UserProfileMiddleware
{
    private PalevelApiService $apiService;

    public function __construct(PalevelApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Only process for authenticated users
        if (Session::has('palevel_user') && Session::has('palevel_token')) {
            $user = Session::get('palevel_user');
            $token = Session::get('palevel_token');
            
            // Only fetch profile if not already cached in session
            if (!Session::has('palevel_user_details')) {
                try {
                    $email = $user['email'] ?? null;
                    
                    if ($email) {
                        // Fetch user profile using email
                        $userProfile = $this->apiService->getUserProfile(email: $email);
                        
                        if ($userProfile) {
                            // Cache the profile in session
                            Session::put('palevel_user_details', $userProfile);
                            Log::info('User profile cached in session', ['email' => $email]);
                        }
                    } else {
                        Log::warning('User session missing email', ['user' => $user]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to fetch user profile in middleware', [
                        'error' => $e->getMessage(),
                        'email' => $user['email'] ?? null
                    ]);
                    // Don't block the request, just continue without profile
                }
            }
        }

        return $next($request);
    }
}
