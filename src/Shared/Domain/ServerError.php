<?php

namespace App\Shared\Domain;

class ServerError extends \Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly array $errorData,
    ) {
        parent::__construct($message);
    }
}
