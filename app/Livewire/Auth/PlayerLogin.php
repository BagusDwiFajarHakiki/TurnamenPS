<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Player;
use App\Models\PlayerLoginAttempt;

class PlayerLogin extends Component
{
    public string $username = '';
    public string $password = '';
    public string $errorMessage = '';

    protected array $rules = [
        'username' => 'required|string',
        'password' => 'required|string',
    ];

    public function login()
    {
        $this->validate();

        $player = Player::where('username', $this->username)->first();

        // Standard Laravel authentication using the custom 'player' guard
        if (Auth::guard('player')->attempt([
            'username' => $this->username,
            'password' => $this->password,
        ])) {
            // Log successful attempt
            PlayerLoginAttempt::create([
                'player_id' => $player->id,
                'username_attempted' => $this->username,
                'ip_address' => request()->ip(),
                'success' => true,
            ]);

            // Update last login
            $player->update(['last_login_at' => now()]);

            return redirect()->intended('/dashboard');
        }

        // Log failed attempt
        PlayerLoginAttempt::create([
            'player_id' => $player?->id,
            'username_attempted' => $this->username,
            'ip_address' => request()->ip(),
            'success' => false,
        ]);

        $this->errorMessage = app()->getLocale() == 'id' 
            ? 'Username atau Password salah.' 
            : 'Invalid Username or Password.';
    }

    public function render()
    {
        return view('livewire.auth.player-login')
            ->layout('components.layouts.app', ['title' => __('Player Login')]);
    }
}
