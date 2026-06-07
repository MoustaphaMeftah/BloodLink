<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="bg-light">

@include('partials.navbar')

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh; padding-top: 80px;">
    <div class="card shadow-lg p-4" style="width: 420px; border-radius: 12px; border: none;">
        <div class="text-center mb-4">
            <div class="mx-auto bg-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                <i class="fas fa-droplet text-white" style="font-size: 28px;"></i>
            </div>
            <h3 class="mt-3 fw-bold">Forgot Password</h3>
            <p class="text-muted small">Enter your email to receive a reset link</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label fw-medium">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-envelope text-danger"></i></span>
                    <input type="email" name="email" id="email" class="form-control" placeholder="your@email.com" required>
                </div>
            </div>
            <button type="submit" class="btn btn-danger w-100 py-2 fw-bold" style="border-radius: 8px;">
                <i class="fas fa-paper-plane me-1"></i> Send Reset Link
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-danger text-decoration-none fw-medium">Back to Login</a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
