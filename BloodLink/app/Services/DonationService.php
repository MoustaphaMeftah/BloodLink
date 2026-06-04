<?php

namespace App\Services;

use App\Models\Donor;
use App\Models\Donation;
use Illuminate\Http\JsonResponse;

class DonationService
{
    public function canDonate(Donor $donor): array
    {
        if (!$donor->isDonationEligible()) {
            $daysUntilEligible = $donor->getDaysUntilEligible();
            return [
                'eligible' => false,
                'reason' => "Donor must wait {$daysUntilEligible} more days before donation",
                'days_until_eligible' => $daysUntilEligible
            ];
        }

        if (!$donor->availability) {
            return [
                'eligible' => false,
                'reason' => 'Donor is not available for donation'
            ];
        }

        return [
            'eligible' => true,
            'reason' => 'Donor is eligible for donation'
        ];
    }

    public function recordDonation(array $data): Donation
    {
        $donor = Donor::findOrFail($data['donor_id']);

        $eligibility = $this->canDonate($donor);
        if (!$eligibility['eligible']) {
            throw new \Exception($eligibility['reason']);
        }

        $donation = Donation::create($data);

        $donor->update([
            'last_donation_date' => $data['donation_date']
        ]);

        return $donation;
    }

    public function getDonationHistory(Donor $donor, int $perPage = 15)
    {
        return $donor->donations()
            ->with('bloodRequest')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
