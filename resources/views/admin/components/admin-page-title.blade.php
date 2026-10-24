{{-- Page title bar (Paces .page-title-head); the optional slot holds page actions on the right --}}
@props([
    'title',
])

<div {{ $attributes->class('flex min-h-15 w-full flex-wrap items-center gap-2 md:justify-between') }}>
    <h4 class="text-base font-bold text-body">{{ $title }}</h4>

    @if ($slot->isNotEmpty())
        <div class="flex items-center gap-2">{{ $slot }}</div>
    @endif
</div>
