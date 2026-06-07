<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Friend;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $friends = Auth::user()->acceptedFriends();

        $pendingRequests = Friend::where('friend_id', $userId)
            ->where('status', 'pending')
            ->with('requester')
            ->get();

        $sentRequests = Friend::where('user_id', $userId)
            ->where('status', 'pending')
            ->with('requested')
            ->get();

        return view('friends.index', compact('friends', 'pendingRequests', 'sentRequests'));
    }

    public function sendRequest(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot send a friend request to yourself.');
        }

        if (Friend::areFriends(Auth::id(), $user->id)) {
            return back()->with('error', 'You are already friends.');
        }

        if (Auth::user()->hasSentRequestTo($user)) {
            return back()->with('error', 'Friend request already sent.');
        }

        if (Auth::user()->hasPendingRequestFrom($user)) {
            Friend::where('user_id', $user->id)
                ->where('friend_id', Auth::id())
                ->update(['status' => 'accepted']);

            return back()->with('success', 'Friend request accepted!');
        }

        Friend::create([
            'user_id' => Auth::id(),
            'friend_id' => $user->id,
            'status' => 'pending',
        ]);

        ActivityLogger::log('send_friend_request', "Sent friend request to {$user->email}.", 'App\Models\User', $user->id);

        return back()->with('success', 'Friend request sent.');
    }

    public function acceptRequest(User $user)
    {
        $request = Friend::where('user_id', $user->id)
            ->where('friend_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $request->update(['status' => 'accepted']);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Friend Request Accepted',
            'message' => Auth::user()->name.' accepted your friend request.',
            'type' => 'friend_accepted',
        ]);

        ActivityLogger::log('accept_friend_request', "Accepted friend request from {$user->email}.", 'App\Models\User', $user->id);

        return back()->with('success', 'Friend request accepted.');
    }

    public function declineRequest(User $user)
    {
        $request = Friend::where('user_id', $user->id)
            ->where('friend_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $request->delete();

        ActivityLogger::log('decline_friend_request', "Declined friend request from {$user->email}.", 'App\Models\User', $user->id);

        return back()->with('success', 'Friend request declined.');
    }

    public function removeFriend(User $user)
    {
        Friend::where(function ($q) use ($user) {
            $q->where('user_id', Auth::id())->where('friend_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('friend_id', Auth::id());
        })->where('status', 'accepted')->delete();

        ActivityLogger::log('remove_friend', "Removed friend {$user->email}.", 'App\Models\User', $user->id);

        return back()->with('success', 'Friend removed.');
    }

    public function cancelRequest(User $user)
    {
        Friend::where('user_id', Auth::id())
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->delete();

        ActivityLogger::log('cancel_friend_request', "Cancelled friend request to {$user->email}.", 'App\Models\User', $user->id);

        return back()->with('success', 'Friend request cancelled.');
    }

    public function findPeople(Request $request)
    {
        $userId = Auth::id();
        $query = User::where('id', '!=', $userId);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(20);

        return view('friends.search', compact('users'));
    }

    public function notifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('friends.notifications', compact('notifications'));
    }

    public function markNotificationRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['read_status' => true]);

        return back()->with('success', 'Notification marked as read.');
    }
}
