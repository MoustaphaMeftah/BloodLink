<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    //Register a new user

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:donor,hospital,patient,admin',
            'city' => 'required|string|max:255',
            'blood_type' => 'nullable|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
        ]);
        //si les donnes n'est pas valide

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }


        try {
            // Create user
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'city' => $request->city,
                'email_verified_at' => null,
            ]);

            // If donor, create donor profile
            if ($request->role === 'donor') {
                $user->donor()->create([
                    'blood_type' => $request->blood_type,
                    'city' => $request->city,
                    'phone' => $request->phone,
                    'availability' => true,
                ]);
            }

            //  ENVOI EMAIL VERIFICATION (Laravel officiel)
            $user->sendEmailVerificationNotification();

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please verify your email.',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    //Login user

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
            }

            //  BLOQUER SI EMAIL NON VERIFIE
            if (!$user->hasVerifiedEmail()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your email first'
                ], 403);
            }

            // Generate token (using Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    // log out 

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json(['success' => true, 'message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // get curennt user 

    public function getUser(Request $request)
    {
        return response()->json(['success' => true, 'user' => $request->user()], 200);
    }

    // Verify email

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'verification_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = User::where('verification_code', $request->verification_code)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Invalid verification code'], 401);
            }

            $user->update([
                'email_verified_at' => now(),
                'verification_code' => null,
            ]);

            return response()->json(['success' => true, 'message' => 'Email verified successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    // Request password reset

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            // Generate reset token
            $resetToken = Str::random(60);

            $user->update(['password_reset_token' => $resetToken]);

            // Send reset email
            // Mail::send new ResetPasswordNotification($user, $resetToken);

            return response()->json(['success' => true, 'message' => 'Password reset link sent to your email'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    //reset password

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = User::where('password_reset_token', $request->token)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Invalid reset token'], 401);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'password_reset_token' => null,
            ]);

            return response()->json(['success' => true, 'message' => 'Password reset successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
