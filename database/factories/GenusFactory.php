<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Genus;
use Faker\Generator as Faker;

$factory->define(Genus::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName
    ];
});
