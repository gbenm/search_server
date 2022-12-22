<?php
namespace App\Shared\Domain;

interface CacheInterface {
  public function get(string $key, mixed $default = null);
  public function set(string $key, mixed $value, ?int $ttl = null);
  public function has(string $key);
}
