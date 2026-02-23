<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Info;

class InfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Info::create([
            'uuid' => 'f8a4eedc-8b09-4ed0-a172-ba7bf6316486',
            'no_whatsapp' => env('NO_WHATSAPP'),
            'address' => env('ADDRESS'),
            'email' => env('EMAIL'),
            'instagram' => env('INSTAGRAM', 'https://www.instagram.com'),
            'youtube' => env('YOUTUBE', 'https://www.youtube.com'),
            'logo' => env('LOGO', 'https://via.placeholder.com/150'),
            'meta_domain' => 'https://pw.nuparis.id',
            'meta_title' => 'lorem ipsum dolor sit amet',
            'meta_desc' => 'lorem ipsum dolor sit amet',
            'meta_keywords' => 'lorem ipsum dolor sit amet',
            'meta_image' => env('META_IMAGE', 'meta/01JVVK1RJBHPS0CJ9D28W57AHG.png'),
        ]);
    }
}
