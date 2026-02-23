<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $url = $this->faker->url();
        $cleanUrl = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $url);

        return [
            'uuid' => $this->faker->uuid(),
            'review_slug' => Str::slug($cleanUrl) . '-' . $this->faker->unique()->numberBetween(1, 100),
            'review_fullname' => $this->faker->name(),
            'review_link' => $url,
            'review_rating' => $this->faker->numberBetween(1, 5),
            'review_content' => $this->faker->sentence(4),
            'review_avatar' => 'img_reviews/avatar.jpg',
            'review_image' => 'img_reviews/review-' . $this->faker->numberBetween(1, 9) . '.jpg',
        ];
    }
}
