<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloodLink - Home</title>

    <!-- Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- CSS Laravel -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>

@include('partials.navbar')

<!-- HERO SECTION -->
<section class="text-white d-flex align-items-center"
    style="min-height: 100vh; background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);">

    <div class="container text-center">

        <h1 class="display-3 fw-bold">Save Lives with BloodLink</h1>

        <p class="lead mt-3">
            Connect donors, hospitals, and patients in real time.
        </p>

        <div class="mt-4">

            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg me-2 fw-bold" style="border-radius: 8px;">
                <i class="fas fa-sign-in-alt me-1"></i> Login
            </a>

            <a href="{{ route('register') }}?role=donor" class="btn btn-light btn-lg me-2 fw-bold" style="border-radius: 8px;">
                <i class="fas fa-heart me-1"></i> Become a Donor
            </a>

            <a href="{{ route('register') }}?role=hospital" class="btn btn-outline-light btn-lg fw-bold" style="border-radius: 8px;">
                <i class="fas fa-hospital me-1"></i> Register Hospital
            </a>

        </div>

    </div>

</section>

<!-- FEATURES -->
<section id="features" class="py-5">

    <div class="container">

        <h2 class="text-center mb-5">Why BloodLink?</h2>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="feature-card text-center p-4 shadow-sm">
                    <i class="fas fa-bolt text-danger fa-2x"></i>
                    <h5 class="mt-3">Fast Matching</h5>
                    <p>Find donors instantly in emergency cases.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card text-center p-4 shadow-sm">
                    <i class="fas fa-map-marker-alt text-danger fa-2x"></i>
                    <h5 class="mt-3">Nearby Donors</h5>
                    <p>Search donors near your location.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card text-center p-4 shadow-sm">
                    <i class="fas fa-shield-alt text-danger fa-2x"></i>
                    <h5 class="mt-3">Secure System</h5>
                    <p>All data is encrypted and protected.</p>
                </div>
            </div>

        </div>

    </div>

</section>

<!-- ABOUT -->
<section id="about" class="py-5 bg-light">

    <div class="container text-center">

        <h2>About BloodLink</h2>

        <p class="mt-3">
            BloodLink is a smart platform that connects blood donors with hospitals
            to save lives quickly and efficiently.
        </p>

    </div>

</section>

<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-4">

    <p class="mb-0">© {{ date('Y') }} BloodLink. All rights reserved.</p>

</footer>

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>