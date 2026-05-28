@extends('layouts.app')

@section('title', 'Thanh toán — ' . ($checkout['title'] ?? ''))

@section('content')
    @php
        $checkoutThumbnailUrl = media_url(
            $checkout['thumbnail_url'] ?? null,
            'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=480&q=80'
        );
    @endphp

    <main class="ml-0 sm:ml-[96px] flex-1 flex justify-center items-start min-h-[calc(100vh-66px)]">
        <div class="w-full max-w-[1100px] flex flex-col lg:flex-row px-4 sm:px-6 lg:px-10 py-10">

            <div class="flex-1 min-w-0 lg:pr-10 pb-10 lg:pb-0">
                <h1 class="text-[28px] sm:text-[32px] font-bold text-[#2d2f31] mb-8">
                    Thanh toán
                </h1>

                @auth
                    <div class="border-t border-[#d1d7dc] py-6">
                        <div class="flex items-start gap-3">
                            <span
                                class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#e8f5e9] text-[#2e7d32] text-sm font-bold"
                                aria-hidden="true">✓</span>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-[18px] sm:text-[20px] font-bold text-[#2d2f31] mb-2">
                                    1. Đăng nhập hoặc đăng ký tài khoản
                                </h2>
                                <p class="text-[14px] text-[#6a6f73]">
                                    Đã hoàn thành — bạn đang đăng nhập với
                                    <strong class="text-[#2d2f31]">{{ Auth::user()->email }}</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div id="checkout-passwordless-root" data-send-url="{{ route('auth.send-code') }}"
                        data-verify-url="{{ route('auth.verify-code') }}">

                        @if (session('oauth_error'))
                            <div class="mb-4 rounded border border-[#f5c2c2] bg-[#fff4f4] px-4 py-3 text-[14px] text-[#b32d2d]"
                                role="alert">
                                {{ session('oauth_error') }}
                            </div>
                        @endif

                        <div id="checkout-step-logged-in" class="hidden border-t border-[#d1d7dc] py-6">
                            <div class="flex items-start gap-3">
                                <span
                                    class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#e8f5e9] text-[#2e7d32] text-sm font-bold"
                                    aria-hidden="true">✓</span>
                                <div class="min-w-0 flex-1">
                                    <h2 class="text-[18px] sm:text-[20px] font-bold text-[#2d2f31] mb-2">
                                        1. Đăng nhập hoặc đăng ký tài khoản
                                    </h2>
                                    <p
                                        class="text-[14px] text-[#1e4620] bg-[#e8f5e9] border border-[#c8e6c9] rounded px-4 py-3">
                                        <span id="checkout-logged-in-message"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div id="checkout-step-email" class="border-t border-[#d1d7dc] py-6">
                            <h2 class="text-[18px] sm:text-[20px] font-bold text-[#2d2f31] mb-4">
                                1. Đăng nhập hoặc đăng ký tài khoản
                            </h2>
                            <p class="text-[14px] text-[#6a6f73] mb-6 leading-relaxed">
                                Tài khoản của bạn là bắt buộc để truy cập khóa học đã mua. Vui lòng xác thực địa chỉ email của
                                bạn,
                                vì chúng tôi sẽ sử dụng nó để gửi xác nhận đơn hàng. Bằng cách đăng ký, bạn đồng ý với
                                <a href="#" class="text-[#f05123] underline">Điều khoản sử dụng</a>
                                và
                                <a href="#" class="text-[#f05123] underline">Chính sách bảo mật</a>.
                            </p>

                            <p id="checkout-email-error"
                                class="hidden mb-4 text-[14px] text-[#b32d2d] bg-[#fff4f4] border border-[#f5c2c2] rounded px-4 py-3"
                                role="alert"></p>

                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:gap-3 mb-6">
                                <div class="flex-1 min-w-0 w-full relative group">
                                    <input
                                        class="w-full h-[48px] px-4 pt-3 pb-1 border border-[#2d2f31] text-[15px] outline-none bg-white focus:border-[#5624d0] transition-colors peer"
                                        placeholder=" " id="email-input" type="email" value="">
                                    <label for="email-input"
                                        class="absolute left-4 bg-white px-1 text-[#6a6f73] transition-all duration-200 pointer-events-none
                                    peer-placeholder-shown:text-[15px] peer-placeholder-shown:top-[12px]
                                    peer-focus:text-[11px] peer-focus:top-[-9px] peer-focus:font-bold peer-focus:text-[#2d2f31]
                                    text-[11px] top-[-9px] font-bold text-[#2d2f31]">
                                        Email
                                    </label>
                                </div>

                                <div
                                    class="text-[14px] text-[#6a6f73] lg:px-1 flex-shrink-0 text-center lg:text-left lg:self-center">
                                    or</div>

                                <a href="{{ route('auth.google.redirect', ['continue' => request()->fullUrl()]) }}"
                                    class="inline-flex h-[48px] w-full lg:w-auto shrink-0 items-center justify-center gap-2 rounded-[2px] border border-[#dadce0] bg-white px-5 text-[15px] font-semibold text-[#3c4043] shadow-sm transition hover:bg-[#f8f9fa] hover:shadow"
                                    data-checkout-google-signin>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path
                                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                            fill="#4285F4"></path>
                                        <path
                                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                            fill="#34A853"></path>
                                        <path
                                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                            fill="#FBBC05"></path>
                                        <path
                                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                            fill="#EA4335"></path>
                                    </svg>
                                    Continue with Google
                                </a>
                            </div>

                            <div class="text-[13px] font-bold mb-4">Không cần mật khẩu</div>
                            <button type="button" id="checkout-email-continue"
                                class="w-full h-12 bg-[#1473e6] text-white font-bold text-[16px] hover:bg-[#105cba] transition-colors cursor-pointer flex items-center justify-center rounded-[2px] disabled:opacity-60 disabled:cursor-not-allowed">
                                <span id="checkout-email-continue-label">Continue</span>
                            </button>
                        </div>

                        <div id="checkout-step-otp" class="border-t border-[#d1d7dc] py-6 hidden">
                            <h2 class="text-[18px] sm:text-[20px] font-bold text-[#2d2f31] mb-4">
                                Verify your email
                            </h2>
                            <div>
                                <p id="checkout-otp-sent-message" class="text-[14px] text-[#2d2f31] mb-3 leading-relaxed">
                                    We sent a 6-digit code to <span id="checkout-otp-email-display"
                                        class="font-medium break-all"></span>.
                                </p>
                                <p class="mb-5">
                                    <button type="button" id="checkout-otp-change-email"
                                        class="text-[#1473e6] font-bold hover:text-[#105cba] underline underline-offset-2 text-[14px]">
                                        Change email
                                    </button>
                                </p>

                                <p id="checkout-otp-error"
                                    class="hidden mb-4 text-[14px] text-[#b32d2d] bg-[#fff4f4] border border-[#f5c2c2] rounded px-4 py-3"
                                    role="alert"></p>

                                <div class="relative mb-4">
                                    <label for="checkout-otp-input" class="sr-only">6-digit code</label>
                                    <input id="checkout-otp-input" name="otp" inputmode="numeric" maxlength="6"
                                        autocomplete="one-time-code"
                                        class="w-full h-[52px] px-4 border-2 border-[#2d2f31] text-[15px] outline-none bg-white tracking-[0.2em] font-medium rounded-[2px] focus:border-[#1473e6]"
                                        placeholder="000000" type="text" pattern="[0-9]{6}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[#6a6f73] pointer-events-none"
                                        aria-hidden="true">
                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2">
                                        </rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </div>
                                <button type="button" id="checkout-otp-continue"
                                    class="w-full h-12 bg-[#1473e6] text-white font-bold text-[16px] hover:bg-[#105cba] transition-colors cursor-pointer flex items-center justify-center rounded-[2px] mb-4 disabled:opacity-60 disabled:cursor-not-allowed">
                                    <span id="checkout-otp-continue-label">Continue</span>
                                </button>
                                <p class="text-[14px] text-[#6a6f73] text-center">
                                    <span>Didn't receive the code? </span>
                                    <button type="button" id="checkout-otp-resend"
                                        class="text-[#1473e6] font-bold hover:text-[#105cba] disabled:text-[#6a6f73] disabled:no-underline disabled:cursor-not-allowed">
                                        Resend code
                                    </button>
                                </p>
                            </div>
                        </div>

                    </div>
                @endauth

                <div id="checkout-step-billing"
                    class="border-t border-b border-[#d1d7dc] py-6 flex justify-between items-center gap-4 @guest opacity-60 pointer-events-none @endguest">
                    <h2 class="text-[16px] font-bold text-[#2d2f31]">2. Thực hiện thanh toán
                    </h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"
                        class="checkout-billing-lock-icon w-4 h-4 text-[#6a6f73] flex-shrink-0 @auth hidden @endauth"
                        aria-hidden="true">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>

                @auth
                    @if (session('onepay_error'))
                        <div class="mt-4 rounded border border-[#f5c2c2] bg-[#fff4f4] px-4 py-3 text-[14px] text-[#b32d2d]"
                            role="alert">
                            {{ session('onepay_error') }}
                        </div>
                    @endif

                    @if (!empty($onepay))
                        <div class="border-t border-[#d1d7dc] py-8">
                            <h2 class="text-[18px] sm:text-[20px] font-bold text-[#2d2f31] mb-4">
                                Thanh toán thẻ OnePay
                            </h2>
                            <p
                                class="text-[14px] text-[#2d2f31] mb-4 leading-relaxed bg-[#f7f9fa] border border-[#d1d7dc] rounded px-4 py-3">
                                Chọn loại thẻ để chuyển sang cổng OnePay và hoàn tất thanh toán an toàn.
                            </p>
                            <div class="flex flex-col gap-3">
                                @foreach ($onepay['available_methods'] as $onepayMethod)
                                    <form method="POST" action="{{ $onepay['start_url'] }}">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $onepay['course_id'] }}">
                                        <input type="hidden" name="method" value="{{ $onepayMethod['method'] }}">
                                        <button type="submit"
                                            class="w-full h-12 bg-[#5624d0] text-white font-bold text-[15px] hover:bg-[#401b9b] transition-colors rounded-[2px]">
                                            Thanh toán qua {{ $onepayMethod['label'] }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endauth

                <div class="mt-10">
                    <h3 class="text-[16px] font-bold text-[#2d2f31] mb-6">Chi tiết đơn hàng (1 khóa học)</h3>
                    <div class="flex gap-4 items-start rounded-xl border border-[#e5e7eb] bg-white p-4 shadow-sm">
                        <img alt="{{ $checkout['title'] }}"
                            class="w-16 h-16 rounded-lg object-cover border border-[#d1d7dc] flex-shrink-0"
                            src="{{ $checkoutThumbnailUrl }}">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-[15px] font-bold leading-tight text-[#2d2f31] mb-1 line-clamp-2">
                                {{ $checkout['title'] }}
                            </h4>
                            @if (!empty($checkout['has_discount']))
                                <span
                                    class="inline-flex items-center rounded-full bg-[#fff1e8] px-2 py-0.5 text-[12px] font-bold text-[#f05123]">
                                    Đã giảm {{ $checkout['discount_formatted'] }}
                                </span>
                            @endif
                        </div>
                        <div class="flex-shrink-0 text-right">
                            @if (!empty($checkout['has_discount']))
                                <div class="text-[13px] text-[#6a6f73]"
                                    style="text-decoration: line-through; text-decoration-thickness: 1px;">
                                    {{ $checkout['original_price_formatted'] }}
                                </div>
                            @endif
                            <div class="text-[16px] font-bold text-[#2d2f31]">{{ $checkout['line_price_formatted'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="w-full lg:w-[380px] xl:w-[420px] flex-shrink-0 border-t lg:border-t-0 lg:border-l border-[#d1d7dc] pt-8 lg:pt-0 lg:pl-10">
                <div class="lg:sticky lg:top-[98px]">
                    <div class="rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm">
                        <h2 class="text-[22px] sm:text-[24px] font-bold text-[#2d2f31] mb-6">Tổng quan đơn hàng</h2>

                        <div class="space-y-4 text-[15px]">
                            <div class="flex justify-between items-center gap-4">
                                <span class="text-[#6a6f73]">Giá gốc:</span>
                                <span class="text-[#2d2f31]">{{ $checkout['original_price_formatted'] }}</span>
                            </div>
                            @if (!empty($checkout['has_discount']))
                                <div class="flex justify-between items-center gap-4">
                                    <span class="text-[#6a6f73]">Giảm giá:</span>
                                    <span
                                        class="font-semibold text-[#16a34a]">-{{ $checkout['discount_formatted'] }}</span>
                                </div>
                            @endif
                        </div>

                        <hr class="border-[#d1d7dc] my-5">

                        <div class="flex justify-between items-start gap-4 mb-2">
                            <span class="text-[16px] font-bold text-[#2d2f31]">Tổng cộng</span>
                            <span
                                class="text-[24px] font-black leading-none text-[#f05123]">{{ $checkout['total_formatted'] }}</span>
                        </div>
                        <p class="text-[13px] text-[#6a6f73]">Áp dụng cho 1 khóa học. Bạn sẽ được chuyển sang cổng thanh
                            toán sau khi xác nhận.</p>
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var checkoutAuthenticated = @json(auth()->check());

            /**
             * Mở khóa bước 2 (thanh toán) sau khi đăng nhập (OTP hoặc đã đăng nhập từ server).
             */
            function unlockCheckoutBillingStep() {
                var billing = document.getElementById('checkout-step-billing');
                if (!billing) {
                    return;
                }
                billing.classList.remove('opacity-60', 'pointer-events-none');
                var lock = billing.querySelector('.checkout-billing-lock-icon');
                if (lock) {
                    lock.classList.add('hidden');
                }
            }

            if (checkoutAuthenticated) {
                unlockCheckoutBillingStep();
            }

            var root = document.getElementById('checkout-passwordless-root');
            if (root) {
                var sendUrl = root.getAttribute('data-send-url');
                var verifyUrl = root.getAttribute('data-verify-url');
                var stepEmail = document.getElementById('checkout-step-email');
                var stepOtp = document.getElementById('checkout-step-otp');
                var stepLoggedIn = document.getElementById('checkout-step-logged-in');
                var emailInput = document.getElementById('email-input');
                var btnEmailContinue = document.getElementById('checkout-email-continue');
                var lblEmailContinue = document.getElementById('checkout-email-continue-label');
                var btnChangeEmail = document.getElementById('checkout-otp-change-email');
                var otpEmailDisplay = document.getElementById('checkout-otp-email-display');
                var otpInput = document.getElementById('checkout-otp-input');
                var btnOtpContinue = document.getElementById('checkout-otp-continue');
                var lblOtpContinue = document.getElementById('checkout-otp-continue-label');
                var btnResend = document.getElementById('checkout-otp-resend');
                var emailError = document.getElementById('checkout-email-error');
                var otpError = document.getElementById('checkout-otp-error');
                var loggedInMessage = document.getElementById('checkout-logged-in-message');

                var currentEmail = '';
                var resendTimerId = null;
                var resendSecondsLeft = 0;
                var verifying = false;
                var sending = false;

                /**
                 * Lấy CSRF token từ meta tag (Laravel).
                 *
                 * @return {string}
                 */
                function getCsrfToken() {
                    var meta = document.querySelector('meta[name="csrf-token"]');
                    return meta ? meta.getAttribute('content') || '' : '';
                }

                /**
                 * Gửi POST JSON và trả về { ok, status, data }.
                 *
                 * @param {string} url
                 * @param {object} body
                 * @return {Promise<{ok: boolean, status: number, data: object}>}
                 */
                function postJson(url, body) {
                    return fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify(body),
                    }).then(function(res) {
                        return res.json().catch(function() {
                            return {};
                        }).then(function(data) {
                            return {
                                ok: res.ok,
                                status: res.status,
                                data: data
                            };
                        });
                    });
                }

                /**
                 * Lấy thông báo lỗi đầu tiên từ response JSON.
                 *
                 * @param {object} data
                 * @param {string} fallback
                 * @return {string}
                 */
                function firstErrorMessage(data, fallback) {
                    if (data && data.message) {
                        return data.message;
                    }
                    if (data && data.errors) {
                        var keys = Object.keys(data.errors);
                        if (keys.length && data.errors[keys[0]] && data.errors[keys[0]][0]) {
                            return data.errors[keys[0]][0];
                        }
                    }
                    return fallback || 'Something went wrong.';
                }

                /**
                 * Hiển thị / ẩn thông báo lỗi email.
                 *
                 * @param {string} message
                 */
                function setEmailError(message) {
                    if (!emailError) {
                        return;
                    }
                    if (message) {
                        emailError.textContent = message;
                        emailError.classList.remove('hidden');
                    } else {
                        emailError.textContent = '';
                        emailError.classList.add('hidden');
                    }
                }

                /**
                 * Hiển thị / ẩn thông báo lỗi OTP.
                 *
                 * @param {string} message
                 */
                function setOtpError(message) {
                    if (!otpError) {
                        return;
                    }
                    if (message) {
                        otpError.textContent = message;
                        otpError.classList.remove('hidden');
                    } else {
                        otpError.textContent = '';
                        otpError.classList.add('hidden');
                    }
                }

                /**
                 * Bật/tắt trạng thái loading cho nút.
                 *
                 * @param {HTMLButtonElement} btn
                 * @param {HTMLElement|null} labelEl
                 * @param {boolean} loading
                 * @param {string} loadingText
                 * @param {string} defaultText
                 */
                function setButtonLoading(btn, labelEl, loading, loadingText, defaultText) {
                    if (!btn) {
                        return;
                    }
                    btn.disabled = !!loading;
                    if (labelEl) {
                        labelEl.textContent = loading ? loadingText : defaultText;
                    }
                }

                /**
                 * Cập nhật nhãn nút gửi lại mã theo countdown.
                 */
                function updateResendButton() {
                    if (!btnResend) {
                        return;
                    }
                    if (resendSecondsLeft > 0) {
                        btnResend.disabled = true;
                        btnResend.textContent = 'Resend code (' + resendSecondsLeft + 's)';
                    } else {
                        btnResend.disabled = false;
                        btnResend.textContent = 'Resend code';
                    }
                }

                /**
                 * Dừng countdown gửi lại mã.
                 */
                function clearResendCountdown() {
                    if (resendTimerId) {
                        clearInterval(resendTimerId);
                        resendTimerId = null;
                    }
                    resendSecondsLeft = 0;
                    updateResendButton();
                }

                /**
                 * Bắt đầu countdown 30s cho nút gửi lại mã.
                 *
                 * @param {number} seconds
                 */
                function startResendCountdown(seconds) {
                    clearResendCountdown();
                    resendSecondsLeft = seconds;
                    updateResendButton();
                    resendTimerId = setInterval(function() {
                        resendSecondsLeft--;
                        if (resendSecondsLeft <= 0) {
                            clearInterval(resendTimerId);
                            resendTimerId = null;
                            resendSecondsLeft = 0;
                        }
                        updateResendButton();
                    }, 1000);
                }

                /**
                 * Hiển thị bước nhập email.
                 */
                function showEmailStep() {
                    clearResendCountdown();
                    currentEmail = '';
                    setEmailError('');
                    setOtpError('');
                    if (stepLoggedIn) {
                        stepLoggedIn.classList.add('hidden');
                    }
                    if (stepEmail) {
                        stepEmail.classList.remove('hidden');
                    }
                    if (stepOtp) {
                        stepOtp.classList.add('hidden');
                    }
                    if (emailInput) {
                        emailInput.disabled = false;
                        emailInput.focus();
                    }
                    if (otpInput) {
                        otpInput.value = '';
                    }
                }

                /**
                 * Hiển thị bước OTP sau khi gửi mã thành công.
                 *
                 * @param {string} email
                 */
                function showOtpStep(email) {
                    currentEmail = email;
                    if (otpEmailDisplay) {
                        otpEmailDisplay.textContent = email;
                    }
                    setOtpError('');
                    if (stepLoggedIn) {
                        stepLoggedIn.classList.add('hidden');
                    }
                    if (stepEmail) {
                        stepEmail.classList.add('hidden');
                    }
                    if (stepOtp) {
                        stepOtp.classList.remove('hidden');
                    }
                    if (otpInput) {
                        otpInput.value = '';
                        otpInput.focus();
                    }
                }

                /**
                 * Hiển thị trạng thái đã đăng nhập.
                 *
                 * @param {object} data
                 */
                function showLoggedIn(data) {
                    if (stepEmail) {
                        stepEmail.classList.add('hidden');
                    }
                    if (stepOtp) {
                        stepOtp.classList.add('hidden');
                    }
                    if (stepLoggedIn) {
                        stepLoggedIn.classList.remove('hidden');
                    }
                    if (loggedInMessage && data.user) {
                        loggedInMessage.textContent = (data.message || 'Signed in successfully.') + ' ' + data.user
                            .email;
                    }
                    unlockCheckoutBillingStep();
                }

                /**
                 * Gọi API gửi mã OTP.
                 *
                 * @param {string} email
                 * @param {'email'|'otp'} errorTarget
                 * @return {Promise<boolean>}
                 */
                function sendCodeRequest(email, errorTarget) {
                    var target = errorTarget || 'email';
                    var setErr = target === 'otp' ? setOtpError : setEmailError;
                    var clearBoth = function() {
                        setEmailError('');
                        setOtpError('');
                    };
                    return postJson(sendUrl, {
                        email: email
                    }).then(function(result) {
                        if (result.status === 419) {
                            setErr('Session expired. Please refresh the page.');
                            return false;
                        }
                        if (!result.ok) {
                            setErr(firstErrorMessage(result.data, 'Unable to send code.'));
                            return false;
                        }
                        if (target === 'email') {
                            clearBoth();
                        } else {
                            setOtpError('');
                        }
                        return true;
                    }).catch(function() {
                        setErr('Network error. Please try again.');
                        return false;
                    });
                }

                /**
                 * Xử lý bấm Tiếp tục ở bước email.
                 */
                function onEmailContinue() {
                    if (sending || !emailInput || !btnEmailContinue) {
                        return;
                    }
                    if (!emailInput.checkValidity()) {
                        emailInput.reportValidity();
                        return;
                    }
                    var email = emailInput.value.trim();
                    sending = true;
                    setEmailError('');
                    emailInput.disabled = true;
                    setButtonLoading(btnEmailContinue, lblEmailContinue, true, 'Sending...', 'Continue');
                    sendCodeRequest(email, 'email').then(function(ok) {
                        sending = false;
                        emailInput.disabled = false;
                        setButtonLoading(btnEmailContinue, lblEmailContinue, false, '', 'Continue');
                        if (ok) {
                            showOtpStep(email);
                            startResendCountdown(30);
                        }
                    });
                }

                /**
                 * Xác minh OTP và đăng nhập.
                 */
                function verifyOtp() {
                    if (verifying || !otpInput || !currentEmail) {
                        return;
                    }
                    var code = otpInput.value.replace(/\D/g, '').slice(0, 6);
                    if (code.length !== 6) {
                        setOtpError('Please enter the 6-digit code.');
                        return;
                    }
                    verifying = true;
                    setOtpError('');
                    setButtonLoading(btnOtpContinue, lblOtpContinue, true, 'Verifying...', 'Continue');
                    postJson(verifyUrl, {
                        email: currentEmail,
                        code: code
                    }).then(function(result) {
                        if (result.status === 419) {
                            setOtpError('Session expired. Please refresh the page.');
                            return;
                        }
                        if (result.ok) {
                            showLoggedIn(result.data);
                            clearResendCountdown();
                            if (window.location.pathname.indexOf('/checkout') !== -1) {
                                window.location.reload();
                            }
                            return;
                        }
                        setOtpError(firstErrorMessage(result.data, 'Invalid or expired code.'));
                    }).catch(function() {
                        setOtpError('Network error. Please try again.');
                    }).then(function() {
                        verifying = false;
                        setButtonLoading(btnOtpContinue, lblOtpContinue, false, '', 'Continue');
                    });
                }

                if (btnEmailContinue && emailInput) {
                    btnEmailContinue.addEventListener('click', onEmailContinue);
                    emailInput.addEventListener('keydown', function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            onEmailContinue();
                        }
                    });
                }

                if (btnChangeEmail) {
                    btnChangeEmail.addEventListener('click', function() {
                        showEmailStep();
                    });
                }

                if (btnOtpContinue && otpInput) {
                    btnOtpContinue.addEventListener('click', function() {
                        verifyOtp();
                    });
                }

                if (otpInput) {
                    otpInput.addEventListener('input', function() {
                        this.value = this.value.replace(/\D/g, '').slice(0, 6);
                        setOtpError('');
                        if (this.value.length === 6) {
                            verifyOtp();
                        }
                    });
                }

                if (btnResend) {
                    btnResend.addEventListener('click', function() {
                        if (!currentEmail || btnResend.disabled || sending) {
                            return;
                        }
                        sending = true;
                        setOtpError('');
                        sendCodeRequest(currentEmail, 'otp').then(function(ok) {
                            if (ok) {
                                if (otpInput) {
                                    otpInput.value = '';
                                    otpInput.focus();
                                }
                                startResendCountdown(30);
                            } else {
                                updateResendButton();
                            }
                        }).finally(function() {
                            sending = false;
                        });
                    });
                }
            }

            document.querySelectorAll('[data-checkout-copy]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var text = btn.getAttribute('data-checkout-copy') || '';
                    if (!text || !navigator.clipboard) {
                        return;
                    }
                    navigator.clipboard.writeText(text).catch(function() {});
                });
            });
        });
    </script>
@endpush
