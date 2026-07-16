<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\MatchCompleted::class,
            \App\Listeners\UpdatePlayerAggregate::class
        );

        \Filament\Notifications\Livewire\Notifications::alignment(\Filament\Support\Enums\Alignment::Center);
        \Filament\Notifications\Livewire\Notifications::verticalAlignment(\Filament\Support\Enums\VerticalAlignment::Start);
    }
}
