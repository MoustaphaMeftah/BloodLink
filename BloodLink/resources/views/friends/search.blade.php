<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find People - BloodLink</title>
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
                <h3><i class="fas fa-search"></i> Find People</h3>
            </div>
            <div class="page-actions">
                <a href="{{ route('friends') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> My Friends
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

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            <option value="donor" {{ request('role') == 'donor' ? 'selected' : '' }}>Donor</option>
                            <option value="hospital" {{ request('role') == 'hospital' ? 'selected' : '' }}>Hospital</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-filter me-1"></i> Search
                        </button>
                    </div>
                    <div class="col-md-2">
                        @if (request('search') || request('role'))
                            <a href="{{ route('friends.find') }}" class="btn btn-outline-secondary w-100">Clear</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-users me-2 text-danger"></i> People
                <span class="badge bg-danger ms-2">{{ $users->total() }}</span>
            </div>
            <div class="card-body p-0">
                @if ($users->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-search" style="font-size:2.5rem;opacity:0.3;margin-bottom:1rem;display:block;"></i>
                        <h5>No Users Found</h5>
                        <p>Try a different search term or filter.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($users as $u)
                            @php
                                $isFriend = \App\Models\Friend::areFriends(Auth::id(), $u->id);
                                $hasSentPending = Auth::user()->hasSentRequestTo($u);
                                $hasPendingFrom = Auth::user()->hasPendingRequestFrom($u);
                            @endphp
                            <div class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $u->name }}</strong>
                                        <div class="small text-muted">
                                            <span class="badge bg-{{ $u->role === 'admin' ? 'dark' : ($u->role === 'hospital' ? 'info' : 'success') }}" style="font-size:0.6rem;">{{ ucfirst($u->role) }}</span>
                                            &middot; {{ $u->city ?? 'Unknown city' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    @if (Auth::user()->role === 'admin' || $u->role === 'admin' || $isFriend)
                                        <a href="{{ route('messages.show', $u) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-envelope"></i> Message
                                        </a>
                                    @elseif ($hasPendingFrom)
                                        <form method="POST" action="{{ route('friends.accept', $u) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-user-check"></i> Accept
                                            </button>
                                        </form>
                                    @elseif (!$hasSentPending)
                                        <form method="POST" action="{{ route('friends.send', $u) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-user-plus"></i> Add Friend
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary py-2">Pending</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @if ($users->hasPages())
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
