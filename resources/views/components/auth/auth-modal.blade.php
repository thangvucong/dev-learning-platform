@php
    $authOldForm = old('_auth_form');
    $authSessionForm = session('auth_modal');
    $authInitialTab = in_array($authOldForm, ['login', 'register'], true)
        ? $authOldForm
        : (in_array($authSessionForm, ['login', 'register'], true) ? $authSessionForm : 'login');
    $authOpenOnLoad = ($errors->any() && in_array($authOldForm, ['login', 'register'], true))
        || in_array($authSessionForm, ['login', 'register'], true);
@endphp

<div id="auth-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4" data-auth-modal="overlay"
    data-auth-initial-tab="{{ $authInitialTab }}" data-auth-open-on-load="{{ $authOpenOnLoad ? '1' : '0' }}"
    style="background-color: rgba(0, 0, 0, 0.55); backdrop-filter: blur(2px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[440px] overflow-hidden" data-auth-modal="content">
        <div class="flex items-center justify-between px-7 pt-6 pb-0">
            <div class="flex gap-1 bg-[#f5f5f5] p-1 rounded-xl">
                <button type="button"
                    class="px-5 py-[7px] rounded-lg text-sm font-semibold transition-all duration-200 bg-white text-[#292929] shadow-sm"
                    data-auth-switch="login">
                    Đăng nhập
                </button>
                <button type="button"
                    class="px-5 py-[7px] rounded-lg text-sm font-semibold transition-all duration-200 text-[#808080] hover:text-[#292929]"
                    data-auth-switch="register">
                    Đăng ký
                </button>
            </div>
            <button type="button"
                class="w-8 h-8 rounded-full flex items-center justify-center text-[#808080] hover:bg-[#f0f0f0] hover:text-[#292929] transition-colors"
                data-auth-close aria-label="Đóng">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>

        <div data-auth-panel="login">
            @include('components.auth._login-form')
        </div>
        <div data-auth-panel="register" class="hidden">
            @include('components.auth._register-form')
        </div>
    </div>
</div>
