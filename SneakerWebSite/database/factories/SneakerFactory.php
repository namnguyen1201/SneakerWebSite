<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Sneaker::class, function (Faker $faker) {
    return [
        'name' => $faker->text(5),
        'brand' => $faker->text(10),
        'price' => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 500),
        'cover_image' => $faker->text(5).'.png'
    ];
});
