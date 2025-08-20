<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const VERIFY_TTL_MINUTES = 5;
    private const RESET_TTL_MINUTES = 5;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = null;
            $plainCode = null;

            $data = $validator->validated();
            $data['email'] = mb_strtolower(trim($data['email']));

            DB::transaction(function () use (&$user, &$plainCode, $data) {
                $user = User::create([
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);

                $plainCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->verification_code_hash = Hash::make($plainCode);
                $user->verification_code_expires_at = now()->addMinutes(self::VERIFY_TTL_MINUTES);
                $user->save();
            });

            Mail::to($user->email)->send((new EmailVerificationMail($user, $plainCode)));

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil. Silakan cek email Anda untuk kode verifikasi.',
                'data' => null,
                'errors' => null,
            ], 201);
        } catch (Exception $e) {
            Log::error('Registration failed: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal. Silakan coba lagi nanti.',
                'data' => null,
                'errors' => null,
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', Password::min(8)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = mb_strtolower(trim($request->input('email')));
        $rateKey = 'login:'.sha1($request->ip().'|'.$email);

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            throw ValidationException::withMessages(['throttle' => ['Too many attempts. Please try again later.']])->status(429);
        }
        RateLimiter::hit($rateKey, 60);

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages(['email' => ['Kredensial tidak valid.']]);
        }

        if (is_null($user->email_verified_at)) {
            throw ValidationException::withMessages(['email' => ['Silakan verifikasi email terlebih dahulu.']]);
        }

        RateLimiter::clear($rateKey);

        $token = $user->createToken('web:'.substr((string) $request->userAgent(), 0, 40), ['*'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User logged in successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_label' => $user->role_label,
                ],
                'token' => $token,
            ],
            'errors' => null,
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_label' => $user->role_label,
                    'email_verified_at' => $user->email_verified_at,
                ],
            ],
            'errors' => null,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out',
            'data' => null,
            'errors' => null,
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = mb_strtolower(trim($request->input('email')));
        $rateKey = 'verify:'.sha1($request->ip().'|'.$email);

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            throw ValidationException::withMessages(['throttle' => ['Too many attempts. Please try again later.']])->status(429);
        }
        RateLimiter::hit($rateKey, 60);

        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi tidak valid atau kedaluwarsa.',
                'data' => null,
                'errors' => null,
            ], 422);
        }

        if ($user->email_verified_at) {
            RateLimiter::clear($rateKey);
            return response()->json([
                'success' => true,
                'message' => 'Email sudah terverifikasi.',
                'data' => null,
                'errors' => null,
            ]);
        }

        if (!$user->verification_code_expires_at || $user->verification_code_expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi tidak valid atau kedaluwarsa.',
                'data' => null,
                'errors' => null,
            ], 422);
        }

        if (!Hash::check($request->input('code'), (string) $user->verification_code_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi salah.',
                'data' => null,
                'errors' => null,
            ], 422);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'verification_code_hash' => null,
            'verification_code_expires_at' => null,
        ])->save();

        RateLimiter::clear($rateKey);

        $token = $user->createToken('web:'.substr((string) $request->userAgent(), 0, 40), ['*'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token,
            ],
            'errors' => null,
        ]);
    }

    public function resendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = mb_strtolower(trim($request->input('email')));
        $rateKey = 'resend:'.sha1($request->ip().'|'.$email);

        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            throw ValidationException::withMessages(['throttle' => ['Too many attempts. Please try again later.']])->status(429);
        }
        RateLimiter::hit($rateKey, 3600);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'Jika email terdaftar, kode verifikasi baru telah dikirim.',
                'data' => null,
                'errors' => null,
            ]);
        }

        if ($user->email_verified_at) {
            RateLimiter::clear($rateKey);
            return response()->json([
                'success' => true,
                'message' => 'Email sudah terverifikasi.',
                'data' => null,
                'errors' => null,
            ]);
        }

        $plainCode = null;

        try {
            DB::transaction(function () use ($user, &$plainCode) {
                $plainCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->verification_code_hash = Hash::make($plainCode);
                $user->verification_code_expires_at = now()->addMinutes(self::VERIFY_TTL_MINUTES);
                $user->save();
            });

            Mail::to($user->email)->send((new EmailVerificationMail($user, $plainCode)));

            RateLimiter::clear($rateKey);

            return response()->json([
                'success' => true,
                'message' => 'Kode verifikasi baru telah dikirim.',
                'data' => null,
                'errors' => null,
            ]);
        } catch (Exception $e) {
            Log::warning('Resend verification failed: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ulang email verifikasi.',
                'data' => null,
                'errors' => null,
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', Password::min(8)],
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
                'data' => null,
                'errors' => null,
            ], 401);
        }

        DB::transaction(function () use ($user, $request) {
            $user->password = Hash::make($request->input('new_password'));
            $user->save();
            $user->tokens()->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
            'data' => null,
            'errors' => null,
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = mb_strtolower(trim($request->input('email')));
        $rateKey = 'forgot:'.sha1($request->ip().'|'.$email);

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            throw ValidationException::withMessages(['throttle' => ['Too many attempts. Please try again later.']])->status(429);
        }
        RateLimiter::hit($rateKey, 600);

        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        if ($user) {
            $plainCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            DB::transaction(function () use ($email, $plainCode) {
                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $email],
                    ['token' => Hash::make($plainCode), 'created_at' => now()]
                );
            });

            try {
                Mail::to($user->email)->send((new ForgotPasswordMail($user, $plainCode)));
            } catch (Exception $e) {
                Log::warning('ForgotPassword mail failed: '.$e->getMessage());
            }
        }

        RateLimiter::clear($rateKey);

        return response()->json([
            'success' => true,
            'message' => 'Jika email terdaftar, kode verifikasi telah dikirim ke email Anda.',
            'data' => null,
            'errors' => null,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'code' => ['required', 'string', 'size:6'],
            'password' => [
                'required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = mb_strtolower(trim($request->input('email')));

        $tokenData = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$tokenData || Carbon::parse($tokenData->created_at)->addMinutes(self::RESET_TTL_MINUTES)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi tidak valid atau telah kedaluwarsa.',
                'data' => null,
                'errors' => null,
            ], 400);
        }

        if (!Hash::check($request->input('code'), (string) $tokenData->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi salah.',
                'data' => null,
                'errors' => null,
            ], 422);
        }

        DB::transaction(function () use ($email, $request) {
            User::where('email', $email)->update(['password' => Hash::make($request->input('password'))]);
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            if ($user = User::where('email', $email)->first()) {
                $user->tokens()->delete();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset.',
            'data' => null,
            'errors' => null,
        ]);
    }
}
