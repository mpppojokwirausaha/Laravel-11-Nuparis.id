<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityCategory>
 */
class ActivityCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activityCategory = $this->faker->name();
        return [
            'uuid' => $this->faker->uuid(),
            'activity_category_name' => $activityCategory,
            'activity_category_slug' => Str::slug($activityCategory) . '-' . $this->faker->unique()->numberBetween(1, 100),
        ];
    }
}
