<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Friend;
use App\Mail\VerifyEmailMail;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{
    use ApiResponse;

    // ==================== WEB METHODS ====================

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function showResetPassword(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
                }
                return back()->withErrors(['email' => 'Invalid credentials']);
            }

            if (!$user->hasVerifiedEmail()) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Please verify your email first'], 403);
                }
                return back()->withErrors(['email' => 'Please verify your email first']);
            }

            if ($request->wantsJson()) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => $user,
                    'token' => $token
                ], 200);
            }

            Auth::attempt($request->only('email', 'password'), $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect($this->dashboardUrl($user));
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:donor,hospital,patient',
            'city' => 'required|string|max:255',
            'blood_type' => 'nullable|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $verificationCode = Str::random(60);

            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'city' => $request->city,
                'email_verified_at' => null,
                'verification_code' => $verificationCode,
            ]);

            if ($request->role === 'donor') {
                $user->donor()->create([
                    'blood_type' => $request->blood_type,
                    'city' => $request->city,
                    'availability' => true,
                ]);
            }

            if ($request->role === 'hospital') {
                $user->hospital()->create([
                    'name' => $user->name,
                    'address' => $request->city,
                    'phone' => $request->phone,
                    'contact_person' => $user->name,
                ]);
            }

            try {
                Mail::to($user->email)->send(new VerifyEmailMail($user, $verificationCode));
            } catch (\Exception $e) {
                // Log email error but don't block registration
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful. Please verify your email.',
                    'user' => $user
                ], 201);
            }

            Auth::login($user);
            return redirect($this->dashboardUrl($user))->with('success', 'Registration successful.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function logout(Request $request)
    {
        if ($request->wantsJson() && $request->user()) {
            if ($request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }
            return response()->json(['success' => true, 'message' => 'Logged out successfully'], 200);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::where('email', $request->email)->first();
            $resetToken = Str::random(60);
            $user->update(['password_reset_token' => $resetToken]);

            Mail::to($user->email)->send(new ResetPasswordMail($user, $resetToken));

            return back()->with('success', 'Password reset link sent to your email.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::where('password_reset_token', $request->token)->first();

            if (!$user) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Invalid reset token'], 401);
                }
                return back()->withErrors(['token' => 'Invalid reset token']);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'password_reset_token' => null,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Password reset successfully'], 200);
            }

            return redirect()->route('login')->with('success', 'Password reset successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function getUser(Request $request)
    {
        return response()->json(['success' => true, 'user' => $request->user()], 200);
    }

    public function verifyEmail(Request $request, string $token = null)
    {
        $code = $token ?? $request->verification_code;

        if (!$code) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Verification code is required'], 422);
            }
            return redirect()->route('login')->withErrors(['error' => 'Verification code is required']);
        }

        try {
            $user = User::where('verification_code', $code)->first();

            if (!$user) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Invalid verification code'], 401);
                }
                return redirect()->route('login')->withErrors(['error' => 'Invalid verification code']);
            }

            $user->update([
                'email_verified_at' => now(),
                'verification_code' => null,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Email verified successfully'], 200);
            }
            Auth::login($user);
            return redirect($this->dashboardUrl($user))->with('success', 'Email verified successfully. Welcome!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('login')->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            $resetToken = Str::random(60);
            $user->update(['password_reset_token' => $resetToken]);

            Mail::to($user->email)->send(new ResetPasswordMail($user, $resetToken));

            return response()->json(['success' => true, 'message' => 'Password reset link sent to your email'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================== PROFILE METHODS ====================

    public function showProfile()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update($request->only(['name', 'phone', 'city']));

        return back()->with('success', 'Profile updated successfully.');
    }

    // ==================== MESSAGE METHODS ====================

    public function showMessages()
    {
        $userId = Auth::id();

        $conversationUserIds = Message::where('sender_id', $userId)
            ->select('receiver_id')
            ->union(
                Message::where('receiver_id', $userId)->select('sender_id')
            )
            ->pluck('receiver_id')
            ->merge(
                Message::where('receiver_id', $userId)->pluck('sender_id')
            )
            ->unique()
            ->values()
            ->toArray();

        $authUser = Auth::user();
        $conversations = User::whereIn('id', $conversationUserIds)->get()->map(function ($user) use ($userId) {
            $user->latest_message = Message::where(function ($q) use ($userId, $user) {
                $q->where('sender_id', $userId)->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($userId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $userId);
            })->orderByDesc('created_at')->first();

            return $user;
        })->filter(function ($user) {
            return $user->latest_message !== null;
        })->filter(function ($user) use ($authUser) {
            if ($authUser->role === 'admin') return true;
            if ($user->role === 'admin') return true;
            return Friend::areFriends($authUser->id, $user->id);
        })->sortByDesc(function ($user) {
            return $user->latest_message->created_at;
        })->values();

        return view('messages.index', compact('conversations'));
    }

    public function showConversation(User $user)
    {
        $userId = Auth::id();

        $messages = Message::where(function ($q) use ($userId, $user) {
            $q->where('sender_id', $userId)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($userId, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $userId);
        })->orderBy('created_at')->get();

        Message::where('sender_id', $user->id)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.show', compact('messages', 'user'));
    }

    public function sendMessage(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (Auth::user()->role !== 'admin' && $user->role !== 'admin' && !Friend::areFriends(Auth::id(), $user->id)) {
            return back()->with('error', 'You can only send messages to your friends.');
        }

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Message sent.');
    }

    private function dashboardUrl(User $user): string
    {
        return match ($user->role) {
            'donor' => route('donor.dashboard'),
            'hospital' => route('hospital.dashboard'),
            'admin' => route('admin.dashboard'),
            default => route('profile'),
        };
    }
}
