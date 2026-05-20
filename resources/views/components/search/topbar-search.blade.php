<div class="relative w-full max-w-md" x-data="globalSearch()" x-init="init()">

    <!-- Search Input Container -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search text-[#a0a0a0]">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.35-4.35" />
            </svg>
        </div>

        <input type="text" x-model="query" @input="handleInput()" @focus="showDropdown = query.trim().length >= 2"
            @keydown.enter.prevent="goToFirstResult()" @keydown.escape="showDropdown = false"
            placeholder="Tìm kiếm khóa học, bài viết..."
            class="w-full pl-10 pr-10 py-2.5 bg-white border border-[#e8e8e8] 
                      rounded-full text-sm text-[#292929] placeholder-[#a0a0a0]
                      focus:outline-none focus:border-[#f05123] focus:ring-2 focus:ring-[#f05123]/20
                      transition-all duration-200">

        <!-- Clear Button -->
        <button type="button" x-show="query.length > 0"
            @click="clearSearch()"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#a0a0a0] 
                       hover:text-[#606060] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-x">
                <path d="M18 6l-12 12" />
                <path d="M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Search Dropdown -->
    <x-search.search-dropdown />
</div>

<script>
    function globalSearch() {
        return {
            query: '',
            results: null,
            showDropdown: false,
            isLoading: false,
            errorMessage: '',
            searchTimeout: null,
            debounceDelay: 300,

            init() {
                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!this.$el.contains(e.target)) {
                        this.showDropdown = false;
                    }
                });
            },

            handleInput() {
                clearTimeout(this.searchTimeout);
                this.errorMessage = '';
                const keyword = this.query.trim();

                if (keyword.length === 0) {
                    this.showDropdown = false;
                    this.results = null;
                    return;
                }

                if (keyword.length < 2) {
                    this.showDropdown = false;
                    return;
                }

                this.showDropdown = true;
                this.isLoading = true;

                // Debounce search request
                this.searchTimeout = setTimeout(() => {
                    this.performSearch();
                }, this.debounceDelay);
            },

            async performSearch() {
                try {
                    const keyword = this.query.trim();
                    if (keyword.length < 2) {
                        return;
                    }

                    const response = await fetch(`{{ route('search') }}?q=${encodeURIComponent(keyword)}&limit=6`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Search failed');
                    }

                    const data = await response.json();

                    if (data.success) {
                        this.results = data.data;
                        this.isLoading = false;
                        this.showDropdown = true;
                        return;
                    }

                    this.errorMessage = data.message || 'Không thể tìm kiếm lúc này.';
                    this.isLoading = false;
                    this.showDropdown = true;
                } catch (error) {
                    console.error('Search error:', error);
                    this.errorMessage = 'Không thể tìm kiếm lúc này. Vui lòng thử lại.';
                    this.results = null;
                    this.isLoading = false;
                    this.showDropdown = true;
                }
            },

            clearSearch() {
                clearTimeout(this.searchTimeout);
                this.query = '';
                this.results = null;
                this.errorMessage = '';
                this.showDropdown = false;
                this.isLoading = false;
            },

            goToFirstResult() {
                if (!this.results || this.results.total === 0) {
                    return;
                }

                const firstCourse = this.results.courses && this.results.courses.length ? this.results.courses[0] : null;
                const firstPost = this.results.posts && this.results.posts.length ? this.results.posts[0] : null;
                const firstResult = firstCourse || firstPost;

                if (firstResult && firstResult.url) {
                    window.location.href = firstResult.url;
                }
            },

            goToResult(url) {
                if (!url) {
                    return;
                }

                this.showDropdown = false;
                window.location.href = url;
            },
        };
    }
</script>
