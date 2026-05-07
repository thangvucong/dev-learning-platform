@extends('components.admin.adminDashboard')

@section('title', 'Hồ sơ cá nhân')

@section('content')
    @php
        $initialModal = '';
        if (
            $errors->hasAny([
                'name',
                'bio',
                'phone',
                'date_of_birth',
                'social_github',
                'social_linkedin',
                'social_portfolio',
            ])
        ) {
            $initialModal = 'edit-profile-modal';
        } elseif ($errors->has('avatar')) {
            $initialModal = 'avatar-upload-modal';
        } elseif ($errors->hasAny(['current_password', 'password'])) {
            $initialModal = 'change-password-modal';
        } elseif ($errors->hasAny(['timezone', 'notifications_enabled', 'weekly_report'])) {
            $initialModal = 'account-settings-modal';
        }
    @endphp
    <div class="space-y-5" id="student-profile-root" data-initial-modal="{{ $initialModal }}">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                <p class="font-semibold mb-1">Có lỗi xảy ra, vui lòng kiểm tra lại thông tin.</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="grid grid-cols-1 xl:grid-cols-12 gap-4">
            <div class="xl:col-span-9">
                @include('components.student.profile.profile-hero', ['profile' => $profile])
            </div>
            <div class="xl:col-span-3">
                @include('components.student.profile.quick-actions')
            </div>
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827]">
            <div class="px-4 sm:px-6 pt-4 border-b border-slate-700">
                <div class="overflow-x-auto pb-4">
                    <div id="profile-tabs-nav"
                        class="inline-flex min-w-max rounded-xl bg-slate-900/60 border border-slate-700 p-1 gap-1">
                        @foreach ([
            'personal' => 'Thông tin cá nhân',
            'learning' => 'Hành trình học tập',
            'achievements' => 'Thành tích',
            'security' => 'Bảo mật',
        ] as $tabKey => $tabLabel)
                            <button type="button"
                                class="profile-tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $loop->first ? 'bg-emerald-500 text-white' : 'text-slate-300 hover:bg-slate-700' }}"
                                data-tab-target="{{ $tabKey }}">
                                {{ $tabLabel }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <div class="profile-tab-panel" data-tab-panel="personal">
                    @include('pages.student.profile.tabs.personal', ['profile' => $profile])
                </div>
                <div class="profile-tab-panel hidden" data-tab-panel="learning">
                    @include('pages.student.profile.tabs.learning', ['learning' => $learning])
                </div>
                <div class="profile-tab-panel hidden" data-tab-panel="achievements">
                    @include('pages.student.profile.tabs.achievements', ['achievements' => $achievements])
                </div>
                <div class="profile-tab-panel hidden" data-tab-panel="security">
                    @include('pages.student.profile.tabs.security', ['security' => $security])
                </div>
            </div>
        </section>
    </div>

    @include('components.student.profile.edit-profile-modal', ['profile' => $profile])
    @include('components.student.profile.avatar-upload-modal', ['profile' => $profile])
    @include('components.student.profile.change-password-modal')
    @include('components.student.profile.account-settings-modal', ['settings' => $settings])
@endsection

@push('scripts')
    <script src="{{ mix('assets/js/student-profile.js') }}"></script>
@endpush
