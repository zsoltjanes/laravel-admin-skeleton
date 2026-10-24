{{-- Card (Paces .card with .card-header / .card-body / .card-footer) --}}
@props([
    'title' => null,
    'actions' => null,
    'footer' => null,
    'flush' => false,
])

<div {{ $attributes->class('flex h-fit flex-col overflow-hidden rounded-card bg-card break-words shadow-card') }}>
    @if ($title || $actions)
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-dashed border-line px-5 py-3.75">
            @if ($title)
                <h4 class="text-[15px] font-medium text-body">{{ $title }}</h4>
            @endif

            {{ $actions }}
        </div>
    @endif

    <div @class(['flex-auto', 'p-5' => ! $flush])>
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-3.75 dark:border-zinc-700">
            {{ $footer }}
        </div>
    @endif
</div>
