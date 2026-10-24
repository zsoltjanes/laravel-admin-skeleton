<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('common.partials.head')
    </head>
    <body class="min-h-screen bg-body-bg text-sm text-body antialiased">
        {{-- Sidenav (Paces .app-menu, dark) --}}
        <flux:sidebar
            sticky
            collapsible="mobile"
            class="w-sidenav gap-0! border-e border-sidenav-border bg-sidenav-bg p-0! shadow-[0_.0625rem_.125rem_#4c4c5c26]"
        >
            <div class="flex min-h-topbar items-center justify-between px-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5" wire:navigate>
                    <span class="flex size-7 items-center justify-center rounded-md bg-primary">
                        <x-app-logo-icon class="size-4 fill-current text-white" />
                    </span>
                    <span class="text-base font-bold text-white">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <flux:sidebar.collapse class="text-sidenav-item! hover:text-sidenav-item-hover! lg:hidden" />
            </div>

            <nav class="min-h-0 grow overflow-y-auto scrollbar-sidenav">
                <ul class="flex w-full flex-col gap-1.25 px-2.5 pb-16.5">
                    <x-admin-nav-title>{{ __('Main') }}</x-admin-nav-title>

                    <x-admin-nav-item icon="squares-2x2" :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-admin-nav-item>

                    <x-admin-nav-title>{{ __('Administration') }}</x-admin-nav-title>

                    <x-admin-nav-group icon="users" :label="__('Users')" :current="request()->routeIs('admin.users.*')">
                        <x-admin-nav-sub-item :href="route('admin.users.index')" :current="request()->routeIs('admin.users.index', 'admin.users.edit')">
                            {{ __('User list') }}
                        </x-admin-nav-sub-item>
                        <x-admin-nav-sub-item :href="route('admin.users.create')" :current="request()->routeIs('admin.users.create')">
                            {{ __('New user') }}
                        </x-admin-nav-sub-item>
                    </x-admin-nav-group>
                </ul>
            </nav>
        </flux:sidebar>

        {{-- Topbar (Paces .app-header, light) --}}
        <flux:header sticky class="min-h-topbar! bg-topbar-bg px-5! shadow-card">
            <flux:sidebar.toggle
                class="size-9.25! rounded-full! bg-primary! text-white! hover:bg-primary-hover! lg:hidden"
                icon="bars-3"
                :aria-label="__('Toggle sidebar')"
            />

            <flux:spacer />

            <div class="flex items-center gap-2.5">
                <x-appearance-toggle class="text-topbar-item! hover:text-topbar-item-hover! hover:bg-transparent!" />
                <x-locale-switcher class="[&_button]:text-topbar-item! [&_button:hover]:text-topbar-item-hover! [&_button:hover]:bg-transparent!" />

                <div class="h-4.5 w-px bg-zinc-600/35"></div>

                <flux:dropdown position="bottom" align="end">
                    <button
                        type="button"
                        class="flex cursor-pointer items-center px-3 text-topbar-item transition-colors hover:text-topbar-item-hover"
                        data-test="user-menu-button"
                    >
                        <flux:avatar
                            size="sm"
                            circle
                            :name="auth()->user()->name"
                            :initials="auth()->user()->initials()"
                            class="lg:me-3"
                        />

                        <span class="hidden items-center gap-1.5 lg:flex">
                            <span class="flex flex-col items-start">
                                <span class="text-[13px]/[14px] font-semibold">{{ auth()->user()->name }}</span>
                                <span class="mb-0.5 text-xs/none">{{ __('Administrator') }}</span>
                            </span>
                            <flux:icon.chevron-down variant="micro" />
                        </span>
                    </button>

                    <flux:menu class="min-w-44">
                        <div class="px-3.5 py-2">
                            <flux:heading class="truncate text-xs!">{{ __('Welcome back!') }}</flux:heading>
                            <flux:text class="truncate text-xs">{{ auth()->user()->email }}</flux:text>
                        </div>

                        <flux:menu.separator />

                        <flux:menu.item :href="route('profile.edit')" icon="user-circle" wire:navigate>
                            {{ __('Profile') }}
                        </flux:menu.item>
                        <flux:menu.item :href="route('security.edit')" icon="cog-6-tooth" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item
                                as="button"
                                type="submit"
                                icon="arrow-right-start-on-rectangle"
                                variant="danger"
                                class="w-full cursor-pointer"
                                data-test="logout-button"
                            >
                                {{ __('Log out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
