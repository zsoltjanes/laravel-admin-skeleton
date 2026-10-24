<?php

test('locale can be switched', function () {
    $this->from(route('login'))
        ->get(route('locale', 'hu'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('locale', 'hu');

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Bejelentkezés');

    $this->get(route('locale', 'en'))->assertSessionHas('locale', 'en');

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Log in to your account');
});

test('unsupported locale is rejected', function () {
    $this->get(route('locale', 'de'))
        ->assertNotFound()
        ->assertSessionMissing('locale');
});

test('login page shows the language and appearance switchers', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee(route('locale', 'hu'))
        ->assertSee(route('locale', 'en'))
        ->assertSee(__('Toggle dark mode'));
});
