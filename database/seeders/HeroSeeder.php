<?php

namespace Database\Seeders;

use \App\Models\Hero;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $heroes = [
            [
                'uuid' => '6258d05e-7666-4637-b57d-f4ef72e0d7cc',
                'hero_name' => 'video_nuparis',
                'hero_status' => true,
                'hero_slug' => 'video_nuparis',
                'hero_assets' => 'assets_hero/video_nuparis.mp4',
            ],
            [
                'uuid' => '70b61cd5-8e46-465b-b188-6ec4ec725dc1',
                'hero_name' => 'slide-1',
                'hero_status' => true,
                'hero_slug' => 'slide-1',
                'hero_assets' => 'assets_hero/slide-1.png',
            ],
            [
                'uuid' => '7f45ae1c-1e5c-4a38-ab2e-0578ba50f595',
                'hero_name' => 'slide-2',
                'hero_status' => true,
                'hero_slug' => 'slide-2',
                'hero_assets' => 'assets_hero/slide-2.png',
            ],
            [
                'uuid' => 'a9712230-c39b-4bbb-a1d2-91ccdc55e0b4',
                'hero_name' => 'slide-3',
                'hero_status' => true,
                'hero_slug' => 'slide-3',
                'hero_assets' => 'assets_hero/slide-3.png',
            ],
            [
                'uuid' => '898fb85b-ad5b-416e-ae71-da5472a95327',
                'hero_name' => 'slide-4',
                'hero_status' => true,
                'hero_slug' => 'slide-4',
                'hero_assets' => 'assets_hero/slide-4.png',
            ],
        ];

        foreach ($heroes as $hero) {
            Hero::create($hero);
        }
    }
}
