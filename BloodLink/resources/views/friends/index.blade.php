<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends - BloodLink</title>
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
                <h3><i class="fas fa-user-friends"></i> Friends</h3>
            </div>
            <div class="page-actions">
                <a href="{{ route('friends.find') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-user-plus me-1"></i> Find People
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($pendingRequests->isNotEmpty())
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-clock me-2"></i> Pending Friend Requests
                    <span class="badge bg-dark ms-2">{{ $pendingRequests->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach ($pendingRequests as $req)
                            <div class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($req->requester->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $req->requester->name }}</strong>
                                        <div class="small text-muted">{{ $req->requester->email }}</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <form method="POST" action="{{ route('friends.accept', $req->requester) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Accept</button>
                                    </form>
                                    <form method="POST" action="{{ route('friends.decline', $req->requester) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times"></i> Decline</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if ($sentRequests->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header text-muted">
                    <i class="fas fa-paper-plane me-2"></i> Sent Requests
                    <span class="badge bg-secondary ms-2">{{ $sentRequests->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach ($sentRequests as $req)
                            <div class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($req->requested->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $req->requested->name }}</strong>
                                        <div class="small text-muted">{{ $req->requested->email }}</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <span class="badge bg-secondary"><i class="fas fa-clock me-1"></i> Pending</span>
                                    <form method="POST" action="{{ route('friends.cancel', $req->requested) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Cancel Request"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-friends me-2 text-danger"></i> My Friends
                <span class="badge bg-danger ms-2">{{ $friends->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if ($friends->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-user-friends" style="font-size:2.5rem;opacity:0.3;margin-bottom:1rem;display:block;"></i>
                        <h5>No Friends Yet</h5>
                        <p>Find people to connect with and start messaging.</p>
                        <a href="{{ route('friends.find') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-search me-1"></i> Find People
                        </a>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($friends as $friend)
                            <div class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($friend->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $friend->name }}</strong>
                                        <div class="small text-muted">
                                            {{ $friend->email }}
                                            <span class="badge bg-{{ $friend->role === 'admin' ? 'dark' : ($friend->role === 'hospital' ? 'info' : 'success') }}" style="font-size:0.6rem;">{{ ucfirst($friend->role) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('messages.show', $friend) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-envelope"></i> Message
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-friend-btn"
                                        data-bs-toggle="modal" data-bs-target="#removeFriendModal"
                                        data-friend-id="{{ $friend->id }}" data-friend-name="{{ $friend->name }}">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>

<!-- Remove Friend Modal -->
<div class="modal fade" id="removeFriendModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="fas fa-user-minus text-danger me-2"></i>Remove Friend</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="width:64px;height:64px;border-radius:50%;background:rgba(220,53,69,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="fas fa-exclamation-triangle" style="font-size:1.5rem;color:#dc3545;"></i>
                </div>
                <p class="mb-1 fw-bold">Remove <span id="removeFriendName" class="text-danger"></span>?</p>
                <p class="small text-muted mb-0">They won't appear in your friends list anymore.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="" id="removeFriendForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-user-minus me-1"></i>Remove</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('removeFriendModal');
    const baseUrl = '{{ route("friends.remove", "__ID__") }}';
    modal.addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('removeFriendName').textContent = btn.dataset.friendName;
        document.getElementById('removeFriendForm').action = baseUrl.replace('__ID__', btn.dataset.friendId);
    });
});
</script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
