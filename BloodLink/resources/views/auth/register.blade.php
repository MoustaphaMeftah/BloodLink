<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="bg-light">

@include('partials.navbar')

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh; padding-top: 80px;">
    <div class="card shadow-lg p-4" style="width: 540px; border-radius: 12px; border: none;">
        <div class="text-center mb-4">
            <div class="mx-auto bg-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                <i class="fas fa-droplet text-white" style="font-size: 28px;"></i>
            </div>
            <h3 class="mt-3 fw-bold">Join BloodLink</h3>
            <p class="text-muted small">Create your account and start saving lives</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label fw-medium">First Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-user text-danger"></i></span>
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="John" value="{{ old('first_name') }}" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label fw-medium">Last Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-user text-danger"></i></span>
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Doe" value="{{ old('last_name') }}" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-medium">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-envelope text-danger"></i></span>
                    <input type="email" name="email" id="email" class="form-control" placeholder="your@email.com" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label fw-medium">Phone</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-phone text-danger"></i></span>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="+212 6XX XX XX XX" value="{{ old('phone') }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label fw-medium">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-lock text-danger"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Min 8 characters" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label fw-medium">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-check-circle text-danger"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repeat password" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="roleSelect" class="form-label fw-medium">I want to register as</label>
                <select name="role" class="form-control" id="roleSelect" required>
                    <option value="donor" {{ request('role') == 'donor' || old('role') == 'donor' ? 'selected' : '' }}>Donor</option>
                    <option value="hospital" {{ request('role') == 'hospital' || old('role') == 'hospital' ? 'selected' : '' }}>Hospital</option>
                    <option value="patient" {{ request('role') == 'patient' || old('role') == 'patient' ? 'selected' : '' }}>Patient</option>
                </select>
            </div>

            <div class="mb-3" id="bloodTypeGroup" style="display: {{ request('role') == 'donor' || old('role') == 'donor' ? 'block' : 'none' }}">
                <label for="blood_type" class="form-label fw-medium">Blood Type</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-tint text-danger"></i></span>
                    <select name="blood_type" id="blood_type" class="form-control">
                        <option value="">Select Blood Type</option>
                        @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $bt)
                            <option value="{{ $bt }}" {{ old('blood_type') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                        @endforeach
                    </select>
                </div>
                <small class="text-muted">Required for donors</small>
            </div>

            <div class="mb-4">
                <label for="city" class="form-label fw-medium">City</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-city text-danger"></i></span>
                    <input type="text" name="city" id="city" class="form-control" placeholder="Your city" value="{{ old('city') }}" required>
                </div>
            </div>

            <button type="submit" class="btn btn-danger w-100 py-2 fw-bold mb-3" style="border-radius: 8px;">
                <i class="fas fa-user-plus me-1"></i> Create Account
            </button>
        </form>

        <div class="text-center">
            <p class="small text-muted mb-0">
                Already have an account?
                <a href="{{ route('login') }}" class="text-danger text-decoration-none fw-medium">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('roleSelect');
        const bloodTypeGroup = document.getElementById('bloodTypeGroup');
        function toggleBloodType() {
            bloodTypeGroup.style.display = roleSelect.value === 'donor' ? 'block' : 'none';
        }
        roleSelect.addEventListener('change', toggleBloodType);
        toggleBloodType();
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
