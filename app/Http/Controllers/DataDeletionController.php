<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\DataDeletionRequest;

class DataDeletionController extends Controller
{
    public function show()
    {
        return view('data-deletion');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'reason' => 'required|string',
            'confirmation' => 'required|accepted',
            'data_categories' => 'required|array|min:1',
            'data_categories.*' => 'string',
        ]);

        try {
            // Log the deletion request
            Log::info('Data deletion request submitted', [
                'email' => $validated['email'],
                'name' => $validated['name'],
                'data_categories' => $validated['data_categories'],
                'timestamp' => now()
            ]);

            // Send notification email to admin
            // Mail::to('admin@palevel.com')->send(new DataDeletionRequest($validated));

            return redirect()->route('data.deletion')
                ->with('success', 'Your data deletion request has been submitted successfully. We will process your request within 30 days and contact you at your provided email address.');

        } catch (\Exception $e) {
            Log::error('Failed to submit data deletion request', [
                'error' => $e->getMessage(),
                'request_data' => $validated
            ]);

            return back()->withInput()
                ->with('error', 'There was an error submitting your request. Please try again or contact support directly.');
        }
    }
}
