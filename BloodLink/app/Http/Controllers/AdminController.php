<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BloodRequest;
use Illuminate\Http\Request;

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
}
