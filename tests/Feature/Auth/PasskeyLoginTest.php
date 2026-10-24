<?php

use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::passkeys());
});

test('passkey login options can be requested by guests', function () {
    $this->getJson(route('passkey.login-options'))
        ->assertOk()
        ->assertJsonStructure(['options' => ['challenge']])
        ->assertSessionHas('passkey.verification_options');
});

test('passkey login options are not available to authenticated users', function () {
    $this->actingAs(User::factory()->create())
        ->getJson(route('passkey.login-options'))
        ->assertRedirect();
});

test('passkey login is rejected with an invalid credential', function () {
    $this->getJson(route('passkey.login-options'));

    $this->postJson(route('passkey.login'), ['credential' => ['id' => 'invalid']])
        ->assertUnprocessable();

    $this->assertGuest();
});

test('passkey endpoints are advertised for the browser', function () {
    $this->getJson(route('well-known.passkeys'))
        ->assertOk()
        ->assertJson([
            'enroll' => route('security.edit'),
            'manage' => route('security.edit'),
        ]);
});
