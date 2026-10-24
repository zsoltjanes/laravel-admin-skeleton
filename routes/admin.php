<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Users;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/', Dashboard::class)->name('dashboard');

    Route::name('admin.')->group(function () {
        Route::livewire('users', Users\Index::class)->name('users.index');
        Route::livewire('users/create', Users\Form::class)->name('users.create');
        Route::livewire('users/{user}/edit', Users\Form::class)->name('users.edit');
    });
});
