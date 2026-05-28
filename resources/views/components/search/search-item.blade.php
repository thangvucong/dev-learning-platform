@props(['item'])

<a href="{{ $item['url'] }}"
    class="flex items-start gap-3 px-4 py-3 hover:bg-[#f9f9f9] transition-colors duration-150 border-b border-[#e8e8e8] last:border-b-0 cursor-pointer group">
    <!-- Thumbnail -->
    <div class="flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden bg-gradient-to-br from-[#f0f0f0] to-[#e0e0e0]">
        <img src="{{ $item['thumbnail'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
    </div>

    <!-- Content -->
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1">
            <h3 class="text-sm font-semibold text-[#292929] truncate group-hover:text-[#f05123] transition-colors">
                {{ $item['title'] }}
            </h3>
            <!-- Type Badge -->
            <span
                class="flex-shrink-0 px-2 py-0.5 rounded text-xs font-medium
                {{ $item['type'] === 'course' ? 'bg-[#f05123]/10 text-[#d8481f]' : 'bg-[#2563eb]/10 text-[#1e40af]' }}">
                {{ $item['type_label'] }}
            </span>
        </div>

        <p class="text-xs text-[#666] line-clamp-2 mb-2">
            {{ $item['description'] }}
        </p>

        <!-- Meta Info -->
        <div class="flex items-center gap-2 text-xs text-[#808080]">
            @if ($item['type'] === 'course')
                <div class="flex items-center gap-1">
                    <img src="{{ $item['meta']['instructor_avatar'] }}" alt="{{ $item['meta']['instructor'] }}"
                        class="w-4 h-4 rounded-full object-cover">
                    <span>{{ $item['meta']['instructor'] }}</span>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <img src="{{ $item['meta']['author_avatar'] }}" alt="{{ $item['meta']['author'] }}"
                        class="w-4 h-4 rounded-full object-cover">
                    <span>{{ $item['meta']['author'] }}</span>
                    <span class="text-[#d0d0d0]">•</span>
                    <span>{{ $item['meta']['date'] }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Arrow Icon -->
    <div class="flex-shrink-0 text-[#c0c0c0] group-hover:text-[#f05123] transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-arrow-right">
            <path d="M5 12h14" />
            <path d="m12 5 7 7-7 7" />
        </svg>
    </div>
</a>
