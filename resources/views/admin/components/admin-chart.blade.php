{{--
    ApexCharts chart rendered by resources/js/charts.js, with its data as a table under it.
    type: area|bar|donut. For area/bar: $data = ['categories' => [...], 'series' => [['name' => ..., 'data' => [...]], ...]].
    For donut: $data = ['labels' => [...], 'series' => [...]]. $unit is appended to values in tooltips and the table.
--}}
@props([
    'type',
    'data',
    'height' => 300,
    'unit' => '',
])

@php
    $config = ['type' => $type, 'height' => $height, 'unit' => $unit, ...$data];

    // Rows of the table view: [label, value per series...]
    if ($type === 'donut') {
        $headers = [__('Category'), __('Value')];
        $rows = collect($data['labels'])->map(fn ($label, $i) => [$label, $data['series'][$i]])->all();
    } else {
        $headers = [__('Period'), ...array_column($data['series'], 'name')];
        $rows = collect($data['categories'])->map(fn ($label, $i) => [$label, ...array_map(fn ($series) => $series['data'][$i], $data['series'])])->all();
    }
@endphp

<div {{ $attributes }}>
    <div wire:ignore>
        <div data-chart="{{ json_encode($config) }}" style="min-height: {{ $height }}px"></div>
    </div>

    <details class="group mt-3 text-sm">
        <summary class="cursor-pointer text-muted select-none hover:text-primary">{{ __('Show data as table') }}</summary>

        <div class="mt-2 overflow-auto">
            <table class="w-full text-start text-body">
                <thead>
                    <tr class="border-b border-line text-2xs uppercase">
                        @foreach ($headers as $header)
                            <th class="px-2 py-1.5 text-start font-bold">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr class="border-b border-zinc-100 last:border-0 dark:border-zinc-700">
                            @foreach ($row as $i => $cell)
                                <td class="px-2 py-1.5 {{ $i ? 'tabular-nums' : '' }}">{{ $i ? Number::format($cell).$unit : $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
</div>
