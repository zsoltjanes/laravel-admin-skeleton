{{-- Language dropdown: shows the active language, the others open on click --}}
@php
    // Language names in their own language, flags by country code
    $languages = ['hu' => ['Magyar', 'hu'], 'en' => ['English', 'gb']];
    $current = app()->getLocale();
@endphp

<flux:dropdown position="bottom" align="end" {{ $attributes }}>
    <flux:button size="sm" variant="ghost" icon-trailing="chevron-down" :aria-label="__('Language').': '.($languages[$current][0] ?? $current)">
        <span class="flex items-center gap-2">
            <x-country-flag :code="$languages[$current][1] ?? $current" />
            {{ strtoupper($current) }}
        </span>
    </flux:button>

    <flux:menu class="min-w-40">
        @foreach (config('app.supported_locales') as $locale)
            <flux:menu.item :href="route('locale', $locale)" :aria-current="$locale === $current ? 'true' : null">
                <span class="flex w-full items-center gap-2">
                    <x-country-flag :code="$languages[$locale][1] ?? $locale" />
                    <span class="flex-1">{{ $languages[$locale][0] ?? strtoupper($locale) }}</span>
                    @if ($locale === $current)
                        <flux:icon.check variant="micro" class="text-zinc-500" />
                    @endif
                </span>
            </flux:menu.item>
        @endforeach
    </flux:menu>
</flux:dropdown>
