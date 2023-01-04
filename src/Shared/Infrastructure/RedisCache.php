<?php

namespace App\Shared\Infrastructure;

use App\Shared\Domain\CacheInterface;
use Phpfastcache\CacheManager;
use Phpfastcache\Drivers\Predis\Config as RedisConfig;
use Phpfastcache\Helper\Psr16Adapter;

class RedisCache implements CacheInterface
{
    private $cache;

    public function __construct()
    {
        $config = new RedisConfig();
        $config->setHost(Env::getRedisHost());
        $config->setPort(Env::getRedisPort());

        $driver = CacheManager::getInstance('Predis', $config);
        $this->cache = new Psr16Adapter($driver);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->get($key, $default);
    }

    public function set(string $key, mixed $value, ?int $ttl = null): void
    {
        $this->cache->set($key, $value, $ttl);
    }

    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }
}
