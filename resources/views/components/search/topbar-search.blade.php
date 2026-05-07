<div class="w-full max-w-md" x-data="globalSearch()" x-init="init()">

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

        <input type="text" x-model="query" @input="handleInput()" @focus="showDropdown = query.length > 0"
            @keydown.escape="showDropdown = false" placeholder="Tìm kiếm khóa học, bài viết..."
            class="w-full pl-10 pr-10 py-2.5 bg-white border border-[#e8e8e8] 
                      rounded-full text-sm text-[#292929] placeholder-[#a0a0a0]
                      focus:outline-none focus:border-[#f05123] focus:ring-2 focus:ring-[#f05123]/20
                      transition-all duration-200">

        <!-- Clear Button -->
        <button type="button" x-show="query.length > 0"
            @click="query = ''; results = null; showDropdown = false; isLoading = false"
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

                if (this.query.length === 0) {
                    this.showDropdown = false;
                    this.results = null;
                    return;
                }

                if (this.query.length < 2) {
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
                    const response = await fetch(`{{ route('search') }}?q=${encodeURIComponent(this.query)}`);

                    if (!response.ok) {
                        throw new Error('Search failed');
                    }

                    const data = await response.json();

                    if (data.success) {
                        this.results = data.data;
                        this.isLoading = false;
                        this.showDropdown = true;
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    this.isLoading = false;
                    this.showDropdown = false;
                }
            },
        };
    }
</script>
