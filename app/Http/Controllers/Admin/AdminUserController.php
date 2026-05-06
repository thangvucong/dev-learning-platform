<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * @var \App\Services\UserService
     */
    protected UserService $userService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\UserService  $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display admin users list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $filters = [
            'keyword' => $request->query('keyword', ''),
            'role' => $request->query('role', ''),
            'status' => $request->query('status', ''),
            'created_from' => $request->query('created_from', ''),
            'created_to' => $request->query('created_to', ''),
        ];

        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

        $users = $this->userService
            ->getAdminUserList($filters, $perPage)
            ->appends($request->query());

        return view('components.admin.user-list', [
            'users' => $users,
            'filters' => $filters,
            'stats' => $this->userService->getAdminUserStats(),
            'perPage' => $perPage,
        ]);
    }

    /**
     * Display user detail page.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user): View
    {
        $user->loadCount([
            'orders as purchased_courses_count' => function ($query) {
                $query->where('status', 'paid');
            },
            'assignedClasses as joined_classes_count',
        ]);

        return view('components.admin.user-detail', [
            'user' => $user,
        ]);
    }

    /**
     * Display edit user form.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user): View
    {
        return view('components.admin.user-edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update user basic profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,teacher,student'],
            'is_active' => ['required', 'boolean'],
        ]);

        $user->update($validated);

        toastr('Cập nhật người dùng thành công.', 'success');

        return redirect()->route('admin.users.index');
    }

    /**
     * Toggle lock/unlock for a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        $this->userService->toggleActive($user);

        $message = $user->is_active ? 'Đã mở khóa tài khoản.' : 'Đã khóa tài khoản.';
        toastr($message, 'success');

        return back();
    }

    /**
     * Remove user from system.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        if ((int) auth()->id() === (int) $user->id) {
            toastr('Không thể xóa chính tài khoản đang đăng nhập.', 'error');

            return back();
        }

        $user->delete();

        toastr('Đã xóa người dùng.', 'success');

        return redirect()->route('admin.users.index');
    }
}

