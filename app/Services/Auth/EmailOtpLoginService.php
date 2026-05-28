<?php

namespace App\Services\Auth;

use App\Mail\CheckoutLoginOtpMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class EmailOtpLoginService
{
    /**
     * Cache key prefix for hashed OTP storage.
     */
    public const CACHE_PREFIX = 'auth_email_otp:';

    /**
     * OTP time-to-live in seconds (5 minutes).
     */
    public const TTL_SECONDS = 300;

    /**
     * Build a deterministic cache key for the normalized email.
     *
     * @param  string  $email
     * @return string
     */
    public function cacheKey(string $email): string
    {
        return self::CACHE_PREFIX . sha1(strtolower(trim($email)));
    }

    /**
     * Generate a 6-digit numeric OTP (zero-padded).
     *
     * @return string
     */
    public function generatePlainOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Hash the plain OTP using SHA-256 (hex).
     *
     * @param  string  $plainOtp
     * @return string
     */
    public function hashOtp(string $plainOtp): string
    {
        return hash('sha256', $plainOtp);
    }

    /**
     * Store hashed OTP in cache and send the plain code by email.
     *
     * @param  string  $email
     * @return void
     */
    public function send(string $email): void
    {
        $plain = $this->generatePlainOtp();
        $hash = $this->hashOtp($plain);

        Cache::put(
            $this->cacheKey($email),
            $hash,
            now()->addSeconds(self::TTL_SECONDS)
        );

        Mail::to($email)->send(new CheckoutLoginOtpMail($plain));
    }

    /**
     * Validate plain OTP against cache; on success remove the cache entry.
     *
     * @param  string  $email
     * @param  string  $plainOtp
     * @return bool
     */
    public function pullIfValid(string $email, string $plainOtp): bool
    {
        $key = $this->cacheKey($email);
        $stored = Cache::get($key);

        if (!is_string($stored) || $stored === '') {
            return false;
        }

        if (!hash_equals($stored, $this->hashOtp($plainOtp))) {
            return false;
        }

        Cache::forget($key);

        return true;
    }
}
