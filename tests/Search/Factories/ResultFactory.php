<?php

namespace Tests\Search\Factories;

use App\Search\Domain\Models\Result;
use Faker\Factory;

class ResultFactory
{
    public static function createBatch(int $size): array
    {
        return array_map(fn() => self::create(), range(1, $size));
    }

    public static function create(
        ?string $title = null,
        ?int $answerCount = null,
        ?string $username = null,
        ?string $profilePictureUrl = null,
    ): Result {
        $faker = Factory::create();

        return new Result(
            title: $title ?: $faker->sentence(),
            answer_count: $answerCount ?: $faker->numberBetween(0, 100),
            username: $username ?: $faker->userName(),
            profile_picture_url: $profilePictureUrl ?: $faker->imageUrl(),
        );
    }
}
