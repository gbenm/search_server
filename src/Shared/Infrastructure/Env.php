<?php
namespace App\Shared\Infrastructure;

use DateTime;
use Exception;

class Env {
  public static function getCacheTTL(): int
  {
    return (int) $_ENV['CACHE_TTL'] ?: 900;
  }

  public static function getRedisHost(): string
  {
    return $_ENV['REDIS_HOST'] ?: '127.0.0.1';
  }

  public static function getRedisPort(): int
  {
    return (int) $_ENV['REDIS_PORT'] ?: 6379;
  }

  public static function getDefaultStartDateTime(): DateTime
  {
    return new DateTime('2022-12-22');
  }

  public static function getDbHost(): string
  {
    return $_ENV['DB_HOST'] ?: '127.0.0.1';
  }

  public static function getDbPort(): int
  {
    return (int) $_ENV['DB_PORT'] ?: 3306;
  }

  public static function getDbName(): string
  {
    return $_ENV['MYSQL_DATABASE'] ?: throw new Exception('MYSQL_DATABASE not set');
  }

  public static function getDbUser(): string
  {
    return $_ENV['MYSQL_USER'] ?: throw new Exception('MYSQL_USER not set');
  }

  public static function getDbPassword(): string
  {
    return $_ENV['MYSQL_PASSWORD'] ?: throw new Exception('MYSQL_PASSWORD not set');
  }
}
