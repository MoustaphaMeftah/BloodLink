<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    public function dashboard()
    {
        $hospital = Auth::user()->hospital;

        return view('hospital.dashboard', compact('hospital'));
    }

    public function manageRequests()
    {
        $hospital = Auth::user()->hospital;
        $requests = BloodRequest::where('hospital_id', $hospital->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('hospital.requests', compact('requests'));
    }

    public function showCreateRequest()
    {
        return view('hospital.create-request');
    }

    public function showRequest(BloodRequest $request)
    {
        $request->load('hospital', 'donors.user');
        return view('hospital.show-request', compact('request'));
    }

    public function updateRequest(Request $req, BloodRequest $request)
    {
        $validator = Validator::make($req->all(), [
            'blood_type' => 'nullable|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'quantity' => 'nullable|integer|min:1',
            'urgency' => 'nullable|in:low,medium,high,critical',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|in:open,fulfilled,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $request->update($req->only(['blood_type', 'quantity', 'urgency', 'location', 'status']));

        return back()->with('success', 'Request updated successfully.');
    }
}
