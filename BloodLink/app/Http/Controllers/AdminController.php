<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BloodRequest;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users_count' => User::count(),
            'donors_count' => User::where('role', 'donor')->count(),
            'hospitals_count' => User::where('role', 'hospital')->count(),
            'requests_count' => BloodRequest::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function manageUsers(Request $request)
    {
        $query = User::query();

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function showAnalytics()
    {
        $monthlyDonations = Donation::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('count', 'month');

        $requestsByType = BloodRequest::selectRaw('blood_type, COUNT(*) as count')
            ->groupBy('blood_type')
            ->pluck('count', 'blood_type');

        return view('admin.analytics', compact('monthlyDonations', 'requestsByType'));
    }

    public function approveUser(User $user)
    {
        $user->update(['email_verified_at' => now()]);
        return back()->with('success', 'User approved successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:donor,hospital,admin,patient',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update($request->only(['name', 'email', 'role']));

        return back()->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function manageRequests(Request $request)
    {
        $query = BloodRequest::with('hospital.user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->blood_type) {
            $query->where('blood_type', $request->blood_type);
        }

        if ($request->urgency) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('location', 'like', "%{$request->search}%")
                  ->orWhereHas('hospital.user', function ($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        $requests = $query->orderByDesc('created_at')->paginate(15);

        return view('admin.requests', compact('requests'));
    }

    public function updateRequestStatus(Request $request, BloodRequest $bloodRequest)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:open,fulfilled,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $bloodRequest->update(['status' => $request->status]);

        return back()->with('success', 'Request status updated to ' . $request->status . '.');
    }
}
