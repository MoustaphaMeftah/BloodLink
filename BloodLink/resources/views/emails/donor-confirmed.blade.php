<!DOCTYPE html>
<html>
<head><title>Donation Confirmed</title></head>
<body style="font-family:sans-serif;padding:20px;">
    @php $appointment = \App\Models\Appointment::where('blood_request_id', $response->blood_request_id)->where('donor_id', $response->donor->user_id)->latest()->first(); @endphp
    <h2>Hello {{ $response->donor->user->name ?? 'Donor' }},</h2>
    <p>Your donation offer has been <strong>accepted</strong> by the hospital. An appointment has been scheduled.</p>
    <ul>
        <li><strong>Blood Type:</strong> {{ $response->bloodRequest->blood_type }}</li>
        <li><strong>Quantity:</strong> {{ $response->bloodRequest->quantity }}ml</li>
        <li><strong>Hospital:</strong> {{ $response->bloodRequest->hospital->name ?? 'N/A' }}</li>
        <li><strong>Location:</strong> {{ $response->bloodRequest->location }}</li>
        @if ($appointment)
            <li><strong>Appointment:</strong> {{ $appointment->scheduled_date->format('l, F j, Y \a\t g:i A') }}</li>
            @if ($appointment->notes)
                <li><strong>Notes:</strong> {{ $appointment->notes }}</li>
            @endif
        @endif
    </ul>
    <p>Please arrive on time at the hospital to complete your donation. Thank you for saving lives!</p>
</body>
</html>
