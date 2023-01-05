<?php

namespace App\Search\Domain\Models;

final class Result
{
    public function __construct(
        public readonly string $title,
        public readonly int $answerCount,
        public readonly ?string $username,
        public readonly string|null|array $profilePictureUrl,
    ) {
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'answer_count' => $this->answerCount,
            'username' => $this->username,
            'profile_picture_url' => $this->profilePictureUrl,
        ];
    }
}
