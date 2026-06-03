<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\BloodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonorController extends Controller
{
    /**
     * Get all donors with filtering
     */
    public function index(Request $request)
    {
        try {
            $query = Donor::with('user');

            // Filter by blood type
            if ($request->blood_type) {
                $query->where('blood_type', $request->blood_type);
            }

            // Filter by city
            if ($request->city) {
                $query->where('city', 'like', '%' . $request->city . '%');
            }

            // Filter by availability
            if ($request->availability !== null) {
                $query->where('availability', $request->availability);
            }

            $donors = $query->paginate($request->per_page ?? 15);

            return response()->json(['success' => true, 'data' => $donors], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get donor by ID
     */
    public function show($id)
    {
        try {
            $donor = Donor::with('user', 'donations', 'requests')->findOrFail($id);

            return response()->json(['success' => true, 'data' => $donor], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Donor not found'], 404);
        }
    }

    /**
     * Update donor profile
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'blood_type' => 'nullable|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'availability' => 'nullable|boolean',
            'medical_history' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $donor = Donor::findOrFail($id);

            $donor->update($request->only([
                'blood_type',
                'city',
                'phone',
                'availability',
                'medical_history'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Donor profile updated successfully',
                'data' => $donor
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update donor availability
     */
    public function updateAvailability(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'availability' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $donor = Donor::findOrFail($id);
            $donor->update(['availability' => $request->availability]);

            return response()->json([
                'success' => true,
                'message' => 'Availability updated',
                'data' => $donor
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Search donors by criteria
     */
    public function search(Request $request)
    {
        try {
            $query = Donor::with('user');

            // Blood type filter
            if ($request->blood_type) {
                $query->where('blood_type', $request->blood_type);
            }

            // City filter
            if ($request->city) {
                $query->where('city', 'like', '%' . $request->city . '%');
            }

            // Availability filter
            if ($request->availability !== null) {
                $query->where('availability', $request->availability);
            }

            // Distance filter (if geolocation available)
            if ($request->latitude && $request->longitude) {
                // Using Haversine formula for distance calculation
                $query->selectRaw(
                    '*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                    cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                    sin(radians(latitude)))) AS distance',
                    [$request->latitude, $request->longitude, $request->latitude]
                )
                ->havingRaw('distance < ?', [$request->distance ?? 25])
                ->orderBy('distance');
            }

            $donors = $query->get();

            return response()->json(['success' => true, 'data' => $donors], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get nearby donors
     */
    public function getNearbyDonors(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'distance' => 'nullable|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $distance = $request->distance ?? 25; // Default 25 km

            $donors = Donor::selectRaw(
                '*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                sin(radians(latitude)))) AS distance',
                [$request->latitude, $request->longitude, $request->latitude]
            )
            ->where('availability', true)
            ->havingRaw('distance < ?', [$distance])
            ->orderBy('distance')
            ->with('user')
            ->get();

            return response()->json(['success' => true, 'data' => $donors], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get donation history
     */
    public function getDonationHistory($donorId)
    {
        try {
            $donor = Donor::findOrFail($donorId);
            $donations = $donor->donations()
                ->with('request')
                ->orderByDesc('created_at')
                ->get();

            return response()->json(['success' => true, 'data' => $donations], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get pending requests for donor
     */
    public function getPendingRequests($donorId)
    {
        try {
            $donor = Donor::findOrFail($donorId);
            $bloodType = $donor->blood_type;

            // Get requests matching donor's blood type
            $requests = BloodRequest::where('status', 'open')
                ->where('blood_type', $bloodType)
                ->with('hospital')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            return response()->json(['success' => true, 'data' => $requests], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Respond to blood request
     */
    public function respondToRequest(Request $request, $donorId, $requestId)
    {
        $validator = Validator::make($request->all(), [
            'accepted' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $donor = Donor::findOrFail($donorId);
            $bloodRequest = BloodRequest::findOrFail($requestId);

            // Record response
            $bloodRequest->donors()->attach($donorId, [
                'response' => $request->accepted ? 'accepted' : 'declined',
                'responded_at' => now()
            ]);

            // If accepted, send notification to hospital
            if ($request->accepted) {
                // TODO: Send notification to hospital
                // Notification::route('mail', $bloodRequest->hospital->email)
                //     ->notify(new DonorAcceptedNotification($donor, $bloodRequest));
            }

            return response()->json([
                'success' => true,
                'message' => $request->accepted ? 'Donation accepted' : 'Request declined'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete donor
     */
    public function destroy($id)
    {
        try {
            $donor = Donor::findOrFail($id);
            $donor->delete();

            return response()->json(['success' => true, 'message' => 'Donor deleted successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
