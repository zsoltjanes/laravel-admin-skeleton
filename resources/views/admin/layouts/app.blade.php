<x-admin::layouts.app.sidebar :title="$title ?? null">
    {{-- Page content (Paces .page-content > main + .footer) --}}
    <flux:main class="flex min-h-[calc(100dvh-var(--spacing-topbar))] flex-col p-0!">
        <div class="w-full grow px-5 pb-5">
            {{ $slot }}
        </div>

        <footer class="mt-auto flex items-center border-t border-line bg-card px-5 py-3 text-body">
            <div class="grid w-full grid-cols-1 gap-base md:grid-cols-2">
                <div class="text-center md:text-start">
                    © {{ now()->year }} {{ config('app.name', 'Laravel') }}
                </div>
            </div>
        </footer>
    </flux:main>
</x-admin::layouts.app.sidebar>
