{{-- Sidenav link (Paces .menu-item > .menu-link) --}}
@props([
    'href',
    'icon' => null,
    'current' => false,
])

<li>
    <a
        href="{{ $href }}"
        wire:navigate
        @if ($current) aria-current="page" @endif
        {{ $attributes->class([
            'relative flex cursor-pointer items-center gap-3 rounded-[5px] p-2.5 text-sm/[1.25] font-medium whitespace-nowrap transition-all',
            'bg-sidenav-item-active-bg text-sidenav-item-active' => $current,
            'text-sidenav-item hover:text-sidenav-item-hover' => ! $current,
        ]) }}
    >
        @if ($icon)
            <flux:icon :icon="$icon" variant="outline" class="size-[18px] shrink-0" />
        @endif

        <span class="truncate">{{ $slot }}</span>
    </a>
</li>
