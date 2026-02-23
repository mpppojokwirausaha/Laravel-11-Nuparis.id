<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->sentence(3);
        return [
            'uuid' => $this->faker->uuid(),
            'event_category_name' => $name,
            'event_category_slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 100),
        ];
    }
}
