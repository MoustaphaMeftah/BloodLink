<!DOCTYPE html>
<html>
<head><title>New Matching Request</title></head>
<body style="font-family:sans-serif;padding:20px;">
    <h2>Hello {{ $donor->name }},</h2>
    <p>A new blood request matching your type has been posted:</p>
    <ul>
        <li><strong>Blood Type:</strong> {{ $bloodRequest->blood_type }}</li>
        <li><strong>Quantity:</strong> {{ $bloodRequest->quantity }}ml</li>
        <li><strong>Urgency:</strong> {{ ucfirst($bloodRequest->urgency) }}</li>
        <li><strong>Location:</strong> {{ $bloodRequest->location }}</li>
        <li><strong>Hospital:</strong> {{ $bloodRequest->hospital?->name ?? 'N/A' }}</li>
    </ul>
    <p>
        <a href="{{ route('donor.requests') }}" style="background:#dc3545;color:white;padding:10px 20px;text-decoration:none;border-radius:6px;">View Request</a>
    </p>
    <p>Thank you for being a life-saver!</p>
</body>
</html>
