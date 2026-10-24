{{-- Collapsible sidenav entry with a sub-menu (Paces .menu-item.hs-accordion > .sub-menu); open when one of its pages is current --}}
@props([
    'label',
    'icon' => null,
    'current' => false,
])

<li x-data="{ open: @js((bool) $current) }">
    <button
        type="button"
        x-on:click="open = ! open"
        x-bind:aria-expanded="open"
        @class([
            'relative flex w-full cursor-pointer items-center gap-3 rounded-[5px] p-2.5 text-sm/[1.25] font-medium whitespace-nowrap transition-all',
            'bg-sidenav-item-active-bg text-sidenav-item-active' => $current,
            'text-sidenav-item hover:text-sidenav-item-hover' => ! $current,
        ])
    >
        @if ($icon)
            <flux:icon :icon="$icon" variant="outline" class="size-[18px] shrink-0" />
        @endif

        <span class="truncate">{{ $label }}</span>

        <flux:icon.chevron-down variant="micro" class="ms-auto size-3.5 transition-transform" x-bind:class="open && 'rotate-180'" />
    </button>

    <ul x-show="open" x-collapse @unless ($current) x-cloak @endunless class="w-full space-y-1 overflow-hidden ps-7.5 pt-1">
        {{ $slot }}
    </ul>
</li>
