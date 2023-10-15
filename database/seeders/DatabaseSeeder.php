<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\FeatureLang;
use App\Models\FeatureValueProduct;
use App\Models\Lang;
use Database\Factories\FeatureLangFactory;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        \App\Models\Lang::insert(
//        [
//            [
//            'name' => 'ukrainian',
//            'active' => 1,
//            'code' => 'uk',
//            'date_format' => 'Y-m-d',
//            'date_format_full' => 'Y-m-d H:i:s',
//            'created_at' => now(),
//            'updated_at' => now()
//            ],
//            [
//            'name' => 'english',
//            'active' => 1,
//            'code' => 'en',
//            'date_format' => 'Y-m-d',
//            'date_format_full' => 'Y-m-d H:i:s',
//            'created_at' => now(),
//            'updated_at' => now()
//            ]
//        ]
//        );
//        \App\Models\Feature::factory(30)->create()->each(function($feature){
//             $faker = \Faker\Factory::create('uk_UA');
//             $feature->translation()->create(
//                 [
//                 'locale' => 'uk',
//                 'name' => $faker->country,
//                ]
//             );
//             $faker = \Faker\Factory::create('en_US');
//             $feature->translation()->create(                 [
//                 'locale' => 'en',
//                 'name' => $faker->country,
//             ]);
//
//             $featureValue =  $feature->featureValue()->create(
//                 [
//                     'custom' => rand(0,1)
//                 ]
//             );
//             $faker = \Faker\Factory::create('uk_UA');
//             $featureValue->translation()->create(
//                 [
//                     'locale' => 'uk',
//                     'value' => $faker->city
//                 ]
//             );
//             $faker = \Faker\Factory::create('en_US');
//             $featureValue->translation()->create([
//                 'locale' => 'en',
//                 'value' => $faker->city,
//             ]);
//         });
//        \App\Models\Product::factory(10)->create()->each(function ($product) {
//            $productLang = \App\Models\ProductLang::factory(1)->make();
//            $product->translation()->saveMany($productLang);
//            $product->translation()->saveMany($productLang);
//            $featureValues = \App\Models\FeatureValue::all()->random(rand(1,5));
//            foreach($featureValues as $item){
//                FeatureValueProduct::create(
//                    [
//                        'product_id' => $product->id,
//                        'feature_value_id' => $item->id,
//                        'feature_id' => $item->feature_id,
//                    ]
//                );
//            }
//
//
//        });
//        \App\Models\Brand::factory(10)->create();
//         \App\Models\User::factory(100)->create();
//        \App\Models\Product::factory(10000)->create();
         $role = Role::create(['name' => 'manager']);
         $user =  \App\Models\User::factory()->create([
             'name' => 'Igor',
             'email' => 'acheryc@gmail.com',
        ]);
        $user->roles()->attach([$role->id]);
    }
}
