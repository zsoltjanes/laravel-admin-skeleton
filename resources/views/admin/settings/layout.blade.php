<div class="grid grid-cols-1 items-start gap-base lg:grid-cols-12">
    <x-admin-card class="lg:col-span-3" flush>
        <flux:navlist class="p-2.5" aria-label="{{ __('Settings') }}">
            <flux:navlist.item icon="user-circle" :href="route('profile.edit')" :current="request()->routeIs('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item icon="shield-check" :href="route('security.edit')" :current="request()->routeIs('security.edit')" wire:navigate>{{ __('Security') }}</flux:navlist.item>
            <flux:navlist.item icon="swatch" :href="route('appearance.edit')" :current="request()->routeIs('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
        </flux:navlist>
    </x-admin-card>

    <x-admin-card class="lg:col-span-9" :title="$heading ?? null">
        @if (filled($subheading ?? null))
            <flux:text>{{ $subheading }}</flux:text>
        @endif

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </x-admin-card>
</div>
