@php
    $th = 'px-2.5 py-2 text-start font-bold whitespace-nowrap first:ps-4.5 last:pe-4.5';
    $td = 'px-2.5 py-3 align-middle first:ps-4.5 last:pe-4.5';
    $iconButton = 'flex size-7.5 cursor-pointer items-center justify-center rounded-card border border-line text-body transition-colors hover:border-zinc-400';
    $pageIds = $this->users->pluck('id')->map(fn ($id) => (string) $id)->all();
@endphp

<div>
    <x-admin-page-title :title="__('Users')" />

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
            :title="__('Unverified')"
            :value="Number::format($this->stats['unverified'])"
            icon="envelope"
            color="warning"
            :label="__('Awaiting verification')"
            :label-value="Number::percentage($this->stats['total'] ? $this->stats['unverified'] / $this->stats['total'] * 100 : 0)"
        />
        <x-admin-stat-card
            :title="__('Two-factor authentication')"
            :value="Number::format($this->stats['two_factor'])"
            icon="shield-check"
            color="info"
            :label="__('Enabled')"
            :label-value="Number::percentage($this->stats['total'] ? $this->stats['two_factor'] / $this->stats['total'] * 100 : 0)"
        />
    </div>

    <x-admin-card flush>
        {{-- Toolbar: search, bulk delete, filters, new user --}}
        <x-slot:actions>
            <div class="flex w-full flex-wrap items-center justify-between gap-2.5">
                <div class="flex flex-wrap items-center gap-2.5">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        icon="magnifying-glass"
                        size="sm"
                        :placeholder="__('Search name or email...')"
                        class="w-64!"
                        clearable
                    />

                    @if ($selected)
                        <flux:button
                            wire:click="deleteSelected"
                            wire:confirm="{{ __('Are you sure you want to delete the selected users?') }}"
                            variant="danger"
                            size="sm"
                            icon="trash"
                            data-test="delete-selected-button"
                        >
                            {{ __('Delete (:count)', ['count' => count($selected)]) }}
                        </flux:button>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="me-2 font-semibold text-body">{{ __('Filter by:') }}</span>

                    <flux:select wire:model.live="status" size="sm" class="w-44!">
                        <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
                        <flux:select.option value="verified">{{ __('Verified') }}</flux:select.option>
                        <flux:select.option value="unverified">{{ __('Unverified') }}</flux:select.option>
                        <flux:select.option value="two_factor">{{ __('2FA enabled') }}</flux:select.option>
                    </flux:select>

                    <flux:select wire:model.live="perPage" size="sm" class="w-20!" :aria-label="__('Rows per page')">
                        @foreach ($this->perPageOptions() as $option)
                            <flux:select.option :value="$option">{{ $option }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:button :href="route('admin.users.create')" variant="danger" size="sm" icon="plus" wire:navigate>
                        {{ __('New user') }}
                    </flux:button>
                </div>
            </div>
        </x-slot:actions>

        <div class="relative w-full overflow-auto">
            <table class="w-full border-collapse text-body">
                <thead>
                    <tr class="border-b border-zinc-100 bg-zinc-100/25 text-2xs uppercase dark:border-zinc-700 dark:bg-zinc-700/25">
                        <th class="{{ $th }} w-[1%]">
                            <input
                                type="checkbox"
                                class="size-4.5 cursor-pointer rounded-sm accent-primary"
                                aria-label="{{ __('Select all') }}"
                                @checked($pageIds && ! array_diff($pageIds, $selected))
                                x-on:change="$wire.set('selected', $event.target.checked ? @js($pageIds) : [])"
                            />
                        </th>
                        @foreach (['name' => __('User'), 'email' => __('Email'), 'created_at' => __('Registered')] as $column => $label)
                            <th class="{{ $th }}">
                                <button type="button" wire:click="sort('{{ $column }}')" class="flex cursor-pointer items-center gap-1 uppercase hover:text-primary">
                                    {{ $label }}
                                    @if ($sortBy === $column)
                                        <flux:icon :icon="$sortDirection === 'asc' ? 'chevron-up' : 'chevron-down'" variant="micro" class="size-3.5" />
                                    @else
                                        <flux:icon.chevron-up-down variant="micro" class="size-3.5 opacity-40" />
                                    @endif
                                </button>
                            </th>
                        @endforeach
                        <th class="{{ $th }}">{{ __('Status') }}</th>
                        <th class="{{ $th }}">{{ __('2FA') }}</th>
                        <th class="{{ $th }} w-[1%] text-center!">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->users as $user)
                        <tr
                            wire:key="user-{{ $user->id }}"
                            @class([
                                'border-b border-zinc-100 transition-colors last:border-0 dark:border-zinc-700',
                                'bg-warning/10' => in_array((string) $user->id, $selected, true),
                                'hover:bg-zinc-100/45 dark:hover:bg-zinc-700/45' => ! in_array((string) $user->id, $selected, true),
                            ])
                        >
                            <td class="{{ $td }}">
                                <input type="checkbox" wire:model.live="selected" value="{{ $user->id }}" class="size-4.5 cursor-pointer rounded-sm accent-primary" aria-label="{{ __('Select :name', ['name' => $user->name]) }}" />
                            </td>
                            <td class="{{ $td }}">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" circle :name="$user->name" :initials="$user->initials()" />
                                    <div>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="mb-1.25 block font-semibold hover:text-primary" wire:navigate>{{ $user->name }}</a>
                                        @if ($user->is(auth()->user()))
                                            <p class="text-2xs text-muted">{{ __('This is you') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="{{ $td }}">{{ $user->email }}</td>
                            <td class="{{ $td }} whitespace-nowrap">
                                {{ $user->created_at?->isoFormat('LL') }}
                                <small class="text-muted">{{ $user->created_at?->isoFormat('LT') }}</small>
                            </td>
                            <td class="{{ $td }}">
                                @if ($user->hasVerifiedEmail())
                                    <span class="inline-flex rounded-card bg-success/15 px-2 text-2xs font-semibold text-success">{{ __('Verified') }}</span>
                                @else
                                    <span class="inline-flex rounded-card bg-warning/15 px-2 text-2xs font-semibold text-warning">{{ __('Unverified') }}</span>
                                @endif
                            </td>
                            <td class="{{ $td }}">
                                @if ($user->two_factor_confirmed_at)
                                    <span class="inline-flex rounded-card bg-info/15 px-2 text-2xs font-semibold text-info">{{ __('On') }}</span>
                                @else
                                    <span class="inline-flex rounded-card bg-zinc-100 px-2 text-2xs font-semibold text-muted dark:bg-zinc-700">{{ __('Off') }}</span>
                                @endif
                            </td>
                            <td class="{{ $td }}">
                                <div class="flex justify-center gap-1.25">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="{{ $iconButton }}" wire:navigate aria-label="{{ __('Edit') }}">
                                        <flux:icon.pencil-square variant="micro" />
                                    </a>
                                    @unless ($user->is(auth()->user()))
                                        <button
                                            type="button"
                                            wire:click="delete({{ $user->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete :name?', ['name' => $user->name]) }}"
                                            class="{{ $iconButton }} hover:border-danger! hover:text-danger"
                                            aria-label="{{ __('Delete') }}"
                                        >
                                            <flux:icon.trash variant="micro" />
                                        </button>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4.5 py-10 text-center text-muted">{{ __('No users found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot:footer>
            {{ $this->users->links('admin.partials.pagination') }}
        </x-slot:footer>
    </x-admin-card>
</div>
