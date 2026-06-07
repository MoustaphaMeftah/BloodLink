<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

<div class="container mt-5 pt-4">
    <h3><i class="fas fa-history"></i> Donation History</h3>
    <hr>

    @if ($donations->isEmpty())
        <div class="alert alert-info">No donations yet.</div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-danger">
                    <tr>
                        <th>Date</th>
                        <th>Quantity</th>
                        <th>Hospital</th>
                        <th>Blood Request</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($donations as $donation)
                        <tr>
                            <td>{{ $donation->donation_date?->format('Y-m-d') ?? $donation->created_at->format('Y-m-d') }}</td>
                            <td>{{ $donation->quantity }}ml</td>
                            <td>{{ $donation->bloodRequest?->hospital?->name ?? 'N/A' }}</td>
                            <td>{{ $donation->bloodRequest?->blood_type ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $donations->links() }}
    @endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
