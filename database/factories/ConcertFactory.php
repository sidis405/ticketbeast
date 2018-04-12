<?php

use Faker\Generator as Faker;

$factory->define(App\Concert::class, function (Faker $faker) {
    return [
        'title' => 'Example Band',
        'subtitle' => 'with some openers',
        'date' => \Carbon\Carbon::parse('+2 weeks'),
        'ticket_price' => 2000,
        'venue' => 'The Example Theatre',
        'venue_address' => 'Viale dei Gladiatori',
        'state' => 'RM',
        'city' => 'Roma',
        'zip' => '00185',
        'additional_information' => 'Some Sample additional data.',
    ];
});

$factory->state(App\Concert::class, 'published', function (Faker $faker) {
    return [
        'published_at' => \Carbon\Carbon::parse('-2 weeks'),
    ];
});


$factory->state(App\Concert::class, 'unpublished', function (Faker $faker) {
    return [
        'published_at' => null,
    ];
});
