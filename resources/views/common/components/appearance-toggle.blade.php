{{-- Light / dark switch: a sun in dark mode, a moon in light mode --}}
<flux:button
    x-data
    x-on:click="$flux.dark = ! $flux.dark"
    size="sm"
    variant="ghost"
    square
    :aria-label="__('Toggle dark mode')"
    {{ $attributes }}
>
    <flux:icon.sun variant="mini" x-show="$flux.dark" x-cloak />
    <flux:icon.moon variant="mini" x-show="! $flux.dark" />
</flux:button>
