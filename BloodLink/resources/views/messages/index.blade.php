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

<div class="dashboard-wrapper">
    <div class="dashboard-sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="dashboard-sidebar" id="dashboardSidebar">
        <div class="sidebar-title">Main Menu</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="sidebar-link">
            <i class="fas fa-users"></i> Manage Users
        </a>
        <a href="{{ route('admin.analytics') }}" class="sidebar-link">
            <i class="fas fa-chart-bar"></i> Analytics
        </a>
        <div class="sidebar-title">Communication</div>
        <a href="{{ route('messages') }}" class="sidebar-link active d-flex align-items-center justify-content-between">
            <span><i class="fas fa-envelope"></i> Messages</span>
            @php $unreadCount = \App\Models\Message::where('receiver_id', Auth::id())->whereNull('read_at')->count(); @endphp
            @if ($unreadCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
            @endif
        </a>
        <div class="sidebar-title">Account</div>
        <a href="{{ route('profile') }}" class="sidebar-link">
            <i class="fas fa-user-shield"></i> Admin Profile
        </a>
        <div class="mt-4 px-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="dashboard-content">
        <div class="page-header">
            <div>
                <button class="sidebar-toggle me-2" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h3><i class="fas fa-envelope"></i> Messages</h3>
            </div>
            <div class="page-actions">
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                    <i class="fas fa-pen me-1"></i> New Message
                </button>
            </div>
        </div>

        @if ($conversations->isEmpty())
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-comments"></i></div>
                        <h5>No Conversations Yet</h5>
                        <p>Your messages with donors and hospitals will appear here once you start communicating.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body p-0">
                    <div class="conversation-list">
                        @foreach ($conversations as $conversation)
                            <a href="{{ route('messages.show', $conversation) }}" class="text-decoration-none">
                                <div class="list-group-item d-flex align-items-center gap-3">
                                    <div class="conv-avatar flex-shrink-0">
                                        {{ strtoupper(substr($conversation->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-dark">{{ $conversation->name }}</strong>
                                            <small class="text-muted">{{ $conversation->latest_message?->created_at?->diffForHumans() }}</small>
                                        </div>
                                        <div class="text-muted small text-truncate">{{ $conversation->email }}</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted" style="font-size:0.8rem;"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @php $allUsers = \App\Models\User::where('id', '!=', Auth::id())->orderBy('name')->get(); @endphp
        <div class="modal fade" id="newMessageModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-pen me-2 text-danger"></i>New Message</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" class="form-control mb-3" id="recipientSearch" placeholder="Search users..." onkeyup="filterUsers()">
                        <div class="list-group" id="userList" style="max-height:350px;overflow-y:auto;">
                            @forelse ($allUsers as $u)
                                <a href="{{ route('messages.show', $u) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
                                    <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong class="d-block">{{ $u->name }}</strong>
                                        <small class="text-muted">{{ $u->email }} &middot; <span class="badge bg-{{ $u->role === 'admin' ? 'dark' : ($u->role === 'hospital' ? 'info' : 'success') }}" style="font-size:0.6rem;">{{ ucfirst($u->role) }}</span></small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted ms-auto" style="font-size:0.8rem;"></i>
                                </a>
                            @empty
                                <div class="text-center text-muted py-3">No other users found</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function filterUsers() {
                var input = document.getElementById('recipientSearch');
                var filter = input.value.toLowerCase();
                var list = document.getElementById('userList');
                var items = list.getElementsByTagName('a');
                for (var i = 0; i < items.length; i++) {
                    items[i].style.display = items[i].textContent.toLowerCase().includes(filter) ? '' : 'none';
                }
            }
        </script>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
