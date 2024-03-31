<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Controller
{
    public function resetStudent(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'old_password' => 'required|string',
            'new_password' => 'required|string|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['error' => 'Incorrect old password'], 422);
        }

        $user->username = $request->username;
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => 'Password reset successful']);
    }
}
