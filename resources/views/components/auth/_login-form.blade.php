<div class="px-7 pt-5 pb-7">
    <div style="opacity: 1; transform: none;">
        <h2 class="text-[20px] font-bold text-[#242424] mb-1">Xin chào!</h2>
        <p class="text-sm text-[#808080] mb-5">Đăng nhập để tiếp tục học tập tại F8.</p>
        <div class="flex flex-col gap-2 mb-5"><button
                class="w-full flex items-center justify-center gap-3 py-[10px] px-4 rounded-xl text-sm font-medium transition-all bg-white hover:bg-gray-50 border border-[#e0e0e0] text-[#3c3c3c]"><span
                    class="shrink-0"><svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true">
                        <path fill="#EA4335"
                            d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z">
                        </path>
                        <path fill="#4285F4"
                            d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z">
                        </path>
                        <path fill="#FBBC05"
                            d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z">
                        </path>
                        <path fill="#34A853"
                            d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.36-8.16 2.36-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z">
                        </path>
                        <path fill="none" d="M0 0h48v48H0z"></path>
                    </svg></span><span>Tiếp tục với Google</span></button><button
                class="w-full flex items-center justify-center gap-3 py-[10px] px-4 rounded-xl text-sm font-medium transition-all bg-[#1877f2] hover:bg-[#166fe5]  text-white"><span
                    class="shrink-0"><svg width="20" height="20" viewBox="0 0 24 24" fill="white"
                        aria-hidden="true">
                        <path
                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z">
                        </path>
                    </svg></span><span>Tiếp tục với Facebook</span></button><button
                class="w-full flex items-center justify-center gap-3 py-[10px] px-4 rounded-xl text-sm font-medium transition-all bg-[#24292e] hover:bg-[#1a1e22]  text-white"><span
                    class="shrink-0"><svg width="20" height="20" viewBox="0 0 24 24" fill="white"
                        aria-hidden="true">
                        <path
                            d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z">
                        </path>
                    </svg></span><span>Tiếp tục với Github</span></button></div>
        <div class="flex items-center gap-3 mb-5">
            <div class="flex-1 h-px bg-[#ebebeb]"></div><span class="text-xs text-[#a0a0a0]">Hoặc</span>
            <div class="flex-1 h-px bg-[#ebebeb]"></div>
        </div>
        @if (old('_auth_form') === 'login' && $errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate class="flex flex-col gap-4">
            @csrf
            <input type="hidden" name="_auth_form" value="login">

            <div class="flex flex-col gap-1"><label for="login-email"
                    class="text-sm font-semibold text-[#333]">Email</label><input id="login-email" name="email"
                    placeholder="Nhập địa chỉ email"
                    class="w-full px-4 py-[10px] rounded-xl border text-sm text-[#292929] placeholder-[#b0b0b0] outline-none transition-all focus:ring-2 focus:ring-[#f05123]/20 focus:border-[#f05123] {{ old('_auth_form') === 'login' && $errors->has('email') ? 'border-red-400' : 'border-[#e0e0e0]' }} bg-white hover:border-[#c0c0c0]"
                    type="email" value="{{ old('_auth_form') === 'login' ? old('email') : '' }}" required autofocus></div>
            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between"><label for="login-password"
                        class="text-sm font-semibold text-[#333]">Mật khẩu</label><button type="button"
                        class="text-xs text-[#f05123] font-medium hover:underline">Quên mật khẩu?</button></div>
                <div class="relative"><input id="login-password" name="password" placeholder="Nhập mật khẩu"
                        class="w-full px-4 py-[10px] pr-11 rounded-xl border text-sm text-[#292929] placeholder-[#b0b0b0] outline-none transition-all focus:ring-2 focus:ring-[#f05123]/20 focus:border-[#f05123] {{ old('_auth_form') === 'login' && $errors->has('password') ? 'border-red-400' : 'border-[#e0e0e0]' }} bg-white hover:border-[#c0c0c0]"
                        type="password" required autocomplete="current-password"><button type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#a0a0a0] hover:text-[#555] transition-colors"
                        aria-label="Hiện mật khẩu"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye" aria-hidden="true">
                            <path
                                d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                            </path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg></button></div>
            </div><button type="submit"
                class="w-full bg-[#f05123] hover:bg-[#d8481f] disabled:opacity-70 text-white font-semibold py-[11px] rounded-full text-sm transition-colors mt-1 flex items-center justify-center gap-2"><span>Đăng
                    nhập</span></button>
        </form>
        <p class="text-center text-sm text-[#808080] mt-5"><span>Chưa có tài khoản? </span><button type="button"
                class="text-[#f05123] font-semibold hover:underline" data-auth-switch="register">Đăng ký ngay</button></p>
    </div>
</div>
