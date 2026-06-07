<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - BloodLink</title>
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
                <h3><i class="fas fa-bell"></i> Notifications</h3>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-bell me-2 text-danger"></i> All Notifications</span>
                <span class="badge bg-secondary ms-2">{{ $notifications->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if ($notifications->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-bell" style="font-size:2.5rem;opacity:0.3;margin-bottom:1rem;display:block;"></i>
                        <h5>No Notifications</h5>
                        <p>You're all caught up!</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($notifications as $n)
                            <div class="list-group-item d-flex align-items-center justify-content-between {{ !$n->read_status ? 'fw-bold' : 'text-muted' }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:40px;height:40px;border-radius:50%;background:{{ !$n->read_status ? 'rgba(220,53,69,0.1)' : 'rgba(108,117,125,0.1)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="fas {{ $n->type === 'friend_accepted' ? 'fa-user-check' : 'fa-bell' }}" style="color:{{ !$n->read_status ? '#dc3545' : '#6c757d' }};"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $n->title }}</strong>
                                        <div class="small">{{ $n->message }}</div>
                                        <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    @if (!$n->read_status)
                                    <form method="POST" action="{{ route('notifications.read', $n) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-check"></i> Mark Read</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
