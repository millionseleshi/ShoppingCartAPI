<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cart;
use App\Product;
use App\User;
use Faker\Generator as Faker;

$factory->define(Cart::class, function (Faker $faker) {
    $userID = User::all()->pluck('id');
    $productID = Product::all()->pluck('id');
    return [
        'user_id' => $faker->randomElement($userID),
        'product_id' => $faker->randomElement($productID)
    ];
});
