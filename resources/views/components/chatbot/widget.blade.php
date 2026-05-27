@php
    $routeName = (string) optional(request()->route())->getName();
    $pageType = 'generic';
    $pageRef = '';

    if ($routeName === 'home') {
        $pageType = 'home';
    } elseif ($routeName === 'posts.index') {
        $pageType = 'posts_index';
    } elseif ($routeName === 'posts.show') {
        $pageType = 'post_detail';
        $pageRef = (string) request()->route('slug', '');
    } elseif ($routeName === 'courses.show') {
        $pageType = 'course_detail';
        $pageRef = (string) request()->route('slug', '');
    }
@endphp

<div data-global-chatbot
    data-chat-url="{{ route('chatbot.message') }}"
    data-page-type="{{ $pageType }}"
    data-page-ref="{{ e($pageRef) }}"
    class="fixed bottom-5 right-5 z-50">
    <button type="button" data-chatbot-toggle
        class="flex h-14 w-14 items-center justify-center rounded-full bg-[#f05123] text-white shadow-xl shadow-orange-500/30 transition hover:bg-[#d8481f]"
        aria-label="Mở chatbot">
        <i class="fa-regular fa-comments text-xl"></i>
    </button>

    <section data-chatbot-panel
        class="hidden absolute bottom-16 right-0 flex h-[560px] w-[min(360px,calc(100vw-2rem))] flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl">
        <header class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <div>
                <p class="text-sm font-bold text-gray-900">Trợ lý học tập</p>
                <p class="text-xs text-gray-500">Hỏi về khóa học, bài viết và nội dung trên nền tảng</p>
            </div>
            <button type="button" data-chatbot-close
                class="flex h-8 w-8 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                aria-label="Đóng chatbot">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </header>

        <div data-chatbot-messages class="flex-1 space-y-3 overflow-y-auto bg-gray-50 p-4">
            <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-white px-3 py-2 text-sm text-gray-700 shadow-sm">
                Mình có thể giúp bạn tìm khóa học, tóm tắt bài viết hoặc giải thích nội dung đang xem.
            </div>
        </div>

        <div data-chatbot-sources class="hidden border-t border-gray-100 bg-white px-4 py-2"></div>

        <form data-chatbot-form class="border-t border-gray-100 bg-white p-3">
            <div class="flex items-end gap-2">
                <textarea data-chatbot-input rows="1" maxlength="1000"
                    class="max-h-28 min-h-[42px] flex-1 resize-none rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-900 outline-none focus:border-[#f05123] focus:ring-0"
                    placeholder="Nhập câu hỏi..."></textarea>
                <button type="submit" data-chatbot-submit
                    class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-xl bg-[#f05123] text-white transition hover:bg-[#d8481f]"
                    aria-label="Gửi">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </section>
</div>
