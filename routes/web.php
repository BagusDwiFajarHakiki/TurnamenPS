<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Tournament\Show as TournamentShow;
use App\Livewire\Home;
use App\Livewire\Tournament\Registration as TournamentRegistration;
use App\Livewire\Auth\PlayerLogin;
use App\Livewire\Player\Dashboard as PlayerDashboard;

Route::get('/', Home::class)->name('home');
Route::get('/tournament/{slug}', TournamentShow::class)->name('tournament.show');
Route::get('/register-player', TournamentRegistration::class)->name('player.register');
Route::get('/login', PlayerLogin::class)->name('player.login');
Route::get('/dashboard', PlayerDashboard::class)->name('player.dashboard');

Route::any('/logout', function () {
    auth('player')->logout();
    return redirect('/');
})->name('player.logout');

Route::get('/set-locale/{lang}', function ($lang) {
    if (in_array($lang, ['id', 'en'])) {
        session()->put('locale', $lang);
    }
    return redirect()->back();
})->name('set-locale');
