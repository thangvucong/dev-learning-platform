@extends('components.admin.adminDashboard')

@section('title', 'Hồ sơ cá nhân')

@section('content')
    <div class="space-y-6">
        @include('components.student.profile.profile-hero', ['profile' => $profile])

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach ($stats as $stat)
                @include('components.student.profile.stats-card', [
                    'label' => $stat['label'],
                    'value' => $stat['value'],
                    'icon' => $stat['icon'],
                    'tone' => $stat['tone'],
                ])
            @endforeach
        </section>

        <section class="rounded-2xl border border-slate-700 bg-[#111827]">
            <div class="px-4 sm:px-6 pt-4">
                <div class="overflow-x-auto">
                    <div id="profile-tabs-nav" class="inline-flex min-w-max rounded-xl bg-slate-900/60 border border-slate-700 p-1 gap-1">
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tabsNav = document.getElementById('profile-tabs-nav');
            if (!tabsNav) {
                return;
            }

            var buttons = Array.prototype.slice.call(document.querySelectorAll('.profile-tab-btn'));
            var panels = Array.prototype.slice.call(document.querySelectorAll('.profile-tab-panel'));

            function activateTab(target) {
                buttons.forEach(function(btn) {
                    var isActive = btn.getAttribute('data-tab-target') === target;
                    btn.classList.toggle('bg-emerald-500', isActive);
                    btn.classList.toggle('text-white', isActive);
                    btn.classList.toggle('text-slate-300', !isActive);
                    btn.classList.toggle('hover:bg-slate-700', !isActive);
                });

                panels.forEach(function(panel) {
                    var isActive = panel.getAttribute('data-tab-panel') === target;
                    panel.classList.toggle('hidden', !isActive);
                });
            }

            buttons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    activateTab(btn.getAttribute('data-tab-target'));
                });
            });
        });
    </script>
@endpush

