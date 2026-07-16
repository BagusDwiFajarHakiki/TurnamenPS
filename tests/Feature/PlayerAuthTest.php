<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Player;
use Livewire\Livewire;
use App\Livewire\Tournament\Registration;
use App\Livewire\Auth\PlayerLogin;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_registration_validation_rules()
    {
        // 1. Password must be at least 8 characters
        Livewire::test(Registration::class)
            ->set('name', 'John Doe')
            ->set('username', 'johndoe')
            ->set('password', 'Sec12')
            ->set('password_confirmation', 'Sec12')
            ->call('register')
            ->assertHasErrors(['password']);

        // 2. Password must contain numbers
        Livewire::test(Registration::class)
            ->set('name', 'John Doe')
            ->set('username', 'johndoe')
            ->set('password', 'SecretPass')
            ->set('password_confirmation', 'SecretPass')
            ->call('register')
            ->assertHasErrors(['password']);

        // 3. Password must contain uppercase and lowercase letters
        Livewire::test(Registration::class)
            ->set('name', 'John Doe')
            ->set('username', 'johndoe')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    public function test_player_registration_success()
    {
        Livewire::test(Registration::class)
            ->set('name', 'John Doe')
            ->set('username', 'johndoe')
            ->set('email', 'john@example.com')
            ->set('password', 'Secret123')
            ->set('password_confirmation', 'Secret123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertSet('isRegistered', true);

        $this->assertDatabaseHas('players', [
            'username' => 'johndoe',
            'email' => 'john@example.com',
        ]);

        $player = Player::where('username', 'johndoe')->first();
        $this->assertTrue(\Hash::check('Secret123', $player->login_code));
    }

    public function test_player_login_success()
    {
        $player = Player::create([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'login_code' => 'Secret123', // auto-hashed in model
            'is_active' => true,
        ]);

        Livewire::test(PlayerLogin::class)
            ->set('username', 'johndoe')
            ->set('password', 'Secret123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($player, 'player');
    }

    public function test_player_login_fails_with_incorrect_password()
    {
        $player = Player::create([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'login_code' => 'Secret123', // auto-hashed in model
            'is_active' => true,
        ]);

        Livewire::test(PlayerLogin::class)
            ->set('username', 'johndoe')
            ->set('password', 'WrongPassword123')
            ->call('login')
            ->assertSet('errorMessage', app()->getLocale() == 'id' ? 'Username atau Password salah.' : 'Invalid Username or Password.');

        $this->assertGuest('player');
    }
}
