<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $request->validate([
                'UserEmail' => 'required|string|email',
                'UserPassword' => 'required|string',
            ]);

            $user = User::where('UserEmail', $request->UserEmail)->first();

            if (!$user || !Hash::check($request->UserPassword, $user->UserPassword)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check in.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
                "user" => $request->user()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function updateEmail(Request $request)
    {
        try {
            $validated = $request->validate([
                'UserEmail' => 'required|string|email|max:255|unique:users,UserEmail',
                'ConfirmUserEmail' => 'required|string|email|max:255|same:UserEmail',
            ]);

            if ($request->UserEmail === $request->user()->UserEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'The new email must be different from the current email.',
                ], 400);
            }

            $request->user()->forceFill([
                'UserEmail' => $request->UserEmail,
            ])->save();

            return response()->json([
                'success' => true,
                'message' => 'Email updated successfully.',
                'user'    => $request->user()
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update email.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'UserPassword' => 'required|string|min:6',
                'ConfirmUserPassword' => 'required|string|min:6|same:UserPassword',
            ]);

            if (!Hash::check($request->current_password, $request->user()->UserPassword)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The current password is incorrect.',
                ], 400);
            }

            $request->user()->forceFill([
                'UserPassword' => Hash::make($request->UserPassword),
            ])->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully.',
                'user'    => $request->user()
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
