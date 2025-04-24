<?php

namespace Database\Factories;

use App\Models\Product;
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
    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            $fakeImagesPath = storage_path('app/public/fake-images');

            $allImages = collect(glob($fakeImagesPath . '/*.{jpg,jpeg,png}', GLOB_BRACE));

            if ($allImages->isNotEmpty()) {
                $selectedImages = collect($allImages->random(rand(1, min(3, $allImages->count()))));

                foreach ($selectedImages as $index => $imagePath) {
                    $product
                        ->addMedia($imagePath)
                        ->withCustomProperties([
                            'main_image' => $index === 0 ? 1 : 0,  // Первый элемент как основной
                            'order' => $index,  // Порядковый номер
                        ])
                        ->preservingOriginal()
                        ->toMediaCollection('images');
                }
            }
        });
    }
}
