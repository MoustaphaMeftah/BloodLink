<?php

namespace App\Services;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Notifications\BloodRequestNotification;
use App\Traits\BloodCompatibility;
use Illuminate\Support\Facades\Notification;

class BloodRequestService
{
    use BloodCompatibility;

    public function getCompatibleDonors(BloodRequest $bloodRequest)
    {
        $compatibleBloodTypes = self::getCompatibleBloodTypes($bloodRequest->blood_type);

        return Donor::with('user')
            ->whereIn('blood_type', $compatibleBloodTypes)
            ->where('availability', true)
            ->where('contact_verified', true)
            ->get();
    }

    public function notifyCompatibleDonors(BloodRequest $bloodRequest): int
    {
        $donors = $this->getCompatibleDonors($bloodRequest);
        $notified = 0;

        foreach ($donors as $donor) {
            $bloodRequest->donors()->attach($donor->id, [
                'status' => 'pending'
            ]);
            $notified++;

            if ($donor->user) {
                Notification::send($donor->user, new BloodRequestNotification($bloodRequest));
            }
        }

        return $notified;
    }

    public function canMarkUrgent(BloodRequest $bloodRequest): array
    {
        $urgentToday = BloodRequest::where('hospital_id', $bloodRequest->hospital_id)
            ->where('urgency', 'critical')
            ->whereDate('created_at', now())
            ->count();

        if ($urgentToday >= 5) {
            return [
                'can_mark' => false,
                'reason' => 'Maximum 5 critical requests per day for this hospital'
            ];
        }

        return [
            'can_mark' => true,
            'reason' => 'Request can be marked as critical'
        ];
    }
}
