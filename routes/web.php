<?php

use App\Http\Controllers\SwitchLocaleController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Admin (the login and the other Fortify routes share the same prefix, see config/fortify.php)
Route::prefix(config('fortify.prefix'))->group(function () {
    Route::get('locale/{locale}', SwitchLocaleController::class)->name('locale');

    require __DIR__.'/admin.php';
    require __DIR__.'/settings.php';
});

Route::get('.well-known/passkey-endpoints', function () {
    return response()->json([
        'enroll' => route('security.edit'),
        'manage' => route('security.edit'),
    ]);
})->name('well-known.passkeys');
