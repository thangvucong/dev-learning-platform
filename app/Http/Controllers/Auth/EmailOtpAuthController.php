<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendEmailOtpRequest;
use App\Http\Requests\Auth\VerifyEmailOtpRequest;
use App\Models\User;
use App\Services\Auth\EmailOtpLoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class EmailOtpAuthController extends Controller
{
    /**
     * @var \App\Services\Auth\EmailOtpLoginService
     */
    protected EmailOtpLoginService $emailOtpLoginService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\Auth\EmailOtpLoginService  $emailOtpLoginService
     */
    public function __construct(EmailOtpLoginService $emailOtpLoginService)
    {
        $this->emailOtpLoginService = $emailOtpLoginService;
    }

    /**
     * Send a 6-digit OTP to the given email (passwordless login).
     *
     * @param  \App\Http\Requests\Auth\SendEmailOtpRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCode(SendEmailOtpRequest $request): JsonResponse
    {
        $email = strtolower(trim($request->validated()['email']));

        try {
            $this->emailOtpLoginService->send($email);
        } catch (Throwable $e) {
            Log::error('send-code mail failed', [
                'email' => $email,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => __('Không thể gửi mã OTP. Vui lòng thử lại sau.'),
            ], 503);
        }

        return response()->json([
            'message' => __('Mã OTP đã được gửi.'),
        ]);
    }

    /**
     * Verify OTP, create user if needed, and log the user in.
     *
     * @param  \App\Http\Requests\Auth\VerifyEmailOtpRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyCode(VerifyEmailOtpRequest $request): JsonResponse
    {
        $data = $request->validated();
        $email = strtolower(trim($data['email']));
        $code = $data['code'];

        if (!$this->emailOtpLoginService->pullIfValid($email, $code)) {
            return response()->json([
                'message' => __('Mã OTP không hợp lệ hoặc đã hết hạn.'),
            ], 422);
        }

        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            $localPart = Str::before($email, '@');
            $name = $localPart !== '' ? $localPart : 'User';

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(64)),
                'email_verified_at' => now(),
            ]);

            $user->assignRole('student');
        } elseif (!$user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, false);

        return response()->json([
            'message' => __('Đăng nhập thành công.'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }
}
