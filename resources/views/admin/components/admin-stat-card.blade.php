{{-- Stat tile (Paces products page top cards); color: primary|secondary|success|info|warning|danger --}}
@props([
    'title',
    'value',
    'icon',
    'color' => 'primary',
    'badge' => null,
    'badgeColor' => 'success',
    'label' => null,
    'labelValue' => null,
])

@php
    $tones = [
        'primary' => ['bg-primary/15 text-primary', 'text-primary'],
        'secondary' => ['bg-secondary/15 text-secondary', 'text-secondary'],
        'success' => ['bg-success/15 text-success', 'text-success'],
        'info' => ['bg-info/15 text-info', 'text-info'],
        'warning' => ['bg-warning/15 text-warning', 'text-warning'],
        'danger' => ['bg-danger/15 text-danger', 'text-danger'],
    ];
    [$iconTone, $dotTone] = $tones[$color] ?? $tones['primary'];
    [$badgeTone] = $tones[$badgeColor] ?? $tones['success'];
@endphp

<x-admin-card {{ $attributes }}>
    <h5 class="mb-2 text-sm font-medium text-body">{{ $title }}</h5>

    <div class="my-5 flex items-center gap-2.5">
        <div class="flex size-9 shrink-0 items-center justify-center rounded-full {{ $iconTone }}">
            <flux:icon :icon="$icon" variant="outline" class="size-5" />
        </div>

        <h3 class="text-xl font-semibold text-body">{{ $value }}</h3>

        @if (filled($badge))
            <span class="ms-auto inline-flex items-center rounded-card px-2 text-xs font-medium {{ $badgeTone }}">{{ $badge }}</span>
        @endif
    </div>

    @if (filled($label))
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-1.5">
                <span class="size-2 rounded-full bg-current {{ $dotTone }}"></span>
                <span class="text-sm text-muted">{{ $label }}</span>
            </div>
            <span class="font-semibold text-body">{{ $labelValue }}</span>
        </div>
    @endif
</x-admin-card>
