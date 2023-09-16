<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ProductLangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'locale' => 'en',
            'name' => fake()->text(20),
            'description' => fake()->paragraph(3),
            'short_description' => fake()->paragraph(1),
            'link_rewrite' => Str::random(5),
            'meta_title' => fake()->paragraph(1),
            'meta_description' => fake()->paragraph(3),
            'meta_keywords' => fake()->title(),
            'available_now' => 1,
            'available_later' => 0,
            'delivery_in_stock' => 0,
            'delivery_out_stock' => 0,
        ];
    }

}
