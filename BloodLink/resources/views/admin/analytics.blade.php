<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

<div class="container mt-5 pt-4">
    <h3><i class="fas fa-chart-bar"></i> Analytics</h3>
    <hr>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Monthly Donations ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    @if ($monthlyDonations->isEmpty())
                        <p class="text-muted">No data available.</p>
                    @else
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Donations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthlyDonations as $month => $count)
                                    <tr>
                                        <td>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</td>
                                        <td>{{ $count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Requests by Blood Type</h5>
                </div>
                <div class="card-body">
                    @if ($requestsByType->isEmpty())
                        <p class="text-muted">No data available.</p>
                    @else
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Blood Type</th>
                                    <th>Requests</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requestsByType as $type => $count)
                                    <tr>
                                        <td><span class="badge bg-danger">{{ $type }}</span></td>
                                        <td>{{ $count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
