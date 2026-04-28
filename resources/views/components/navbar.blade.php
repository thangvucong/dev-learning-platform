<header
    class="fixed top-0 left-0 right-0 h-[66px] bg-white border-b border-gray-200 z-50 flex items-center justify-between px-6">
    <div class="flex items-center gap-4">
        <div
            class="w-[38px] h-[38px] bg-[#f05123] rounded-lg text-white font-bold flex items-center justify-center text-sm">
            F8</div><button
            class="flex items-center text-[#808080] hover:text-[#292929] text-sm font-semibold transition-colors"><svg
                xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-chevron-left mr-1" aria-hidden="true">
                <path d="m15 18-6-6 6-6"></path>
            </svg>QUAY LẠI</button>
    </div>
    <div class="flex-1 max-w-[420px] mx-8">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg
                    xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-search text-[#a0a0a0]" aria-hidden="true">
                    <path d="m21 21-4.34-4.34"></path>
                    <circle cx="11" cy="11" r="8"></circle>
                </svg></div><input
                class="block w-full pl-10 pr-3 py-2 border border-[#e8e8e8] rounded-full leading-5 bg-white placeholder-[#a0a0a0] focus:outline-none focus:border-[#d3d3d3] focus:ring-0 sm:text-sm"
                placeholder="Tìm kiếm khóa học, bài viết, video, ..." type="text">
        </div>
    </div>
    @auth
        @php
            $name = trim(Auth::user()->name ?? 'User');
            $segments = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: ['U'];
            $first = mb_substr($segments[0], 0, 1);
            $last = mb_substr($segments[count($segments) - 1], 0, 1);
            $initials = strtoupper($first . $last);
        @endphp
        <div class="flex items-center gap-4">
            <a href="{{ url('/courses') }}"
                class="text-[15px] font-semibold text-[#242424] hover:text-[#f05123] transition-colors">
                Khóa học của tôi
            </a>

            <button type="button"
                class="w-9 h-9 rounded-full flex items-center justify-center text-[#666] hover:bg-[#f4f4f4] hover:text-[#242424] transition-colors"
                aria-label="Thông báo">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"
                    class="text-current" aria-hidden="true">
                    <path
                        d="M12 22a2.3 2.3 0 0 0 2.3-2.3h-4.6A2.3 2.3 0 0 0 12 22Zm7.2-6.1-1.3-1.3v-4.2a5.9 5.9 0 0 0-4.8-5.8V3.8a1.1 1.1 0 1 0-2.2 0v.8a5.9 5.9 0 0 0-4.8 5.8v4.2l-1.3 1.3a.9.9 0 0 0 .6 1.5h13.2a.9.9 0 0 0 .6-1.5Z" />
                </svg>
            </button>

            <details class="relative group">
                <summary
                    class="list-none cursor-pointer w-9 h-9 rounded-full bg-[#f6ad55] text-white text-sm font-bold flex items-center justify-center select-none shadow-sm">
                    {{ $initials }}
                </summary>

                <div
                    class="absolute right-0 mt-3 w-56 rounded-xl border border-gray-200 bg-white p-2 shadow-[0_10px_30px_rgba(0,0,0,0.12)]">
                    <div class="px-3 py-2 border-b border-gray-100">
                        <p class="text-sm font-semibold text-[#242424]">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-[#777]">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="{{ url('/profile') }}"
                        class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm text-[#333] hover:bg-[#f6f6f6]">
                        Trang cá nhân
                    </a>
                    <a href="{{ url('/dashboard') }}"
                        class="flex items-center rounded-lg px-3 py-2 text-sm text-[#333] hover:bg-[#f6f6f6]">
                        Dashboard
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
