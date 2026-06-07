<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
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

        if (!$hospital) {
            return back()->withErrors(['error' => 'Hospital profile not found']);
        }

        BloodRequest::create([
            'hospital_id' => $hospital->id,
            'blood_type' => $request->blood_type,
            'quantity' => $request->quantity,
            'urgency' => $request->urgency,
            'location' => $request->location,
            'status' => 'open',
        ]);

        return redirect()->route('hospital.dashboard')->with('success', 'Blood request created successfully.');
    }
}
