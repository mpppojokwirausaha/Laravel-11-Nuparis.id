<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArticleCategory>
 */
class ArticleCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $articleCategory = $this->faker->name();
        return [
            'uuid' => $this->faker->uuid(),
            'article_category_name' => $articleCategory,
            'article_category_slug' => Str::slug($articleCategory) . '-' . $this->faker->unique()->numberBetween(1, 100),
        ];
    }
}
