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

<div class="dashboard-wrapper">
    <div class="dashboard-sidebar-overlay" id="sidebarOverlay"></div>
    @include('partials.sidebar')

    <main class="dashboard-content">
        <div class="page-header">
            <div>
                <button class="sidebar-toggle me-2" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h3><i class="fas fa-comment-dots"></i> {{ $user->name }}</h3>
            </div>
            <div class="page-actions">
                <a href="{{ route('messages') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> All Conversations
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex align-items-center gap-3">
                <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.9rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <strong>{{ $user->name }}</strong>
                    <div class="small text-muted">{{ $user->email }}</div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="chat-container">
                    <div class="chat-messages" id="chatMessages">
                        @foreach ($messages as $msg)
                            <div class="chat-bubble {{ $msg->sender_id === Auth::id() ? 'sent' : 'received' }}">
                                <div>{{ $msg->content }}</div>
                                <span class="chat-time">{{ $msg->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                        @if ($messages->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-comment-slash me-1"></i> No messages yet
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <form method="POST" action="{{ route('messages.send', $user) }}">
                    @csrf
                    <div class="input-group">
                        <textarea name="content" class="form-control" rows="1" placeholder="Type your message..." required style="resize:none;"></textarea>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-paper-plane me-1"></i> Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
