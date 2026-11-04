@if ($paginator->hasPages())
    @php
        $paginator->appends(request()->except('page'));
        $perPage = (int) request('per_page', $paginator->perPage());
        $perPageId = 'per-page-' . $paginator->getPageName() . '-' . spl_object_id($paginator);
    @endphp
    <div class="tc-pagination border-t border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
        <div class="flex flex-col gap-4 sm:gap-6 sm:flex-row sm:items-center sm:justify-between">
            <!-- Info Text -->
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan <span class="font-medium">{{ $paginator->firstItem() ?? 0 }}</span> hingga 
                <span class="font-medium">{{ $paginator->lastItem() ?? 0 }}</span> dari 
                <span class="font-medium">{{ $paginator->total() }}</span> hasil
            </div>

            <!-- Per Page Selector -->
            <form method="GET" class="flex items-center gap-2">
                @foreach(request()->query() as $key => $value)
                    @if($key !== 'per_page' && $key !== 'page')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                
                <label for="{{ $perPageId }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Per Halaman:
                </label>
                <select name="per_page" id="{{ $perPageId }}"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                    onchange="this.form.submit()">
                    <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ $perPage === 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50</option>
                </select>
            </form>
        </div>

        <!-- Pagination Links -->
        <nav class="mt-4 flex items-center justify-center gap-1 flex-wrap" aria-label="Navigasi halaman">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-label="Halaman sebelumnya" aria-disabled="true" class="relative inline-flex items-center rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-500 cursor-not-allowed dark:border-gray-600 dark:bg-white/5 dark:text-gray-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"
                    class="relative inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Page Numbers --}}
            @php
                $start = max(1, $paginator->currentPage() - 2);
                $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
            @endphp

            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}"
                    class="relative inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10 transition-colors">
                    1
                </a>
                @if ($start > 2)
                    <span class="px-1 text-gray-500">...</span>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $paginator->currentPage())
                    <span aria-current="page" class="relative inline-flex items-center rounded-lg border border-brand-300 bg-brand-50 px-3 py-2 text-sm font-medium text-brand-600 dark:border-brand-500/30 dark:bg-brand-500/15 dark:text-brand-400">
                        {{ $i }}
                    </span>
                @else
                    <a href="{{ $paginator->url($i) }}"
                        class="relative inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10 transition-colors">
                        {{ $i }}
                    </a>
                @endif
            @endfor

            @if ($end < $paginator->lastPage())
                @if ($end < $paginator->lastPage() - 1)
                    <span class="px-1 text-gray-500">...</span>
                @endif
                <a href="{{ $paginator->url($paginator->lastPage()) }}"
                    class="relative inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10 transition-colors">
                    {{ $paginator->lastPage() }}
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya"
                    class="relative inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span aria-label="Halaman berikutnya" aria-disabled="true" class="relative inline-flex items-center rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-500 cursor-not-allowed dark:border-gray-600 dark:bg-white/5 dark:text-gray-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </nav>
    </div>
@endif
