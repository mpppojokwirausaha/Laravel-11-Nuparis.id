<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        if ($user->hasRole('member')) {
            return redirect()->route('landingpage');
        }

        // GANTI ini dengan nama route dashboard panel kamu
        return redirect()->route('filament.management.pages.dashboard');
    }
}
