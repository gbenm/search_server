<?php

namespace Tests\Search\Factories;

use Faker\Factory;

class SearchProviderResponse
{
    public static function create(int $resultsQuantity): array
    {
        $faker = Factory::create();

        $results = array_map(fn() => [
      'title' => $faker->sentence(),
      'answer_count' => $faker->numberBetween(0, 100),
      'owner' => [
        'display_name' => $faker->name(),
        'profile_image' => $faker->imageUrl(),
      ]
    ], range(1, $resultsQuantity));

        return [
        'items' => $results,
        ];
    }
}
