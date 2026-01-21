<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PalevelApiService;

class LandingController extends Controller
{
    protected $apiService;

    public function __construct(PalevelApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        try {
            $hostels = $this->apiService->getAllHostels();
        } catch (\Exception $e) {
            $hostels = [];
        }

        return view('landing', compact('hostels'));
    }
}
