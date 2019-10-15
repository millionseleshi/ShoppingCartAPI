<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    $categoryID = Category::all()->pluck('id');
    return [
        'productName' => $faker->domainWord,
        'productDescription' => $faker->realText(50, 2),
        'price' => random_int(1, 100000),
        'category_id' => $faker->randomElement($categoryID)
    ];
});
