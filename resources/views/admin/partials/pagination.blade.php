{{-- Livewire pagination in the Paces style (.pagination > .page-item > .page-link) --}}
@php
    $pageName = $paginator->getPageName();
    $link = 'flex size-7.5 cursor-pointer items-center justify-center rounded-card border border-line text-sm transition-colors hover:border-zinc-400';
@endphp

<div class="flex w-full flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-muted">
        {!! __('Showing :from to :to of :total results', [
            'from' => '<span class="font-semibold text-body">'.($paginator->firstItem() ?? 0).'</span>',
            'to' => '<span class="font-semibold text-body">'.($paginator->lastItem() ?? 0).'</span>',
            'total' => '<span class="font-semibold text-body">'.$paginator->total().'</span>',
        ]) !!}
    </p>

    @if ($paginator->hasPages())
        <nav class="inline-flex items-center gap-1.5" aria-label="{{ __('Pagination Navigation') }}">
            @if ($paginator->onFirstPage())
                <span class="{{ $link }} pointer-events-none opacity-60" aria-disabled="true">
                    <flux:icon.chevron-left variant="micro" class="rtl:rotate-180" />
                </span>
            @else
                <button type="button" wire:click="previousPage('{{ $pageName }}')" wire:loading.attr="disabled" class="{{ $link }}" aria-label="{{ __('pagination.previous') }}">
                    <flux:icon.chevron-left variant="micro" class="rtl:rotate-180" />
                </button>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="{{ $link }} pointer-events-none border-transparent">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span wire:key="paginator-{{ $pageName }}-page{{ $page }}" class="{{ $link }} border-primary! bg-primary text-white" aria-current="page">{{ $page }}</span>
                        @else
                            <button type="button" wire:key="paginator-{{ $pageName }}-page{{ $page }}" wire:click="gotoPage({{ $page }}, '{{ $pageName }}')" class="{{ $link }}">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $pageName }}')" wire:loading.attr="disabled" class="{{ $link }}" aria-label="{{ __('pagination.next') }}">
                    <flux:icon.chevron-right variant="micro" class="rtl:rotate-180" />
                </button>
            @else
                <span class="{{ $link }} pointer-events-none opacity-60" aria-disabled="true">
                    <flux:icon.chevron-right variant="micro" class="rtl:rotate-180" />
                </span>
            @endif
        </nav>
    @endif
</div>
