<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid'                  => $this->faker->uuid(),
            'activity_slug'         => $this->faker->slug(),
            'activity_title'        => $this->faker->sentence(3),
            'activity_description'  => $this->faker->text(),
            'activity_image'        => $this->faker->imageUrl(),
            'activity_category_uuid' => $this->faker->numberBetween(1, 2),
            'activity_location'     => $this->faker->address(),
            'activity_date'         => $this->faker->dateTime(),
        ];
    }
}
