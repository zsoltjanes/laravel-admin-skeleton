<div>
    @vite('resources/js/charts.js')

    <x-admin-page-title :title="__('Dashboard')" />

    {{-- Stat tiles --}}
    <div class="mb-base grid grid-cols-1 gap-base md:grid-cols-2 xl:grid-cols-4">
        <x-admin-stat-card
            :title="__('Users')"
            :value="Number::format($this->stats['total'])"
            icon="users"
            color="primary"
            :badge="__('+:count new', ['count' => $this->stats['new_this_month']])"
            :label="__('This month')"
            :label-value="Number::format($this->stats['new_this_month'])"
        />
        <x-admin-stat-card
            :title="__('Verified')"
            :value="Number::format($this->stats['verified'])"
            icon="check-badge"
            color="success"
            :label="__('Verified email address')"
            :label-value="Number::percentage($this->stats['total'] ? $this->stats['verified'] / $this->stats['total'] * 100 : 0)"
        />
        <x-admin-stat-card
            :title="__('Two-factor authentication')"
            :value="Number::format($this->stats['two_factor'])"
            icon="shield-check"
            color="info"
            :label="__('Enabled')"
            :label-value="Number::percentage($this->stats['total'] ? $this->stats['two_factor'] / $this->stats['total'] * 100 : 0)"
        />
        <x-admin-stat-card
            :title="__('Revenue')"
            :value="Number::currency(86000, 'EUR')"
            icon="banknotes"
            color="secondary"
            badge="+12%"
            :label="__('Sample data')"
            :label-value="Number::currency(61000, 'EUR')"
        />
    </div>

    {{-- Charts --}}
    <div class="mb-base grid grid-cols-1 gap-base xl:grid-cols-12">
        <x-admin-card :title="__('Revenue (sample data)')" class="xl:col-span-8">
            <x-admin-chart type="area" :data="$this->revenueChart" unit=" k€" :height="320" />
        </x-admin-card>

        <x-admin-card :title="__('Users by security')" class="xl:col-span-4">
            <x-admin-chart type="donut" :data="[...$this->securityChart, 'totalLabel' => __('Users')]" :height="320" />
        </x-admin-card>
    </div>

    <x-admin-card :title="__('New users per month')">
        <x-admin-chart type="bar" :data="$this->newUsersChart" :height="280" />
    </x-admin-card>
</div>
