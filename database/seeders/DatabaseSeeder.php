<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\FeatureValueProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //                \App\Models\Lang::insert(
        //                [
        //                    [
        //                    'name' => 'ukrainian',
        //                    'active' => 1,
        //                    'code' => 'uk',
        //                    'date_format' => 'Y-m-d',
        //                    'date_format_full' => 'Y-m-d H:i:s',
        //                    'created_at' => now(),
        //                    'updated_at' => now()
        //                    ],
        //                    [
        //                    'name' => 'english',
        //                    'active' => 1,
        //                    'code' => 'en',
        //                    'date_format' => 'Y-m-d',
        //                    'date_format_full' => 'Y-m-d H:i:s',
        //                    'created_at' => now(),
        //                    'updated_at' => now()
        //                    ]
        //                ]
        //                );
        //                \App\Models\Feature::factory(30)->create()->each(function($feature){
        //                     $faker = \Faker\Factory::create('uk_UA');
        //                     $feature->translation()->create(
        //                         [
        //                         'locale' => 'uk',
        //                         'name' => $faker->country,
        //                        ]
        //                     );
        //                     $faker = \Faker\Factory::create('en_US');
        //                     $feature->translation()->create(                 [
        //                         'locale' => 'en',
        //                         'name' => $faker->country,
        //                     ]);
        //
        //                     $featureValue =  $feature->featureValue()->create(
        //                         [
        //                             'custom' => rand(0,1)
        //                         ]
        //                     );
        //                     $faker = \Faker\Factory::create('uk_UA');
        //                     $featureValue->translation()->create(
        //                         [
        //                             'locale' => 'uk',
        //                             'value' => $faker->city
        //                         ]
        //                     );
        //                     $faker = \Faker\Factory::create('en_US');
        //                     $featureValue->translation()->create([
        //                         'locale' => 'en',
        //                         'value' => $faker->city,
        //                     ]);
        //                 });
        //        $products = Product::where('id','>', 10000)->get();
        //        foreach ($products as $product) {
        //            $fakeImagesPath = storage_path('app/public/fake-images');
        //
        //            $allImages = collect(glob($fakeImagesPath . '/*.{jpg,jpeg,png}', GLOB_BRACE));
        //
        //            if ($allImages->isNotEmpty()) {
        //                $selectedImages = collect($allImages->random(rand(1, min(3, $allImages->count()))));
        //
        //                foreach ($selectedImages as $index => $imagePath) {
        //                    $product
        //                        ->addMedia($imagePath)
        //                        ->withCustomProperties([
        //                            'main_image' => $index === 0 ? 1 : 0,  // Первый элемент как основной
        //                            'order' => $index,  // Порядковый номер
        //                        ])
        //                        ->preservingOriginal()
        //                        ->toMediaCollection('image');
        //                }
        //            }
        //        }
        \App\Models\Product::factory(400)->create()->each(function ($product) {
            $productLang = \App\Models\ProductLang::factory(1)->make();
            $product->translation()->saveMany($productLang);
            $featureValues = \App\Models\FeatureValue::all()->random(rand(1, 5));
            foreach ($featureValues as $item) {
                FeatureValueProduct::create(
                    [
                        'product_id' => $product->id,
                        'feature_value_id' => $item->id,
                        'feature_id' => $item->feature_id,
                    ]
                );
            }
            $categoryIds = \App\Models\Category::pluck('id')->toArray(); // список всех категорий
            $randomCategories = collect($categoryIds)->random(rand(1, min(3, count($categoryIds)))); // выбираем от 1 до 3
            $product->categories()->attach($randomCategories);
        });
        \App\Models\Brand::factory(10)->create();
        //                 \App\Models\User::factory(100)->create();
        //                \App\Models\Product::factory(10000)->create();
        //        $role = Role::create(['name' => 'manager']);
        //        $user = \App\Models\User::factory()->create([
        //            'name' => 'Igor',
        //            'email' => 'acheryc@gmail.com',
        //        ]);
        //        $user->roles()->attach([$role->id]);
    }
}
