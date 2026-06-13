<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Helpers\LocationService;
use App\Mail\DonorConfirmed;
use App\Models\Appointment;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\DonorResponse;
use App\Models\Inventory;
use App\Traits\BloodCompatibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    use BloodCompatibility;

    public function dashboard()
    {
        $hospital = Auth::user()->hospital;

        $inventory = Inventory::where('hospital_id', $hospital->id)->get()->keyBy('blood_type');
        $allBloodTypes = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];
        $needsLocation = session()->get('show_location', false) || ! $hospital->latitude || ! $hospital->longitude;

        return view('hospital.dashboard', compact('hospital', 'inventory', 'allBloodTypes', 'needsLocation'));
    }

    public function manageRequests()
    {
        $hospital = Auth::user()->hospital;
        $requests = BloodRequest::where('hospital_id', $hospital->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        ActivityLogger::log('view_requests', 'Viewed hospital requests.');

        return view('hospital.requests', compact('requests'));
    }

    public function showCreateRequest()
    {
        return view('hospital.create-request');
    }

    public function showRequest(BloodRequest $request)
    {
        $request->load('hospital', 'responses.donor.user');

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

        ActivityLogger::log('update_request', "Updated blood request #{$request->id}.", 'App\Models\BloodRequest', $request->id);

        return back()->with('success', 'Request updated successfully.');
    }

    public function showNearbyDonors()
    {
        $hospital = Auth::user()->hospital;

        if (!$hospital) {
            $hospital = (object) ['latitude' => null, 'longitude' => null];
        }

        $compatibleTypes = self::getCompatibleBloodTypes('O-');
        $donors = Donor::with('user')
            ->where('availability', true)
            ->get()
            ->filter(function ($donor) {
                return $donor->latitude !== null && $donor->longitude !== null;
            });

        $nearbyDonors = [];
        if ($hospital->latitude && $hospital->longitude) {
            $nearbyDonors = LocationService::filterNearby($donors, $hospital->latitude, $hospital->longitude);
        }

        $allBloodTypes = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];

        return view('hospital.nearby-donors', compact('hospital', 'nearbyDonors', 'donors', 'allBloodTypes'));
    }

    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $hospital = Auth::user()->hospital;
        if (!$hospital) {
            return back()->with('error', 'Only hospitals can update their location here.');
        }

        $hospital->update($request->only(['latitude', 'longitude']));

        ActivityLogger::log('update_location', 'Updated hospital location.', 'App\Models\Hospital', $hospital->id);

        return back()->with('success', 'Location updated successfully.');
    }

    public function confirmDonor(Request $request, DonorResponse $response)
    {
        $validator = Validator::make($request->all(), [
            'scheduled_date' => 'required|date|after:now',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $hospital = Auth::user()->hospital;

        if ($response->bloodRequest->hospital_id !== $hospital->id) {
            return back()->with('error', 'This response belongs to another hospital.');
        }

        if ($response->status !== 'accepted') {
            return back()->with('error', 'Only accepted responses can be confirmed.');
        }

        if ($response->confirmed_at) {
            return back()->with('error', 'This donor has already been confirmed.');
        }

        $response->update(['confirmed_at' => now()]);

        Appointment::create([
            'donor_id' => $response->donor->user_id,
            'hospital_id' => $hospital->user_id,
            'blood_request_id' => $response->blood_request_id,
            'scheduled_date' => $request->scheduled_date,
            'status' => 'confirmed',
            'notes' => $request->notes,
        ]);

        ActivityLogger::log('confirm_donor', "Confirmed donor for request #{$response->blood_request_id}.", 'App\Models\DonorResponse', $response->id);

        try {
            Mail::to($response->donor->user->email)->send(new DonorConfirmed($response));
        } catch (\Exception $e) {
        }

        return back()->with('success', 'Donor confirmed. Appointment scheduled and donor notified.');
    }
}
