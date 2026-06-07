<?php

namespace App\Http\Controllers;

use App\Http\Requests\DonationStoreRequest;
use App\Models\Donor;
use App\Services\DonationService;
use App\Traits\ApiResponse;

class DonationController extends Controller
{
    use ApiResponse;

    protected DonationService $donationService;

    public function __construct(DonationService $donationService)
    {
        $this->donationService = $donationService;
    }

    public function store(DonationStoreRequest $request)
    {
        try {
            $donation = $this->donationService->recordDonation($request->validated());

            return $this->createdResponse($donation, 'Donation recorded successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function history(int $donorId)
    {
        try {
            $donor = Donor::findOrFail($donorId);
            $donations = $this->donationService->getDonationHistory($donor);

            return $this->successResponse($donations, 'Donation history retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function checkEligibility(int $donorId)
    {
        try {
            $donor = Donor::findOrFail($donorId);
            $eligibility = $this->donationService->canDonate($donor);

            return $this->successResponse($eligibility, 'Donation eligibility check');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
