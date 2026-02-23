<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        return [
            'uuid'                => $this->faker->uuid(),
            'article_title'       => $title,
            'article_slug'        => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 100),
            'article_description' => $this->faker->text(),
            'article_image'       => 'img_articles/article-' . $this->faker->numberBetween(1, 9) . '.jpg',
            'article_category_uuid' => $this->faker->numberBetween(1, 2),
        ];
    }
}
