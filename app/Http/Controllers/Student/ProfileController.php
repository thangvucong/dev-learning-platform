<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * @var \App\Services\Student\StudentProfileService
     */
    protected StudentProfileService $profileService;

    /**
     * @param  \App\Services\Student\StudentProfileService  $profileService
     */
    public function __construct(StudentProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Show learning profile page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $payload = $this->profileService->build($request->user());

        return view('pages.student.profile.index', $payload);
    }

    /**
     * Update basic student profile fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'social_github' => ['nullable', 'url', 'max:255'],
            'social_linkedin' => ['nullable', 'url', 'max:255'],
            'social_portfolio' => ['nullable', 'url', 'max:255'],
        ]);

        $user = $request->user();
        $user->name = $validated['name'];
        $user->save();

        return back()->with('success', 'Đã cập nhật hồ sơ thành công.');
    }

    /**
     * Upload and update avatar image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $oldPath = null;
        if (!empty($user->avatar_url) && strpos($user->avatar_url, '/storage/') !== false) {
            $oldPath = ltrim((string) str_replace('/storage/', '', parse_url($user->avatar_url, PHP_URL_PATH)), '/');
        }

        $path = $validated['avatar']->store('avatars', 'public');
        $user->avatar_url = Storage::url($path);
        $user->save();

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return back()->with('success', 'Đã cập nhật ảnh đại diện.');
    }

    /**
     * Update account password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        if (!Hash::check($validated['current_password'], (string) $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Mật khẩu hiện tại chưa đúng.'])
                ->withInput();
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }

    /**
     * Update account settings placeholder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'timezone' => ['required', 'string', 'max:100'],
            'notifications_enabled' => ['nullable', 'in:0,1'],
            'weekly_report' => ['nullable', 'in:0,1'],
        ]);

        return back()->with('success', 'Đã lưu cài đặt tài khoản (placeholder cho iteration tiếp theo).');
    }
}

