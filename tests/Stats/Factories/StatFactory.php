<?php

namespace Tests\Stats\Factories;

use App\Stats\Domain\Models\Stat;
use Faker\Factory;

class StatFactory
{
    public static function createBatch(int $size): array
    {
        return array_map(fn() => self::create(), range(1, $size));
    }

    public static function create(
        ?string $query = null,
        ?int $searches = null,
    ): Stat {
        $faker = Factory::create();
        return new Stat(
            query: $query ?? $faker->unique()->text(),
            searches: $searches ?? $faker->numberBetween(0, 100)
        );
    }
}
