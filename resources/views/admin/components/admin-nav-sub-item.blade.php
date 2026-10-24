{{-- Sub-menu link inside <x-admin-nav-group> (Paces .sub-menu .menu-link) --}}
@props([
    'href',
    'current' => false,
])

<li>
    <a
        href="{{ $href }}"
        wire:navigate
        @if ($current) aria-current="page" @endif
        {{ $attributes->class([
            'flex cursor-pointer items-center rounded-[5px] px-2.5 py-[7px] text-[13px]/[1.25] whitespace-nowrap transition-all',
            'font-medium text-sidenav-item-hover' => $current,
            'font-normal text-sidenav-item hover:text-sidenav-item-hover' => ! $current,
        ]) }}
    >
        <span class="truncate">{{ $slot }}</span>
    </a>
</li>
