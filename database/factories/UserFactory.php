<?php

/** @var Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(User::class, static function (Faker $faker) {
    $username = $faker->userName;
    return [
        'discord_id' => $faker->numerify('##################'),
        'username' => $username,
        'tag' => $username . '#' . $faker->numerify('####'),
        'email' => $faker->email,
        'avatar' => $faker->imageUrl(150, 150),
        'is_banned' => 0,
        'is_discord_verified' => 1,
        'role_color' => $faker->hexColor,
        'accept_terms' => 1
    ];
});
