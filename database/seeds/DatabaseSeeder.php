<?php

use App\Cart;
use App\Category;
use App\Product;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        factory(User::class, 10)->create();
        factory(Category::class, 10)->create();
        factory(Product::class, 10)->create();
        factory(Cart::class, 10)->create();
    }
}
