<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $registrasionDetails = $request->session()->pull('registrasion_details', []);

            $user = User::updateOrCreate(
                [
                    'email' => $googleUser->getEmail(),
                ],
                [
                    'full_name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'profile_picture_url' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'opd_id' => $registrasionDetails['opd_id'] ?? null,
                    'district_id' => $registrasionDetails['district_id'] ?? null,
                ]
            );

            $freshUser = User::find($user->id);

            $token = $freshUser->createToken('GoogleAuthToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Authentication successful',
                'data' => [
                    'user' => $freshUser,
                    'token' => $token,
                ],
                'errors' => null,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'errors' => $e->getMessage(),
            ], 401);
        }
    }
}
