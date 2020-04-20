<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use TestApp\Post;

$factory
    ->define(Post::class, function (Faker $faker) {
        return [
            'body' => $faker->text,
            'author_id' => 999,
        ];
    });
