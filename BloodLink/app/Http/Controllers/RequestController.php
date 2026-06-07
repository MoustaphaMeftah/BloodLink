<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Mail\NewMatchingRequest;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Traits\BloodCompatibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    use BloodCompatibility;

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blood_type' => 'required|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'quantity' => 'required|integer|min:1',
            'urgency' => 'required|in:low,medium,high,critical',
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $hospital = Auth::user()->hospital;

        if (! $hospital) {
            return back()->withErrors(['error' => 'Hospital profile not found']);
        }

        $bloodRequest = BloodRequest::create([
            'hospital_id' => $hospital->id,
            'blood_type' => $request->blood_type,
            'quantity' => $request->quantity,
            'urgency' => $request->urgency,
            'location' => $request->location,
            'status' => 'open',
        ]);
        ActivityLogger::log('create_request', "Created blood request #{$bloodRequest->id} ({$request->blood_type}, {$request->quantity}ml).", 'App\Models\BloodRequest', $bloodRequest->id);

        $compatibleTypes = self::getCompatibleBloodTypes($request->blood_type);
        $matchingDonors = Donor::whereIn('blood_type', $compatibleTypes)->with('user')->get();
        foreach ($matchingDonors as $donor) {
            try {
                Mail::to($donor->user->email)->send(new NewMatchingRequest($donor->user, $bloodRequest));
            } catch (\Exception $e) {
            }
        }

        return redirect()->route('hospital.dashboard')->with('success', 'Blood request created successfully.');
    }
}
