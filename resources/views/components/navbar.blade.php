<header
    class="fixed top-0 left-0 right-0 h-[66px] bg-white border-b border-gray-200 z-50 flex items-center justify-between px-6">
    <div class="flex items-center gap-4">
        <div
            class="w-[38px] h-[38px] bg-[#f05123] rounded-lg text-white font-bold flex items-center justify-center text-sm">
            <a href="{{ route('home') }}" class="flex h-full w-full items-center justify-center">CST</a>
        </div>
        @if (request()->routeIs('home'))
            <a href="{{ route('home') }}"
                class="flex items-center text-[#808080] hover:text-[#292929] text-lg font-bold transition-colors">
                HỌC ĐỂ ĐI LÀM
            </a>
        @else
            <button type="button" data-topbar-back
                class="flex items-center text-[#808080] hover:text-[#292929] text-sm font-semibold transition-colors"><svg
                    xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chevron-left mr-1" aria-hidden="true">
                    <path d="m15 18-6-6 6-6"></path>
                </svg>QUAY LẠI</button>
        @endif

    </div>
    <div class="flex-1 max-w-[420px] mx-8">
        <x-search.topbar-search />
    </div>
    @auth
        @php
            $authUser = Auth::user();
            $name = trim($authUser->name ?? 'User');
            $segments = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: ['U'];
            $first = mb_substr($segments[0], 0, 1);
            $last = mb_substr($segments[count($segments) - 1], 0, 1);
            $initials = strtoupper($first . $last);
            $avatarUrl = media_url($authUser->avatar_url ?? null);
            $role = (string) $authUser->getRoleNames()->first();
            $dashboardRouteName =
                $role === 'admin' ? 'admin.dashboard' : ($role === 'instructor' ? 'teacher.dashboard' : 'user.dashboard');
        @endphp
        <div class="flex items-center gap-4">
            <button type="button"
                class="w-9 h-9 rounded-full flex items-center justify-center text-[#666] hover:bg-[#f4f4f4] hover:text-[#242424] transition-colors"
                aria-label="Thông báo">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    fill="currentColor" class="text-current" aria-hidden="true">
                    <path
                        d="M12 22a2.3 2.3 0 0 0 2.3-2.3h-4.6A2.3 2.3 0 0 0 12 22Zm7.2-6.1-1.3-1.3v-4.2a5.9 5.9 0 0 0-4.8-5.8V3.8a1.1 1.1 0 1 0-2.2 0v.8a5.9 5.9 0 0 0-4.8 5.8v4.2l-1.3 1.3a.9.9 0 0 0 .6 1.5h13.2a.9.9 0 0 0 .6-1.5Z" />
                </svg>
            </button>

            <details class="relative group">
                <summary
                    class="list-none cursor-pointer w-9 h-9 overflow-hidden rounded-full bg-[#f6ad55] text-white text-sm font-bold flex items-center justify-center select-none shadow-sm ring-1 ring-black/5">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $name }}" class="h-full w-full object-cover">
                    @else
                        {{ $initials }}
                    @endif
                </summary>

                <div
                    class="absolute right-0 mt-3 w-56 rounded-xl border border-gray-200 bg-white p-2 shadow-[0_10px_30px_rgba(0,0,0,0.12)]">
                    <div class="px-3 py-2 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 shrink-0 overflow-hidden rounded-full bg-[#f6ad55] text-white text-sm font-bold flex items-center justify-center">
                                @if ($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ $name }}" class="h-full w-full object-cover">
                                @else
                                    {{ $initials }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-[#242424]">{{ $authUser->name }}</p>
                                <p class="truncate text-xs text-[#777]">{{ $authUser->email }}</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route($authUser->hasRole('student') ? 'user.profile.index' : $dashboardRouteName) }}"
                        class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm text-[#333] hover:bg-[#f6f6f6]">
                        Trang cá nhân
                    </a>
                    <a href="{{ route($dashboardRouteName) }}"
                        class="flex items-center rounded-lg px-3 py-2 text-sm text-[#333] hover:bg-[#f6f6f6]">
                        Trang điều khiển
                    </a>
                    <a href="{{ route('posts.create') }}"
                        class="flex items-center rounded-lg px-3 py-2 text-sm text-[#333] hover:bg-[#f6f6f6]">
                        Viết bài viết
                    </a>
                    <a href="{{ route('my-posts.index') }}"
                        class="flex items-center rounded-lg px-3 py-2 text-sm text-[#333] hover:bg-[#f6f6f6]">
                        Bài viết của tôi
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex items-center rounded-lg px-3 py-2 text-sm text-[#d14343] hover:bg-[#fff1f1]">
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </details>
        </div>
    @else
        <div class="flex items-center gap-4">
            <button type="button" class="text-[#444] text-sm font-semibold hover:text-[#292929]" data-auth-open="register">
                Đăng ký
            </button>
            <button type="button"
                class="bg-[#f05123] hover:bg-[#d8481f] text-white text-sm font-semibold py-[9px] px-5 rounded-full transition-colors"
                data-auth-open="login">
                Đăng nhập
            </button>
        </div>
    @endauth
</header>

@once
    @push('scripts')
        <script>
            document.addEventListener('click', function(event) {
                const trigger = event.target.closest('[data-topbar-back]');
                if (!trigger) {
                    return;
                }

                const fallbackUrl = @json(route('home'));
                const referrer = document.referrer ? new URL(document.referrer) : null;
                const canGoBack = referrer &&
                    referrer.origin === window.location.origin &&
                    referrer.href !== window.location.href &&
                    window.history.length > 1;

                if (canGoBack) {
                    window.history.back();
                    return;
                }

                window.location.href = fallbackUrl;
            });
        </script>
    @endpush
@endonce
