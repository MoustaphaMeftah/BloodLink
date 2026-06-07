<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversation - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

<div class="container mt-5 pt-4">
    <a href="{{ route('messages') }}" class="btn btn-outline-secondary mb-3">&larr; Back</a>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Conversation with {{ $user->name }}</h5>
        </div>
        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            @foreach ($messages as $msg)
                <div class="mb-3 {{ $msg->sender_id === Auth::id() ? 'text-end' : '' }}">
                    <div class="d-inline-block p-3 rounded-3 {{ $msg->sender_id === Auth::id() ? 'bg-danger text-white' : 'bg-light' }}" style="max-width: 70%;">
                        <div>{{ $msg->content }}</div>
                        <small class="{{ $msg->sender_id === Auth::id() ? 'text-white-50' : 'text-muted' }}">
                            {{ $msg->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card-footer">
            <form method="POST" action="{{ route('messages.send', $user) }}">
                @csrf
                <div class="input-group">
                    <textarea name="content" class="form-control" rows="1" placeholder="Type your message..." required></textarea>
                    <button type="submit" class="btn btn-danger">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
