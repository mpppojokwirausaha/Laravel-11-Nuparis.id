<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\sosialMedia>
 */
class SosialMediaFactory extends Factory
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
            'sosial_media_fullname' => $this->faker->name(),
            'sosial_media_slug'     => Str::slug($cleanUrl) . '-' . $this->faker->unique()->numberBetween(1, 100),
            'sosial_media_url'      => $url,
            'sosial_media_image'    => 'img_sosial_media/sosial_media-' . $this->faker->numberBetween(1, 9) . '.jpg',
            'sosial_media_content'  => $this->faker->text(),
            'sosial_media_avatar'   => 'img_sosial_media/sosial_media-' . $this->faker->numberBetween(1, 9) . '.jpg',
        ];
    }
}
