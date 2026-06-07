<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Traits\ApiResponse;
use App\Traits\BloodCompatibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BloodRequestController extends Controller
{
    use ApiResponse, BloodCompatibility;

    public function index(Request $request)
    {
        try {
            $query = BloodRequest::with('hospital');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->blood_type) {
                $query->where('blood_type', $request->blood_type);
            }

            if ($request->urgency) {
                $query->where('urgency', $request->urgency);
            }

            $requests = $query->orderByDesc('created_at')->paginate(10);

            return $this->successResponse($requests, 'Blood requests retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hospital_id' => 'required|exists:hospitals,id',
            'blood_type' => 'required|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'quantity' => 'required|integer|min:1',
            'urgency' => 'required|in:low,medium,high,critical',
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $requestData = BloodRequest::create([
                'hospital_id' => $request->hospital_id,
                'blood_type' => $request->blood_type,
                'quantity' => $request->quantity,
                'urgency' => $request->urgency,
                'location' => $request->location,
                'status' => 'open',
            ]);

            return $this->createdResponse($requestData, 'Blood request created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $request = BloodRequest::with(['hospital', 'donors'])
                ->findOrFail($id);

            return $this->successResponse($request, 'Request retrieved successfully');
        } catch (\Exception $e) {
            return $this->notFoundResponse('Request not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'blood_type' => 'nullable|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'quantity' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:255',
            'urgency' => 'nullable|in:low,medium,high,critical',
            'status' => 'nullable|in:open,fulfilled,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $bloodRequest = BloodRequest::findOrFail($id);

            $bloodRequest->update($request->only([
                'blood_type', 'quantity', 'location', 'urgency', 'status',
            ]));

            return $this->successResponse($bloodRequest, 'Request updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $request = BloodRequest::findOrFail($id);
            $request->delete();

            return $this->successResponse(null, 'Request deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function compatibleDonors($id)
    {
        try {
            $request = BloodRequest::findOrFail($id);

            $compatibleBloodTypes = self::getCompatibleBloodTypes($request->blood_type);

            $donors = Donor::with('user')
                ->whereIn('blood_type', $compatibleBloodTypes)
                ->where('availability', true)
                ->get();

            return $this->successResponse($donors, 'Compatible donors found');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function markUrgent($id)
    {
        try {
            $request = BloodRequest::findOrFail($id);
            $request->update(['urgency' => 'critical']);

            return $this->successResponse($request, 'Request marked as urgent');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function markCompleted($id)
    {
        try {
            $request = BloodRequest::findOrFail($id);
            $request->update(['status' => 'fulfilled']);

            return $this->successResponse($request, 'Request completed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
