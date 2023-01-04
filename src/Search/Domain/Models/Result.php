<?php
namespace App\Search\Domain\Models;

final class Result
{
  public function __construct(
    public readonly string $title,
    public readonly int $answer_count,
    public readonly ?string $username,
    public readonly string|null|array $profile_picture_url,
  ) {}

  public function toArray()
  {
    return [
      'title' => $this->title,
      'answer_count' => $this->answer_count,
      'username' => $this->username,
      'profile_picture_url' => $this->profile_picture_url,
    ];
  }
}
