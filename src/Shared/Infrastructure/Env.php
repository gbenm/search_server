<?php

namespace App\Shared\Infrastructure;

use DateTime;
use Exception;

class Env
{
    private static function getOrElse(string $key, mixed $default = null): mixed
    {
        if (!isset($_ENV[$key])) {
            return $default;
        }

        return $_ENV[$key];
    }

    public static function getSearchProviderUrl(): string
    {
        $url = self::getOrElse('SEARCH_PROVIDER_URL', 'https://api.stackexchange.com/2.3');
        return rtrim($url, '/');
    }

    public static function getCacheTTL(): int
    {
        return (int) self::getOrElse('CACHE_TTL', 900);
    }

    public static function getRedisHost(): string
    {
        return self::getOrElse('REDIS_HOST', '127.0.0.1');
    }

    public static function getRedisPort(): int
    {
        return (int) self::getOrElse('REDIS_PORT', 6379);
    }

    public static function getDefaultStartDateTime(): DateTime
    {
        return new DateTime('2022-12-22');
    }

    public static function getDbHost(): string
    {
        return self::getOrElse('DB_HOST', '127.0.0.1');
    }

    public static function getDbPort(): int
    {
        return (int) self::getOrElse('DB_PORT', 3306);
    }

    public static function getDbName(): string
    {
        return self::getOrElse('MYSQL_DATABASE') ?: throw new Exception('MYSQL_DATABASE not set');
    }

    public static function getDbUser(): string
    {
        return self::getOrElse('MYSQL_USER') ?: throw new Exception('MYSQL_USER not set');
    }

    public static function getDbPassword(): string
    {
        return self::getOrElse('MYSQL_PASSWORD') ?: throw new Exception('MYSQL_PASSWORD not set');
    }
}
