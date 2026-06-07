<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

<div class="container mt-5 pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-user"></i> My Profile</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email</label>
                            <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-medium">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="mb-3">
                            <label for="city" class="form-label fw-medium">City</label>
                            <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $user->city) }}">
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label fw-medium">Role</label>
                            <input type="text" id="role" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                        </div>
                        <button type="submit" class="btn btn-danger fw-bold">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
