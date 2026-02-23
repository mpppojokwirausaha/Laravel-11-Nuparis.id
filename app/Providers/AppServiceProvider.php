<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;

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
        Filament::serving(function () {
            Filament::registerUserMenuItems([
                'profile' => UserMenuItem::make()
                    ->label('Ubah Profil')
                    ->url(route('filament.pages.edit-profile'))
                    ->icon('heroicon-o-user'),

                'logout' => UserMenuItem::make()
                    ->label('Keluar')
                    ->url(route('filament.management.auth.logout'))
            ]);
        });
    }
}
