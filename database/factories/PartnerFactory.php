<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PartnerFactory extends Factory
{
    public function definition(): array
    {

        $name = $this->faker->company();
        return [
            'uuid' => $this->faker->uuid(),
            'partner_name' => $name,
            'partner_slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 100),
            'partner_phone' => $this->faker->phoneNumber(),
            'partner_email' => $this->faker->email(),
            'partner_description' => $this->faker->sentence(3),
            'partner_image' => $this->faker->imageUrl(),
            'partner_address' => $this->faker->address(),
            'partner_status' => $this->faker->numberBetween(0, 1),
        ];
    }
}
