@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
@endphp

@if ($paginator->hasPages())
    <nav class="warehouse-pagination" role="navigation" aria-label="صفحه‌بندی">
        <div class="warehouse-pagination-info">
            نمایش {{ number_format($paginator->firstItem() ?? 0) }} تا {{ number_format($paginator->lastItem() ?? 0) }}
            از {{ number_format($paginator->total()) }} کالا
        </div>

        <div class="warehouse-pagination-links">
            @if ($paginator->onFirstPage())
                <span class="disabled" aria-disabled="true" aria-label="قبلی">‹</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="صفحه قبلی">‹</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="dots">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="current" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" aria-label="رفتن به صفحه {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="صفحه بعدی">›</a>
            @else
                <span class="disabled" aria-disabled="true" aria-label="بعدی">›</span>
            @endif
        </div>
    </nav>
@endif
