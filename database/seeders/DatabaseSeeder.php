<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            EventSeeder::class,
            EventCategorySeeder::class,
            ActivitySeeder::class,
            ActivityCategorySeeder::class,
            PartnerSeeder::class,
            ArticleSeeder::class,
            ArticleCategorySeeder::class,
            NewsSeeder::class,
            InfoSeeder::class,
            ReviewSeeder::class,
            ConsultantSpecializationSeeder::class,
            TicketStatusSeeder::class,
            TicketSeeder::class,
            HeroSeeder::class,
            PropertySeeder::class,
            TicketSeeder::class
        ]);
    }
}
