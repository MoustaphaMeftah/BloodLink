<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Helpers\LocationService;
use App\Mail\DonorResponded;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\DonorResponse;
use App\Traits\ApiResponse;
use App\Traits\BloodCompatibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class DonorController extends Controller
{
    use ApiResponse, BloodCompatibility;

    // ==================== WEB METHODS ====================

    public function dashboard()
    {
        $donor = Auth::user()->donor;
        $needsLocation = session()->get('show_location', false) || ! $donor->latitude || ! $donor->longitude;

        return view('donor.dashboard', compact('donor', 'needsLocation'));
    }

    public function getPendingWebRequests()
    {
        $donor = Auth::user()->donor;
        $bloodType = $donor->blood_type;

        $respondedIds = DonorResponse::where('donor_id', $donor->id)->pluck('blood_request_id');

        $compatibleTypes = self::getDonatableBloodTypes($bloodType);

        $requests = BloodRequest::where('status', 'open')
            ->whereIn('blood_type', $compatibleTypes)
            ->whereNotIn('id', $respondedIds)
            ->with('hospital')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('donor.requests', compact('requests'));
    }

    public function respondToWebRequest(Request $request, int $requestId)
    {
        $validator = Validator::make($request->all(), [
            'accepted' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $donor = Auth::user()->donor;
            $bloodRequest = BloodRequest::findOrFail($requestId);

            $status = $request->accepted ? 'accepted' : 'rejected';

            $response = DonorResponse::create([
                'donor_id' => $donor->id,
                'blood_request_id' => $bloodRequest->id,
                'status' => $status,
                'response_date' => now(),
            ]);

            ActivityLogger::log('respond_request', ($request->accepted ? 'Accepted' : 'Declined')." blood request #{$bloodRequest->id}.", 'App\Models\BloodRequest', $bloodRequest->id);

            try {
                $hospitalUser = $bloodRequest->hospital?->user;
                if ($hospitalUser) {
                    Mail::to($hospitalUser->email)->send(new DonorResponded($hospitalUser, $response));
                }
            } catch (\Exception $e) {
            }

            $redirect = $request->input('redirect_to', url()->previous());

            return redirect($redirect)->with('success', $request->accepted ? 'Donation accepted' : 'Request declined');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function getWebDonationHistory()
    {
        $donor = Auth::user()->donor;
        $donations = $donor->donations()
            ->with('bloodRequest.hospital')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('donor.history', compact('donations'));
    }

    public function showNearbyRequests()
    {
        $realDonor = Auth::user()->donor;

        if (!$realDonor) {
            $respondedIds = collect();
            $compatibleTypes = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];
            $donor = (object) ['latitude' => null, 'longitude' => null, 'blood_type' => null];
        } else {
            $respondedIds = DonorResponse::where('donor_id', $realDonor->id)->pluck('blood_request_id');
            $compatibleTypes = self::getDonatableBloodTypes($realDonor->blood_type);
            $donor = $realDonor;
        }

        $requests = BloodRequest::with('hospital')
            ->where('status', 'open')
            ->whereIn('blood_type', $compatibleTypes)
            ->whereNotIn('id', $respondedIds)
            ->get();

        $nearbyRequests = [];
        $allOnMap = [];

        if ($donor->latitude && $donor->longitude) {
            foreach ($requests as $req) {
                if ($req->hospital && $req->hospital->latitude && $req->hospital->longitude) {
                    $distance = LocationService::haversineDistance(
                        $donor->latitude, $donor->longitude,
                        $req->hospital->latitude, $req->hospital->longitude
                    );
                    $req->distance = round($distance, 1);
                    $allOnMap[] = $req;
                    if ($distance <= 25) {
                        $nearbyRequests[] = $req;
                    }
                }
            }
            usort($nearbyRequests, fn ($a, $b) => $a->distance <=> $b->distance);
            usort($allOnMap, fn ($a, $b) => $a->distance <=> $b->distance);
        }

        return view('donor.nearby-requests', compact('donor', 'nearbyRequests', 'allOnMap', 'requests'));
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

        $donor = Auth::user()->donor;
        if (!$donor) {
            return back()->with('error', 'Only donors can update their location here.');
        }

        $donor->update($request->only(['latitude', 'longitude']));

        ActivityLogger::log('update_location', 'Updated donor location.', 'App\Models\Donor', $donor->id);

        return back()->with('success', 'Location updated successfully.');
    }

    // ==================== API METHODS ====================

    public function index(Request $request)
    {
        try {
            $query = Donor::with('user');

            if ($request->blood_type) {
                $query->where('blood_type', $request->blood_type);
            }

            if ($request->city) {
                $query->where('city', 'like', '%'.$request->city.'%');
            }

            if ($request->availability !== null) {
                $query->where('availability', $request->availability);
            }

            $donors = $query->paginate($request->per_page ?? 15);

            return $this->successResponse($donors, 'Donors retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $donor = Donor::with('user', 'donations', 'bloodRequests')->findOrFail($id);

            return $this->successResponse($donor, 'Donor retrieved successfully');
        } catch (\Exception $e) {
            return $this->notFoundResponse('Donor not found');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $donor = Donor::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'blood_type' => 'nullable|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
                'city' => 'nullable|string|max:255',
                'availability' => 'nullable|boolean',
                'medical_history' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $donor->update($request->only([
                'blood_type', 'city', 'availability', 'medical_history', 'latitude', 'longitude',
            ]));

            return $this->successResponse($donor, 'Donor profile updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function updateAvailability(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'availability' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $donor = Donor::findOrFail($id);
            $donor->update(['availability' => $request->availability]);

            return $this->successResponse($donor, 'Availability updated');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = Donor::with('user');

            if ($request->blood_type) {
                $query->where('blood_type', $request->blood_type);
            }

            if ($request->city) {
                $query->where('city', 'like', '%'.$request->city.'%');
            }

            if ($request->availability !== null) {
                $query->where('availability', $request->availability);
            }

            $donors = $query->get();

            if ($request->latitude && $request->longitude) {
                $lat = (float) $request->latitude;
                $lon = (float) $request->longitude;
                $maxDistance = (float) ($request->distance ?? 25);

                $donors = $donors->filter(function ($donor) use ($lat, $lon, $maxDistance) {
                    if ($donor->latitude === null || $donor->longitude === null) {
                        return false;
                    }
                    $donor->distance = $this->haversineDistance($lat, $lon, $donor->latitude, $donor->longitude);

                    return $donor->distance < $maxDistance;
                })->sortBy('distance')->values();
            }

            return $this->successResponse($donors, 'Search results');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getNearbyDonors(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'distance' => 'nullable|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $distance = $request->distance ?? 25;
            $lat = (float) $request->latitude;
            $lon = (float) $request->longitude;

            $donors = Donor::where('availability', true)
                ->with('user')
                ->get()
                ->filter(function ($donor) use ($lat, $lon, $distance) {
                    if ($donor->latitude === null || $donor->longitude === null) {
                        return false;
                    }
                    $donor->distance = $this->haversineDistance($lat, $lon, $donor->latitude, $donor->longitude);

                    return $donor->distance < $distance;
                })
                ->sortBy('distance')
                ->values();

            return $this->successResponse($donors, 'Nearby donors');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function getDonationHistory($donorId)
    {
        try {
            $donor = Donor::findOrFail($donorId);
            $donations = $donor->donations()
                ->with('bloodRequest')
                ->orderByDesc('created_at')
                ->get();

            return $this->successResponse($donations, 'Donation history');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getPendingRequests($donorId)
    {
        try {
            $donor = Donor::findOrFail($donorId);
            $bloodType = $donor->blood_type;

            $respondedIds = DonorResponse::where('donor_id', $donor->id)->pluck('blood_request_id');

            $compatibleTypes = self::getDonatableBloodTypes($bloodType);

            $requests = BloodRequest::where('status', 'open')
                ->whereIn('blood_type', $compatibleTypes)
                ->whereNotIn('id', $respondedIds)
                ->with('hospital')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            return $this->successResponse($requests, 'Pending requests');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function respondToRequest(Request $request, $donorId, $requestId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,rejected',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $donor = Donor::findOrFail($donorId);
            $bloodRequest = BloodRequest::findOrFail($requestId);

            DonorResponse::create([
                'donor_id' => $donor->id,
                'blood_request_id' => $bloodRequest->id,
                'status' => $request->status,
                'response_date' => now(),
            ]);

            return $this->successResponse(null, $request->status === 'accepted' ? 'Donation accepted' : 'Request declined');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $donor = Donor::findOrFail($id);
            $donor->delete();

            return $this->successResponse(null, 'Donor deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
