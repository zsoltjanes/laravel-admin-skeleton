<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('common.partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="grid min-h-dvh lg:grid-cols-2">
            @php($image = 'images/auth.webp')

            {{-- Left half: public/images/auth.webp if present, otherwise the logo on a gradient --}}
            <div class="relative hidden overflow-hidden bg-neutral-900 lg:block dark:border-e dark:border-neutral-800">
                @if (file_exists(public_path($image)))
                    <img src="{{ asset($image) }}" alt="" class="absolute inset-0 size-full object-cover">
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-6 bg-[radial-gradient(ellipse_at_top_left,#3f3f46,#09090b_65%)] text-white">
                        <x-app-logo-icon class="w-1/3 max-w-48 fill-current drop-shadow-2xl" />
                        <span class="text-2xl font-semibold">{{ config('app.name', 'Laravel') }}</span>
                    </div>
                @endif
            </div>

            {{-- Right half: the form --}}
            <div class="relative flex items-center justify-center p-8">
                <div class="absolute end-4 top-4 flex items-center gap-1">
                    <x-appearance-toggle />
                    <x-locale-switcher />
                </div>

                <div class="flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <span class="flex h-9 w-9 items-center justify-center rounded-md">
                            <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                        </span>

                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
