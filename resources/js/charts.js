/**
 * Admin charts (ApexCharts) styled after the Paces theme.
 * Every element with a data-chart JSON config is rendered; charts re-render when the
 * light/dark mode changes and after wire:navigate page visits.
 */
import ApexCharts from 'apexcharts';

// Categorical palette in fixed order, validated (dataviz validate_palette.js) per mode against the card surface
const PALETTES = {
    light: ['#236dc9', '#02bc9c', '#7b70ef'],
    dark: ['#236dc9', '#03a98b', '#7b70ef'],
};

const charts = new Map();

const isDark = () => document.documentElement.classList.contains('dark');
const token = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();

function baseOptions(config) {
    const dark = isDark();
    const unit = config.unit ?? '';
    const format = (value) => `${new Intl.NumberFormat(document.documentElement.lang).format(value)}${unit}`;

    return {
        chart: {
            type: config.type,
            height: config.height,
            fontFamily: 'Nunito, ui-sans-serif, system-ui, sans-serif',
            foreColor: token('--color-muted'),
            background: 'transparent',
            toolbar: { show: false },
            zoom: { enabled: false },
            parentHeightOffset: 0,
        },
        theme: { mode: dark ? 'dark' : 'light' },
        colors: PALETTES[dark ? 'dark' : 'light'],
        dataLabels: { enabled: false },
        grid: {
            borderColor: token('--color-line'),
            strokeDashArray: 3,
            padding: { left: 6, right: 6 },
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '13px',
            labels: { colors: token('--color-body') },
            markers: { size: 5, shape: 'circle', strokeWidth: 0 },
            itemMargin: { horizontal: 8 },
        },
        tooltip: { theme: dark ? 'dark' : 'light', y: { formatter: format } },
        yaxis: { labels: { formatter: format } },
        states: { hover: { filter: { type: 'darken' } } },
    };
}

function options(config) {
    const base = baseOptions(config);

    if (config.type === 'donut') {
        return {
            ...base,
            series: config.series,
            labels: config.labels,
            legend: { ...base.legend, position: 'bottom', horizontalAlign: 'center' },
            // 2px surface gap between slices
            stroke: { width: 2, colors: [token('--color-card')] },
            dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 600 }, dropShadow: { enabled: false } },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            value: { color: token('--color-body'), fontSize: '20px', fontWeight: 700 },
                            total: { show: true, label: config.totalLabel ?? '', color: token('--color-muted') },
                        },
                    },
                },
            },
            yaxis: undefined,
        };
    }

    const common = {
        ...base,
        series: config.series,
        xaxis: {
            categories: config.categories,
            axisBorder: { show: false },
            axisTicks: { show: false },
            crosshairs: { stroke: { color: token('--color-line'), dashArray: 3 } },
        },
        // A single series is named by the card title, so it gets no legend
        legend: { ...base.legend, show: config.series.length > 1 },
    };

    if (config.type === 'bar') {
        return {
            ...common,
            plotOptions: { bar: { columnWidth: '45%', borderRadius: 4, borderRadiusApplication: 'end' } },
            tooltip: { ...common.tooltip, shared: true, intersect: false },
        };
    }

    return {
        ...common,
        stroke: { width: 2, curve: 'smooth' },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.02, stops: [0, 95] } },
        markers: { size: 0, strokeWidth: 2, strokeColors: token('--color-card'), hover: { size: 5 } },
        tooltip: { ...common.tooltip, shared: true, intersect: false },
    };
}

/**
 * Render the charts on the page; already rendered ones are kept unless `force` (theme change).
 */
function render(force = false) {
    // Drop charts whose element left the page (wire:navigate)
    for (const [element, chart] of charts) {
        if (force || ! document.body.contains(element)) {
            chart.destroy();
            charts.delete(element);
        }
    }

    document.querySelectorAll('[data-chart]').forEach((element) => {
        if (charts.has(element)) {
            return;
        }

        const chart = new ApexCharts(element, options(JSON.parse(element.dataset.chart)));
        chart.render();
        charts.set(element, chart);
    });
}

// This module may load after the first livewire:navigated (when the page is reached via wire:navigate)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => render());
} else {
    render();
}

document.addEventListener('livewire:navigated', () => render());

// Re-render with the other palette when Flux toggles the .dark class
let dark = isDark();

new MutationObserver(() => {
    if (dark !== isDark()) {
        dark = isDark();
        render(true);
    }
}).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
