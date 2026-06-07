<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Helpers\LocationService;
use App\Models\ActivityLog;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Hospital;
use App\Models\User;
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
        ActivityLogger::log('approve_user', "Approved user {$user->email}.", 'App\Models\User', $user->id);

        return back()->with('success', 'User approved successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:donor,hospital,admin',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update($request->only(['name', 'email', 'role']));

        ActivityLogger::log('update_user', "Updated user {$user->email}.", 'App\Models\User', $user->id);

        return back()->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        ActivityLogger::log('delete_user', "Deleted user {$user->email}.", 'App\Models\User', $user->id);
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function exportCsv(Request $request)
    {
        $type = $request->query('type', 'users');
        $filename = $type.'_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($type) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            if ($type === 'users') {
                fputcsv($handle, ['Name', 'Email', 'Role', 'Phone', 'City', 'Verified', 'Joined']);
                User::chunk(200, function ($users) use ($handle) {
                    foreach ($users as $u) {
                        fputcsv($handle, [
                            $u->name, $u->email, $u->role, $u->phone ?? '',
                            $u->city ?? '', $u->email_verified_at ? 'Yes' : 'No',
                            $u->created_at->format('Y-m-d'),
                        ]);
                    }
                });
            } elseif ($type === 'donations') {
                fputcsv($handle, ['Donor', 'Blood Type', 'Quantity (ml)', 'Hospital', 'Date']);
                Donation::with(['donor.user', 'bloodRequest.hospital'])->chunk(200, function ($donations) use ($handle) {
                    foreach ($donations as $d) {
                        fputcsv($handle, [
                            $d->donor?->user?->name ?? 'N/A',
                            $d->bloodRequest?->blood_type ?? 'N/A',
                            $d->quantity,
                            $d->bloodRequest?->hospital?->name ?? 'N/A',
                            $d->donation_date?->format('Y-m-d') ?? $d->created_at->format('Y-m-d'),
                        ]);
                    }
                });
            } elseif ($type === 'requests') {
                fputcsv($handle, ['Blood Type', 'Quantity', 'Urgency', 'Status', 'Hospital', 'Location', 'Created']);
                BloodRequest::with('hospital.user')->chunk(200, function ($requests) use ($handle) {
                    foreach ($requests as $r) {
                        fputcsv($handle, [
                            $r->blood_type, $r->quantity, $r->urgency,
                            $r->status, $r->hospital?->user?->name ?? 'N/A',
                            $r->location, $r->created_at->format('Y-m-d'),
                        ]);
                    }
                });
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
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

        ActivityLogger::log('update_request_status', "Set request #{$bloodRequest->id} status to {$request->status}.", 'App\Models\BloodRequest', $bloodRequest->id);

        return back()->with('success', 'Request status updated to '.$request->status.'.');
    }

    public function showActivityLog(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(30);

        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.activity-log', compact('logs', 'actions'));
    }

    public function showMap()
    {
        $donors = Donor::with('user')
            ->where('availability', true)
            ->get()
            ->filter(fn ($d) => $d->latitude && $d->longitude)
            ->values();

        $requests = BloodRequest::with('hospital')
            ->where('status', 'open')
            ->get()
            ->filter(fn ($r) => $r->hospital && $r->hospital->latitude && $r->hospital->longitude)
            ->values();

        return view('admin.map', compact('donors', 'requests'));
    }
}
