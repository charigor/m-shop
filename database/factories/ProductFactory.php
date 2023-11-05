<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => 1,
            'tax_id' => 1,
            'quantity' => 3,
            'reference' => Str::random(5),
            'price' => 1000.00,
            'unity' => Str::random(5),
            'unit_price_ratio' => 0,
            'width' => 0,
            'height' => 0,
            'depth' => 0,
            'weight' => 0,
            'active' => rand(0, 1),
        ];
    }
}
