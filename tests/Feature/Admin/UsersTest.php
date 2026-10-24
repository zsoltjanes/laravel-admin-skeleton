<?php

use App\Livewire\Admin\Users\Form;
use App\Livewire\Admin\Users\Index;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['name' => 'Admin User', 'email' => 'admin@example.com']);
    $this->actingAs($this->admin);
});

test('guests can not open the user pages', function () {
    auth()->logout();

    $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    $this->get(route('admin.users.create'))->assertRedirect(route('login'));
});

test('user list can be rendered', function () {
    User::factory()->create(['name' => 'Jane Listed']);

    $this->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Jane Listed')
        ->assertSee(route('admin.users.create'));
});

test('sidebar has the users dropdown with its pages', function () {
    $this->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee(route('admin.users.index'))
        ->assertSee(route('admin.users.create'))
        ->assertSee(__('User list'));
});

test('users can be searched by name and email', function () {
    User::factory()->create(['name' => 'Alice Search', 'email' => 'alice@example.com']);
    User::factory()->create(['name' => 'Bob Other', 'email' => 'bob@example.com']);

    Livewire::test(Index::class)
        ->set('search', 'Alice')
        ->assertSee('Alice Search')
        ->assertDontSee('Bob Other')
        ->set('search', 'bob@')
        ->assertSee('Bob Other')
        ->assertDontSee('Alice Search');
});

test('users can be filtered by status', function () {
    User::factory()->unverified()->create(['name' => 'Not Verified']);
    User::factory()->withTwoFactor()->create(['name' => 'Has Twofactor']);

    Livewire::test(Index::class)
        ->set('status', 'unverified')
        ->assertSee('Not Verified')
        ->assertDontSee('Has Twofactor')
        ->set('status', 'two_factor')
        ->assertSee('Has Twofactor')
        ->assertDontSee('Not Verified');
});

test('users can be sorted', function () {
    User::factory()->create(['name' => 'Zed Last']);
    User::factory()->create(['name' => 'Aaron First']);

    Livewire::test(Index::class)
        ->call('sort', 'name')
        ->assertSet('sortBy', 'name')
        ->assertSet('sortDirection', 'asc')
        ->assertSeeInOrder(['Aaron First', 'Admin User', 'Zed Last'])
        ->call('sort', 'name')
        ->assertSet('sortDirection', 'desc')
        ->assertSeeInOrder(['Zed Last', 'Admin User', 'Aaron First']);
});

test('only whitelisted columns can be sorted', function () {
    Livewire::test(Index::class)
        ->call('sort', 'password')
        ->assertSet('sortBy', 'created_at');
});

test('the list is paginated', function () {
    User::factory()->count(12)->create();

    $component = Livewire::test(Index::class);

    expect($component->get('perPage'))->toBe(10)
        ->and($component->instance()->users->count())->toBe(10)
        ->and($component->instance()->users->total())->toBe(13);
});

test('a user can be deleted', function () {
    $user = User::factory()->create();

    Livewire::test(Index::class)->call('delete', $user->id);

    expect($user->fresh())->toBeNull();
});

test('selected users can be deleted at once', function () {
    $users = User::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->set('selected', $users->take(2)->pluck('id')->map(fn ($id) => (string) $id)->all())
        ->call('deleteSelected')
        ->assertSet('selected', []);

    expect(User::whereKey($users->pluck('id'))->count())->toBe(1);
});

test('the logged in user can not delete themselves from the list', function () {
    Livewire::test(Index::class)
        ->call('delete', $this->admin->id)
        ->set('selected', [(string) $this->admin->id])
        ->call('deleteSelected');

    expect($this->admin->fresh())->not->toBeNull();
});

test('create user page can be rendered', function () {
    $this->get(route('admin.users.create'))->assertOk();
});

test('a user can be created', function () {
    Livewire::test(Form::class)
        ->set('name', 'New Person')
        ->set('email', 'new@example.com')
        ->set('password', 'secret-password')
        ->set('password_confirmation', 'secret-password')
        ->set('verified', false)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    $user = User::firstWhere('email', 'new@example.com');

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('New Person')
        ->and(Hash::check('secret-password', $user->password))->toBeTrue()
        ->and($user->hasVerifiedEmail())->toBeFalse();
});

test('creating a user validates the input', function () {
    Livewire::test(Form::class)
        ->set('name', '')
        ->set('email', 'admin@example.com')
        ->set('password', 'a')
        ->set('password_confirmation', 'b')
        ->call('save')
        ->assertHasErrors(['name' => 'required', 'email' => 'unique', 'password']);
});

test('edit user page can be rendered', function () {
    $user = User::factory()->create(['name' => 'Edit Me']);

    $this->get(route('admin.users.edit', $user))
        ->assertOk()
        ->assertSee('Edit Me');
});

test('a user can be updated without changing the password', function () {
    $user = User::factory()->create(['password' => Hash::make('old-password')]);

    Livewire::test(Form::class, ['user' => $user])
        ->assertSet('name', $user->name)
        ->set('name', 'Renamed')
        ->set('email', 'renamed@example.com')
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Renamed')
        ->and($user->email)->toBe('renamed@example.com')
        ->and(Hash::check('old-password', $user->password))->toBeTrue();
});

test('a user password can be changed', function () {
    $user = User::factory()->create();

    Livewire::test(Form::class, ['user' => $user])
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('save')
        ->assertHasNoErrors();

    expect(Hash::check('brand-new-password', $user->fresh()->password))->toBeTrue();
});

test('a user can keep their own email when updating', function () {
    $user = User::factory()->create(['email' => 'keep@example.com']);

    Livewire::test(Form::class, ['user' => $user])
        ->set('name', 'Still Me')
        ->call('save')
        ->assertHasNoErrors();
});
