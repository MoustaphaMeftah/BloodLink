<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile()
    {
        return response()->json([
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $user->update($request->only([
            'name',
            'phone',
            'city'
        ]));

        return response()->json([
            'message' => 'Profile updated',
            'user' => $user
        ]);
    }
}
