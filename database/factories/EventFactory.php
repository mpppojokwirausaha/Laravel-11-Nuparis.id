<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        return [
            'uuid'              => $this->faker->uuid(),
            'event_title'       => $title,
            'event_slug'        => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 100),
            'event_description' => $this->faker->paragraph(),
            'event_image'       => 'img_event/event-' . $this->faker->numberBetween(1, 8) . '.jpg',
            'event_date'        => $this->faker->date(),
            'event_location'    => $this->faker->address(),
            'event_status'      => $this->faker->randomElement(['Active', 'Inactive']),
            'event_price'       => $this->faker->numberBetween(0, 500),
            'event_category_uuid' => $this->faker->randomElement([
                '9b3b5c6e-3f8c-4d5a-9b3b-5c6e3f8c4d5a',
                '9b3b5c6e-3f8c-4d5a-9b3b-5c6e3f8c4d7b'
            ]),
        ];
    }
}
