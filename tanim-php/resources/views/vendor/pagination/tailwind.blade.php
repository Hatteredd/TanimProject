@if ($paginator->hasPages())
<style>
.pgn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    flex-wrap: wrap;
    padding: 1rem 0;
    font-family: 'Outfit', sans-serif;
}
.pgn-info {
    font-size: .78rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-right: .5rem;
}
.pgn-info strong { color: var(--primary); }

/* Base button */
.pgn-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.25rem;
    height: 2.25rem;
    padding: 0 .65rem;
    font-size: .82rem;
    font-weight: 700;
    border-radius: .75rem;
    text-decoration: none;
    border: 1.5px solid var(--border-glass);
    background: var(--bg-glass);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    color: var(--text-muted);
    box-shadow: var(--shadow-neu-sm);
    transition: all .2s ease;
    cursor: pointer;
    line-height: 1;
}
.pgn-btn:hover {
    background: var(--primary-faint);
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-1px);
    box-shadow: var(--shadow-hover);
}

/* Active page */
.pgn-btn.active {
    background: linear-gradient(135deg, var(--primary), var(--primary-2));
    border-color: transparent;
    color: #fff;
    box-shadow: 0 4px 14px rgba(46,139,46,.35);
    transform: translateY(-1px);
    cursor: default;
}

/* Disabled (prev on page 1, next on last page) */
.pgn-btn.disabled {
    opacity: .35;
    cursor: not-allowed;
    pointer-events: none;
    box-shadow: none;
    transform: none;
}

/* Dots separator */
.pgn-dots {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.25rem;
    height: 2.25rem;
    font-size: .85rem;
    color: var(--text-light);
    font-weight: 700;
}

/* Prev / Next wider */
.pgn-btn.pgn-nav {
    padding: 0 1rem;
    gap: .3rem;
    font-size: .8rem;
    border-radius: 1rem;
}
</style>

<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
    <div class="pgn">

        {{-- Info --}}
        @if ($paginator->firstItem())
        <span class="pgn-info">
            Showing <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong> of <strong>{{ $paginator->total() }}</strong>
        </span>
        @endif

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="pgn-btn pgn-nav disabled" aria-disabled="true">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Prev
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pgn-btn pgn-nav" aria-label="{{ __('pagination.previous') }}">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Prev
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pgn-dots">···</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pgn-btn active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pgn-btn" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pgn-btn pgn-nav" aria-label="{{ __('pagination.next') }}">
                Next
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            </a>
        @else
            <span class="pgn-btn pgn-nav disabled" aria-disabled="true">
                Next
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            </span>
        @endif

    </div>
</nav>
@endif
