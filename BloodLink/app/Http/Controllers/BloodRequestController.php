<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BloodRequestController extends Controller
{
    /**
     * 🔵 Get all blood requests (with filters)
     */
    public function index(Request $request)
    {
        try {
            $query = BloodRequest::with('hospital');

            // Filter by status
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Filter by blood type
            if ($request->blood_type) {
                $query->where('blood_type', $request->blood_type);
            }

            // Filter urgent requests
            if ($request->urgent !== null) {
                $query->where('urgent', $request->urgent);
            }

            $requests = $query->orderByDesc('created_at')->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $requests
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🟢 Create a new blood request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hospital_id' => 'required|exists:users,id',
            'blood_type' => 'required|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'units_needed' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'urgent' => 'nullable|boolean',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $requestData = BloodRequest::create([
                'hospital_id' => $request->hospital_id,
                'blood_type' => $request->blood_type,
                'units_needed' => $request->units_needed,
                'location' => $request->location,
                'urgent' => $request->urgent ?? false,
                'notes' => $request->notes,
                'status' => 'open'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Blood request created successfully',
                'data' => $requestData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔍 Show single request
     */
    public function show($id)
    {
        try {
            $request = BloodRequest::with(['hospital', 'donors'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $request
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }
    }

    /**
     * ✏️ Update request
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'blood_type' => 'nullable|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'units_needed' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:255',
            'urgent' => 'nullable|boolean',
            'status' => 'nullable|in:open,processing,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bloodRequest = BloodRequest::findOrFail($id);

            $bloodRequest->update($request->only([
                'blood_type',
                'units_needed',
                'location',
                'urgent',
                'status',
                'notes'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Request updated successfully',
                'data' => $bloodRequest
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ❌ Delete request
     */
    public function destroy($id)
    {
        try {
            $request = BloodRequest::findOrFail($id);
            $request->delete();

            return response()->json([
                'success' => true,
                'message' => 'Request deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🧬 Get compatible donors for a request
     */
    public function getCompatibleDonors($id)
    {
        try {
            $request = BloodRequest::findOrFail($id);

            $donors = Donor::with('user')
                ->where('blood_type', $request->blood_type)
                ->where('availability', true)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $donors
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🚨 Mark request as urgent
     */
    public function markUrgent($id)
    {
        try {
            $request = BloodRequest::findOrFail($id);
            $request->update(['urgent' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Request marked as urgent'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Mark request as completed
     */
    public function markCompleted($id)
    {
        try {
            $request = BloodRequest::findOrFail($id);
            $request->update(['status' => 'completed']);

            return response()->json([
                'success' => true,
                'message' => 'Request completed successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
