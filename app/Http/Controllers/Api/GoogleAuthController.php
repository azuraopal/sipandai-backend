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

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('google_id', $googleUser->getId())->first();

            if(!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();

                if($user) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }

            if(!$user) {
                $user = User::create([
                    'google_id' => $googleUser->getId(),
                    'full_name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Authentication successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
                'errors' => null,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'data' => null,
                'errors' => $e->getMessage(),
            ], 401);
        }
    }
}
