<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BloodRequestNotification extends Notification
{
    use Queueable;

    public BloodRequest $bloodRequest;

    public function __construct(BloodRequest $bloodRequest)
    {
        $this->bloodRequest = $bloodRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Blood Request',
            'message' => "A {$this->bloodRequest->blood_type} blood request has been posted by {$this->bloodRequest->hospital->name}.",
            'type' => 'blood_request',
            'blood_request_id' => $this->bloodRequest->id,
        ];
    }
}
