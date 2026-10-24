<?php

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee(__('Log in to your account'))
        ->assertSee(route('password.request'));
});

test('login screen lives under the admin prefix', function () {
    expect(route('login', absolute: false))->toBe('/admin/login')
        ->and(route('password.request', absolute: false))->toBe('/admin/forgot-password');
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('the seeded admin can log in', function () {
    $this->seed(UserSeeder::class);

    $this->post(route('login.store'), [
        'email' => config('app.admin.email'),
        'password' => config('app.admin.password'),
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    $this->get(route('dashboard'))->assertOk();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('users can not authenticate with an unknown email', function () {
    $this->post(route('login.store'), [
        'email' => 'nobody@example.com',
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('email and password are required', function () {
    $this->post(route('login.store'), [])
        ->assertSessionHasErrors(['email', 'password']);

    $this->assertGuest();
});

test('remember me sets the remember cookie', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
        'remember' => 'on',
    ]);

    $response->assertCookie(auth()->guard()->getRecallerName());
    expect($user->fresh()->remember_token)->not->toBeNull();
});

test('login is throttled after too many failed attempts', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertTooManyRequests();

    $this->assertGuest();

    RateLimiter::clear(Str::lower($user->email).'|127.0.0.1');
});

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->get(route('profile.edit'))->assertRedirect(route('login'));
});

test('users are redirected to the intended page after login', function () {
    $user = User::factory()->create();

    $this->get(route('profile.edit'))->assertRedirect(route('login'));

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('profile.edit'));
});

test('authenticated users are redirected away from the login page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));
});

test('users with two factor enabled are redirected to the two factor challenge', function () {
    enableTwoFactor();

    $user = User::factory()->withTwoFactor()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('two-factor.login'));

    $this->assertGuest();
});

test('users can logout', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('logout'))
        ->assertRedirect(route('home'));

    $this->assertGuest();
});

test('public registration is disabled', function () {
    if (Features::enabled(Features::registration())) {
        $this->markTestSkipped('Registration is enabled in config/fortify.php.');
    }

    expect(Route::has('register'))->toBeFalse();

    $this->get('/admin/register')->assertNotFound();
    $this->get(route('login'))->assertDontSee(__('Sign up'));
});
