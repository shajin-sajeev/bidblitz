@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="pagination">
        <ul class="pagination-items">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">
                        <span>
                            <svg fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                            </svg>
                        </span>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <span>
                            <svg fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                            </svg>
                        </span>
                    </a>
                </li>
            @endif

            @php
                $lastPage = $paginator->lastPage();
                $currentPage = $paginator->currentPage();
                $firstPages = range(1, min(2, $lastPage));
                $middlePages = range(max(1, $currentPage - 1), min($lastPage, $currentPage + 1));
                $lastPages = $lastPage > 2 ? range(max(1, $lastPage - 1), $lastPage) : [];
                $pages = array_values(array_unique(array_merge($firstPages, $middlePages, $lastPages)));
                sort($pages);
                $previousPage = 0;
            @endphp

            @foreach ($pages as $page)
                @if ($previousPage && $page > $previousPage + 1)
                    <li class="page-item disabled pagination-ellipsis" aria-disabled="true">
                        <span class="page-link">...</span>
                    </li>
                @endif

                @if ($page == $paginator->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link"><span>{{ $page }}</span></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($page) }}"><span>{{ $page }}</span></a>
                    </li>
                @endif

                @php $previousPage = $page; @endphp
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <span>
                            <svg fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </span>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">
                        <span>
                            <svg fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </span>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
