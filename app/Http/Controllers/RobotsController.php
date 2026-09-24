<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /management',
            'Disallow: /dashboard',
            'Disallow: /edit-profile',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /report/download',
            'Disallow: /clear',
            'Disallow: /down',
            'Disallow: /up',
            'Disallow: /linkstorage',
            'Disallow: /*/data',
            'Disallow: /toss',
            'Sitemap: ' . route('sitemap'),
        ];

        return response(implode("\n", $lines) . "\n")
            ->header('Content-Type', 'text/plain');
    }
}
