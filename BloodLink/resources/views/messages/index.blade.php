<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

<div class="container mt-5 pt-4">
    <h3><i class="fas fa-envelope"></i> Messages</h3>
    <hr>

    @if ($conversations->isEmpty())
        <div class="alert alert-info">No conversations yet.</div>
    @else
        <div class="list-group">
            @foreach ($conversations as $conversation)
                <a href="{{ route('messages.show', $conversation) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $conversation->name }}</strong>
                        <small class="text-muted d-block">{{ $conversation->email }}</small>
                    </div>
                    <small class="text-muted">{{ $conversation->latest_message?->created_at?->diffForHumans() }}</small>
                </a>
            @endforeach
        </div>
    @endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
