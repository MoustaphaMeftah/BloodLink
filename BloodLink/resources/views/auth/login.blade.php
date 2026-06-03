<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BloodLink</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- TON CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow-lg p-4" style="width: 400px; border-radius: 12px;">

        <div class="text-center mb-4">
            <i class="fas fa-droplet text-danger" style="font-size: 40px;"></i>
            <h3 class="mt-2">BloodLink Login</h3>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button class="btn btn-danger w-100">
                Login
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="/register">Create account</a>
        </div>

    </div>

</div>

</body>
</html>