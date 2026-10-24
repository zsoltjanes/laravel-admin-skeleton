<?php

namespace App\Livewire\Admin\Settings;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DeleteUserForm extends Component
{
    public function render(): View
    {
        return view('admin.settings.delete-user-form');
    }
}
