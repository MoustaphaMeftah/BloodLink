<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

<div class="container mt-5 pt-4">
    <h3><i class="fas fa-tachometer-alt"></i> Donor Dashboard</h3>
    <hr>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fas fa-droplet fa-3x"></i>
                    <h5 class="mt-2">Blood Type</h5>
                    <h3>{{ Auth::user()->donor?->blood_type ?? 'N/A' }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-3x"></i>
                    <h5 class="mt-2">Status</h5>
                    <h3>{{ Auth::user()->donor?->availability ? 'Available' : 'Unavailable' }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-3x"></i>
                    <h5 class="mt-2">Last Donation</h5>
                    <h5>{{ Auth::user()->donor->last_donation_date?->format('Y-m-d') ?? 'No donations yet' }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
