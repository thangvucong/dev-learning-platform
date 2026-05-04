<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Session key used to persist post-oauth redirect target.
     */
    private const OAUTH_INTENDED_URL_SESSION_KEY = 'auth.google.intended_url';

    /**
     * Build Google Socialite driver with redirect URI from config (must match Google Cloud exactly).
     *
     * @return \Laravel\Socialite\Two\GoogleProvider
     */
    protected function googleDriver()
    {
        return Socialite::driver('google')
            ->redirectUrl(config('services.google.redirect'))
            ->scopes(['openid', 'email', 'profile']);
    }

    /**
     * Redirect the guest to Google OAuth and store intended return URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(Request $request): RedirectResponse
    {
        $intended = $this->resolveIntendedUrl($request);
        $request->session()->put(self::OAUTH_INTENDED_URL_SESSION_KEY, $intended);

        return $this->googleDriver()->redirect();
    }

    /**
     * Handle Google OAuth callback: verify email, find or create user, log in, return to checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()->to($this->pullIntendedUrl($request))
                ->with('oauth_error', __('Google sign-in was cancelled or denied.'));
        }

        try {
            $socialUser = $this->googleDriver()->user();
        } catch (Throwable $e) {
            Log::warning('Google OAuth callback failed', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->to($this->pullIntendedUrl($request))
                ->with('oauth_error', __('Unable to complete Google sign-in. Please try again.'));
        }

        if (!$this->isGoogleEmailVerified($socialUser)) {
            return redirect()->to($this->pullIntendedUrl($request))
                ->with('oauth_error', __('Your Google account email must be verified to continue.'));
        }

        $email = strtolower(trim((string) $socialUser->getEmail()));
        if ($email === '') {
            return redirect()->to($this->pullIntendedUrl($request))
                ->with('oauth_error', __('Google did not return an email address.'));
        }

        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            $name = $socialUser->getName() ?: (Str::before($email, '@') ?: 'User');
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(64)),
                'email_verified_at' => now(),
                'avatar_url' => $socialUser->getAvatar(),
            ]);

            try {
                $user->assignRole('student');
            } catch (Throwable $e) {
                Log::warning('assignRole student failed after Google signup', [
                    'user_id' => $user->id,
                    'message' => $e->getMessage(),
                ]);
            }
        } else {
            $updates = [];
            if (!$user->email_verified_at) {
                $updates['email_verified_at'] = now();
            }
            if (empty($user->avatar_url) && $socialUser->getAvatar()) {
                $updates['avatar_url'] = $socialUser->getAvatar();
            }
            if ($updates !== []) {
                $user->forceFill($updates)->save();
            }
        }

        Auth::login($user, false);

        return redirect()->to($this->pullIntendedUrl($request));
    }

    /**
     * Require Google to assert the email is verified before trusting the account.
     *
     * @param  \Laravel\Socialite\Contracts\User  $user
     * @return bool
     */
    protected function isGoogleEmailVerified(SocialiteUser $user): bool
    {
        $raw = $user->user ?? [];

        return (bool) Arr::get($raw, 'email_verified', Arr::get($raw, 'verified_email', false));
    }

    /**
     * Resolve internal URL to continue after OAuth from query param.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function resolveIntendedUrl(Request $request): string
    {
        $default = route('home');
        $candidate = (string) $request->query('continue', '');
        if ($candidate === '') {
            return $default;
        }

        if (Str::startsWith($candidate, '/')) {
            return url($candidate);
        }

        $parsedHost = parse_url($candidate, PHP_URL_HOST);
        $currentHost = parse_url(url('/'), PHP_URL_HOST);

        if ($parsedHost && $currentHost && strcasecmp((string) $parsedHost, (string) $currentHost) === 0) {
            return $candidate;
        }

        return $default;
    }

    /**
     * Get and forget intended URL from session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function pullIntendedUrl(Request $request): string
    {
        $stored = (string) $request->session()->pull(self::OAUTH_INTENDED_URL_SESSION_KEY, '');

        return $stored !== '' ? $stored : route('home');
    }
}
