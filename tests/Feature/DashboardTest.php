<?php

use App\Livewire\Admin\Dashboard;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk();
});

test('dashboard lives under the admin prefix', function () {
    expect(route('dashboard', absolute: false))->toBe('/admin');
});

test('top bar shows the user menu with the appearance and language switchers', function () {
    $user = User::factory()->create(['name' => 'Admin User']);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('data-test="user-menu-button"', false)
        ->assertSee('Admin User')
        ->assertSee($user->email)
        ->assertSee(route('profile.edit'))
        ->assertSee('data-test="logout-button"', false)
        ->assertSee(__('Toggle dark mode'))
        ->assertSee(route('locale', 'hu'))
        ->assertSee(route('locale', 'en'));
});

test('dashboard shows the user stats and the charts', function () {
    User::factory()->count(3)->create();
    User::factory()->unverified()->create();

    $this->actingAs(User::factory()->withTwoFactor()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('data-chart=', false)
        ->assertSee(__('Users by security'))
        ->assertSee(__('New users per month'))
        ->assertSee(__('Show data as table'));
});

test('dashboard security chart groups users without overlap', function () {
    User::factory()->count(2)->create();
    User::factory()->unverified()->create();
    $admin = User::factory()->withTwoFactor()->create();

    $this->actingAs($admin);

    $chart = Livewire\Livewire::test(Dashboard::class)->instance()->securityChart;

    expect($chart['series'])->toBe([1, 2, 1])
        ->and(array_sum($chart['series']))->toBe(User::count());
});
