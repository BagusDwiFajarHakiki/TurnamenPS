<?php

namespace App\Livewire\Tournament;

use Livewire\Component;
use App\Models\Player;

class Registration extends Component
{
    public string $name = '';
    public string $username = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    public bool $isRegistered = false;
    public string $successMessage = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_num|max:50|unique:players,username',
            'phone' => [
                'required',
                'string',
                'regex:/^(08[1-9][0-9]{7,10}|628[1-9][0-9]{7,10}|\+628[1-9][0-9]{7,10})$/',
                'unique:players,phone',
            ],
            'password' => [
                'required',
                'confirmed',
                'string',
                'max:8',
            ],
        ];
    }

    public function register()
    {
        $this->validate();

        $player = Player::create([
            'name' => $this->name,
            'username' => $this->username,
            'phone' => $this->normalizePhone($this->phone),
            'login_code' => $this->password,
            'is_active' => true,
        ]);

        $this->isRegistered = true;
        
        $this->successMessage = app()->getLocale() == 'id' 
            ? 'Akun pemain Anda berhasil dibuat! Silakan masuk menggunakan username dan password Anda.'
            : 'Your player account was created successfully! Please log in using your username and password.';
    }

    public function render()
    {
        return view('livewire.tournament.registration')
            ->layout('components.layouts.app', ['title' => __('Register')]);
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-()]+/', '', $phone);

        if (str_starts_with($phone, '+62')) {
            return '0' . substr($phone, 3);
        }

        if (str_starts_with($phone, '62')) {
            return '0' . substr($phone, 2);
        }

        return $phone;
    }
}
