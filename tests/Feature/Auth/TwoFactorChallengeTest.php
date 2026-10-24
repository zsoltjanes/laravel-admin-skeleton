<?php

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

beforeEach(function () {
    enableTwoFactor();
});

/**
 * A user with confirmed 2FA using a real TOTP secret, plus that secret.
 *
 * @return array{0: User, 1: string}
 */
function userWithTotp(): array
{
    $secret = app(Google2FA::class)->generateSecretKey();

    $user = User::factory()->create();
    $user->forceFill([
        'two_factor_secret' => encrypt($secret),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1', 'recovery-code-2'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    return [$user, $secret];
}

function startTwoFactorLogin(User $user): void
{
    test()->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('two-factor.login'));
}

test('two factor challenge redirects to login when not authenticated', function () {
    $this->get(route('two-factor.login'))->assertRedirect(route('login'));
});

test('two factor challenge can be rendered', function () {
    [$user] = userWithTotp();

    startTwoFactorLogin($user);

    $this->get(route('two-factor.login'))->assertOk();
    $this->assertGuest();
});

test('users can complete the challenge with a valid code', function () {
    [$user, $secret] = userWithTotp();

    startTwoFactorLogin($user);

    $this->post(route('two-factor.login.store'), [
        'code' => app(Google2FA::class)->getCurrentOtp($secret),
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('users can not complete the challenge with an invalid code', function () {
    [$user] = userWithTotp();

    startTwoFactorLogin($user);

    $this->post(route('two-factor.login.store'), ['code' => '000000'])
        ->assertRedirect(route('two-factor.login'))
        ->assertSessionHasErrors('code');

    $this->assertGuest();
});

test('users can complete the challenge with a recovery code once', function () {
    [$user] = userWithTotp();

    startTwoFactorLogin($user);

    $this->post(route('two-factor.login.store'), ['recovery_code' => 'recovery-code-1'])
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->recoveryCodes())->not->toContain('recovery-code-1');
});

test('users can not complete the challenge with an invalid recovery code', function () {
    [$user] = userWithTotp();

    startTwoFactorLogin($user);

    $this->post(route('two-factor.login.store'), ['recovery_code' => 'wrong-code'])
        ->assertRedirect(route('two-factor.login'))
        ->assertSessionHasErrors('recovery_code');

    $this->assertGuest();
});
