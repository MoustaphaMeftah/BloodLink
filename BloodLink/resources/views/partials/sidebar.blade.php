@php
    $role = Auth::user()->role;
    $route = Route::currentRouteName();
@endphp

<aside class="dashboard-sidebar" id="dashboardSidebar">
    <div class="sidebar-title">Main Menu</div>

    @if ($role === 'donor')
        <a href="{{ route('donor.dashboard') }}" class="sidebar-link {{ str_starts_with($route, 'donor.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('donor.requests') }}" class="sidebar-link {{ str_starts_with($route, 'donor.requests') ? 'active' : '' }}">
            <i class="fas fa-list"></i> Browse Requests
        </a>
        <a href="{{ route('donor.history') }}" class="sidebar-link {{ str_starts_with($route, 'donor.history') ? 'active' : '' }}">
            <i class="fas fa-history"></i> Donation History
        </a>
        <a href="{{ route('donor.nearby') }}" class="sidebar-link {{ str_starts_with($route, 'donor.nearby') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt"></i> Nearby Requests
        </a>
    @elseif ($role === 'hospital')
        <a href="{{ route('hospital.dashboard') }}" class="sidebar-link {{ str_starts_with($route, 'hospital.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('hospital.requests') }}" class="sidebar-link {{ $route === 'hospital.requests' || str_starts_with($route, 'hospital.request.show') || str_starts_with($route, 'hospital.request.update') ? 'active' : '' }}">
            <i class="fas fa-list"></i> My Requests
        </a>
        <a href="{{ route('hospital.request.create') }}" class="sidebar-link {{ str_starts_with($route, 'hospital.request.create') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i> Create Request
        </a>
        <a href="{{ route('hospital.nearby-donors') }}" class="sidebar-link {{ str_starts_with($route, 'hospital.nearby-donors') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt"></i> Nearby Donors
        </a>
    @elseif ($role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ str_starts_with($route, 'admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="sidebar-link {{ str_starts_with($route, 'admin.users') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Manage Users
        </a>
        <a href="{{ route('admin.requests') }}" class="sidebar-link {{ str_starts_with($route, 'admin.requests') ? 'active' : '' }}">
            <i class="fas fa-tint"></i> Blood Requests
        </a>
        <a href="{{ route('admin.analytics') }}" class="sidebar-link {{ str_starts_with($route, 'admin.analytics') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Analytics
        </a>
        <a href="{{ route('admin.activity-log') }}" class="sidebar-link {{ str_starts_with($route, 'admin.activity-log') ? 'active' : '' }}">
            <i class="fas fa-history"></i> Activity Log
        </a>
        <div class="sidebar-title">Maps</div>
        <a href="{{ route('admin.map') }}" class="sidebar-link {{ str_starts_with($route, 'admin.map') ? 'active' : '' }}">
            <i class="fas fa-map-marked-alt"></i> View Map
        </a>
    @endif

    @if ($role !== 'admin')
    <div class="sidebar-title">Social</div>
    <a href="{{ route('friends') }}" class="sidebar-link d-flex align-items-center justify-content-between {{ str_starts_with($route, 'friends') ? 'active' : '' }}">
        <span><i class="fas fa-user-friends"></i> Friends</span>
        @php $pendingFriendCount = \App\Models\Friend::where('friend_id', Auth::id())->where('status', 'pending')->count(); @endphp
        @if ($pendingFriendCount > 0)
            <span class="badge bg-danger rounded-pill">{{ $pendingFriendCount }}</span>
        @endif
    </a>
    @endif

    <div class="sidebar-title">Communication</div>
    <a href="{{ route('messages') }}" class="sidebar-link d-flex align-items-center justify-content-between {{ str_starts_with($route, 'messages') ? 'active' : '' }}">
        <span><i class="fas fa-envelope"></i> Messages</span>
        @php $unreadCount = \App\Models\Message::where('receiver_id', Auth::id())->whereNull('read_at')->count(); @endphp
        @if ($unreadCount > 0)
            <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
        @endif
    </a>

    <div class="sidebar-title">Account</div>
    <a href="{{ route('profile') }}" class="sidebar-link {{ str_starts_with($route, 'profile') ? 'active' : '' }}">
        <i class="fas fa-user-cog"></i>
        @if ($role === 'admin')
            Admin Profile
        @elseif ($role === 'hospital')
            Hospital Profile
        @else
            My Profile
        @endif
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
