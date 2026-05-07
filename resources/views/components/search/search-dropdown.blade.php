<div class="absolute top-full w-[700px] mx-auto left-0 right-0 mt-2 bg-white border border-[#e8e8e8] rounded-xl shadow-lg overflow-hidden z-50"
    x-show="showDropdown" x-transition @click.outside="showDropdown = false" @keydown.escape.window="showDropdown = false">

    <!-- Loading State -->
    <template x-if="isLoading">
        <div class="px-4 py-6">
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-[#f0f0f0] rounded-lg animate-pulse flex-shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 bg-[#f0f0f0] rounded w-3/4 animate-pulse"></div>
                        <div class="h-3 bg-[#f0f0f0] rounded w-1/2 animate-pulse"></div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-[#f0f0f0] rounded-lg animate-pulse flex-shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 bg-[#f0f0f0] rounded w-3/4 animate-pulse"></div>
                        <div class="h-3 bg-[#f0f0f0] rounded w-1/2 animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Results State -->
    <template x-if="!isLoading && results">
        <div>
            <!-- Empty State -->
            <template x-if="results.total === 0">
                <x-search.empty-state />
            </template>

            <!-- Results Container -->
            <template x-if="results.total > 0">
                <div class="max-h-96 overflow-y-auto">
                    <!-- Courses Section -->
                    <template x-if="results.courses && results.courses.length > 0">
                        <div>
                            <div class="px-4 py-2 bg-[#f9f9f9] border-b border-[#e8e8e8]">
                                <p class="text-xs font-semibold text-[#808080] uppercase tracking-wider">Khóa học</p>
                            </div>
                            <div>
                                <template x-for="item in results.courses" :key="item.id">
                                    <a :href="item.url"
                                        class="flex items-start gap-3 px-4 py-3 hover:bg-[#f9f9f9] transition-colors duration-150 border-b border-[#e8e8e8] last:border-b-0 cursor-pointer group">
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden bg-gradient-to-br from-[#f0f0f0] to-[#e0e0e0]">
                                            <img :src="item.thumbnail" :alt="item.title"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 x-text="item.title"
                                                    class="text-sm font-semibold text-[#292929] truncate group-hover:text-[#f05123] transition-colors">
                                                </h3>
                                                <span x-text="item.type_label"
                                                    class="flex-shrink-0 px-2 py-0.5 rounded text-xs font-medium bg-[#f05123]/10 text-[#d8481f]">
                                                </span>
                                            </div>
                                            <p x-text="item.description" class="text-xs text-[#666] line-clamp-2 mb-2">
                                            </p>
                                            <div class="flex items-center gap-2 text-xs text-[#808080]">
                                                <div class="flex items-center gap-1">
                                                    <img :src="item.meta.instructor_avatar" :alt="item.meta.instructor"
                                                        class="w-4 h-4 rounded-full object-cover">
                                                    <span x-text="item.meta.instructor"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="flex-shrink-0 text-[#c0c0c0] group-hover:text-[#f05123] transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-arrow-right">
                                                <path d="M5 12h14" />
                                                <path d="m12 5 7 7-7 7" />
                                            </svg>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Posts Section -->
                    <template x-if="results.posts && results.posts.length > 0">
                        <div>
                            <template x-if="results.courses && results.courses.length > 0">
                                <div class="border-t border-[#e8e8e8]"></div>
                            </template>
                            <div class="px-4 py-2 bg-[#f9f9f9] border-b border-[#e8e8e8]">
                                <p class="text-xs font-semibold text-[#808080] uppercase tracking-wider">Bài viết</p>
                            </div>
                            <div>
                                <template x-for="item in results.posts" :key="item.id">
                                    <a :href="item.url"
                                        class="flex items-start gap-3 px-4 py-3 hover:bg-[#f9f9f9] transition-colors duration-150 border-b border-[#e8e8e8] last:border-b-0 cursor-pointer group">
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden bg-gradient-to-br from-[#f0f0f0] to-[#e0e0e0]">
                                            <img :src="item.thumbnail" :alt="item.title"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 x-text="item.title"
                                                    class="text-sm font-semibold text-[#292929] truncate group-hover:text-[#f05123] transition-colors">
                                                </h3>
                                                <span x-text="item.type_label"
                                                    class="flex-shrink-0 px-2 py-0.5 rounded text-xs font-medium bg-[#2563eb]/10 text-[#1e40af]">
                                                </span>
                                            </div>
                                            <p x-text="item.description" class="text-xs text-[#666] line-clamp-2 mb-2">
                                            </p>
                                            <div class="flex items-center gap-2 text-xs text-[#808080]">
                                                <div class="flex items-center gap-2">
                                                    <img :src="item.meta.author_avatar" :alt="item.meta.author"
                                                        class="w-4 h-4 rounded-full object-cover">
                                                    <span x-text="item.meta.author"></span>
                                                    <span class="text-[#d0d0d0]">•</span>
                                                    <span x-text="item.meta.date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="flex-shrink-0 text-[#c0c0c0] group-hover:text-[#f05123] transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-arrow-right">
                                                <path d="M5 12h14" />
                                                <path d="m12 5 7 7-7 7" />
                                            </svg>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Footer - View All -->
            <template x-if="results.total > 0">
                <div class="px-4 py-3 border-t border-[#e8e8e8] bg-[#f9f9f9]">
                    <p class="text-xs text-center text-[#808080]">
                        Tìm thấy <span class="font-semibold text-[#292929]" x-text="results.total"></span> kết quả
                    </p>
                </div>
            </template>
        </div>
    </template>
</div>
