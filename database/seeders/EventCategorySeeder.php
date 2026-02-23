<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventCategory;
use Faker\Generator as Faker;

class EventCategorySeeder extends Seeder
{
    public function run(): void
    {
        // EventCategory::factory(10)->create();
        $eventCategories = [
            [
                'uuid' => '0c28d67d-b709-4c61-be3f-34f75684d83b',
                'event_category_name' => 'Promo Event',
                'event_category_slug' => 'promo-event',
                'created_at' => '2025-06-02 15:14:34',
                'updated_at' => '2025-06-02 15:14:34',
            ],
            [
                'uuid' => '14d8af7d-89be-4a44-b61e-811389d18326',
                'event_category_name' => 'Training',
                'event_category_slug' => 'training',
                'created_at' => '2025-05-21 07:58:08',
                'updated_at' => '2025-05-21 07:58:08',
            ],
            [
                'uuid' => '659fc929-9cff-4c8e-91e3-7058978e28e9',
                'event_category_name' => 'Seminar',
                'event_category_slug' => 'seminar',
                'created_at' => '2025-05-21 08:59:55',
                'updated_at' => '2025-05-21 08:59:55',
            ]
        ];

        foreach ($eventCategories as $data) {
            $model = new EventCategory($data);
            $model->timestamps = false;
            $model->save();
        }
    }
}
