<div>
    <x-admin-page-title :title="$user ? __('Edit user') : __('New user')">
        <flux:button :href="route('admin.users.index')" size="sm" icon="arrow-left" wire:navigate>
            {{ __('Back to users') }}
        </flux:button>
    </x-admin-page-title>

    <form wire:submit="save" class="grid grid-cols-1 items-start gap-base lg:grid-cols-12">
        <x-admin-card :title="__('Account details')" class="lg:col-span-8">
            <div class="grid gap-5 md:grid-cols-2">
                <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                <flux:input
                    wire:model="password"
                    :label="__('Password')"
                    type="password"
                    :required="! $user"
                    autocomplete="new-password"
                    :description="$user ? __('Leave empty to keep the current password.') : null"
                    viewable
                />
                <flux:input
                    wire:model="password_confirmation"
                    :label="__('Confirm password')"
                    type="password"
                    :required="! $user"
                    autocomplete="new-password"
                    viewable
                />
            </div>
        </x-admin-card>

        <x-admin-card :title="__('Status')" class="lg:col-span-4">
            <flux:switch wire:model="verified" :label="__('Email address verified')" :description="__('Unverified users are asked to verify their email address after login.')" />

            <x-slot:footer>
                <flux:button :href="route('admin.users.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" data-test="save-user-button">
                    {{ $user ? __('Save') : __('Create user') }}
                </flux:button>
            </x-slot:footer>
        </x-admin-card>
    </form>
</div>
