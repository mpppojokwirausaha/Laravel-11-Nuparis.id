<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockMemberAccessToPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->hasRole('member')) {
            return redirect()->route('landingpage');
        }

        return $next($request);
    }
}
