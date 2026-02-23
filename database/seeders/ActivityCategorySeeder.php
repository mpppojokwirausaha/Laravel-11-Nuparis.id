<?php

namespace Database\Seeders;

use App\Models\ActivityCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ActivityCategory::factory(10)->create();
        $activityCategory = [
            [
                'uuid' => '2628fcbc-e8a9-4eb0-9c0f-ea5f26d92f6a',
                'activity_category_name' => 'Education',
                'activity_category_slug' => 'education',
                'created_at' => '2025-05-21 22:04:35',
                'updated_at' => '2025-05-21 22:04:35',
            ],
            [
                'uuid' => '3bb55ec0-b275-4b34-bcda-7414ff511d76',
                'activity_category_name' => 'Seminar',
                'activity_category_slug' => 'seminar',
                'created_at' => '2025-05-21 15:01:11',
                'updated_at' => '2025-05-21 15:01:11',
            ],
            [
                'uuid' => 'dbcf05bd-b74f-43fb-9a12-21edceaf3060',
                'activity_category_name' => 'Sosialisasi',
                'activity_category_slug' => 'sosialisasi',
                'created_at' => '2025-05-21 15:07:27',
                'updated_at' => '2025-05-21 15:07:27',
            ]
        ];
        foreach ($activityCategory as $data) {
            $model = new ActivityCategory($data);
            $model->timestamps = false;
            $model->save();
        }
    }
}
