<!DOCTYPE html>
<html>
<head><title>Donor Responded</title></head>
<body style="font-family:sans-serif;padding:20px;">
    <h2>Hello {{ $hospital->name }},</h2>
    <p>A donor has responded to your blood request:</p>
    <ul>
        <li><strong>Donor:</strong> {{ $response->donor?->user?->name ?? 'Anonymous' }}</li>
        <li><strong>Blood Type:</strong> {{ $response->donor?->blood_type ?? '?' }}</li>
        <li><strong>Status:</strong> {{ ucfirst($response->status) }}</li>
        @if ($response->notes)
            <li><strong>Notes:</strong> {{ $response->notes }}</li>
        @endif
    </ul>
    <p>
        <a href="{{ route('hospital.requests') }}" style="background:#dc3545;color:white;padding:10px 20px;text-decoration:none;border-radius:6px;">View Requests</a>
    </p>
</body>
</html>
