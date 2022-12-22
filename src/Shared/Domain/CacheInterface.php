<?php
namespace App\Shared\Domain;

interface CacheInterface {
  public function get(string $key, mixed $default = null): mixed;
  public function set(string $key, mixed $value, ?int $ttl = null): void;
  public function has(string $key): bool;
}
