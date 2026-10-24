<?php

namespace App\Livewire\Admin\Users;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

/**
 * Creates a new user or edits an existing one.
 */
class Form extends Component
{
    use PasswordValidationRules;

    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $verified = true;

    public function mount(?User $user = null): void
    {
        $this->user = $user?->exists ? $user : null;

        if ($this->user) {
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->verified = $this->user->hasVerifiedEmail();
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user)],
            // Editing: an empty password keeps the current one
            'password' => $this->user
                ? ['nullable', 'string', Password::default(), 'confirmed']
                : $this->passwordRules(),
            'verified' => ['boolean'],
        ]);

        $user = $this->user ?? new User;

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (filled($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->email_verified_at = $validated['verified'] ? ($user->email_verified_at ?? now()) : null;
        $user->save();

        Flux::toast(variant: 'success', text: $this->user ? __('User updated.') : __('User created.'));

        $this->redirectRoute('admin.users.index', navigate: true);
    }

    public function render(): View
    {
        return view('admin.users.form')
            ->title($this->user ? __('Edit user') : __('New user'));
    }
}
