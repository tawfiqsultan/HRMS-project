<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetCodeMail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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
                // 'userRole' => $request->user()->UserRole,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to login.',
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

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'UserEmail' => 'required|email|exists:users,UserEmail',
        ]);

        $code = rand(100000, 999999);
        $resetToken = Str::random(40);

        // Store code and email in cache
        Cache::put("reset_email_token_$resetToken", $request->UserEmail, now()->addMinutes(10));
        Cache::put("reset_code_{$request->email}", $code, now()->addMinutes(10));

        // Send email
        Mail::to($request->userEmail)->send(new ResetCodeMail($code));

        return response()->json([
            'message' => 'Verification code has been sent to your email.',
            'reset_token' => $resetToken,
            'code' => $code,
        ], 200);
    }



    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'reset_token' => 'required|string',
            'code' => 'required|numeric',
        ]);

        $email = Cache::get("reset_email_token_$request->reset_token");

        if (!$email) {
            return response()->json([
                'message' => 'Invalid or expired reset token.',
            ], 400);
        }

        $cachedCode = Cache::get("reset_code_$email");

        if ($cachedCode != $request->code) {
            return response()->json([
                'message' => 'Invalid verification code.',
            ], 400);
        }

        return response()->json([
            'message' => 'Verification successful.',
        ], 200);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'reset_token' => 'required|string',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required|min:6|same:password',
        ]);

        $email = Cache::get("reset_email_token_$request->reset_token");

        if (!$email) {
            return response()->json([
                'message' => 'Invalid or expired reset token.',
            ], 400);
        }

        $user = User::where('UserEmail', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Clean up
        Cache::forget("reset_email_token_$request->reset_token");
        Cache::forget("reset_code_$email");

        return response()->json([
            'message' => 'Password has been reset successfully.',
        ]);
    }
}
