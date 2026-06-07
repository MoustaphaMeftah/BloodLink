<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponse;

    public function profile()
    {
        return $this->successResponse(Auth::user(), 'User profile');
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user->update($request->only(['name', 'phone', 'city']));

        return $this->successResponse($user, 'Profile updated');
    }
}
