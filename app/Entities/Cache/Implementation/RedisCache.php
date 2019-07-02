<?php

namespace App\Entities\Cache\Implementation;

use App\Entities\Cache\CacheInterface;
use Illuminate\Support\Facades\Redis;

class RedisCache implements CacheInterface
{
    CONST CACHE_TTL = 24 * 60 * 60;

    /**
     * @var Redis
     */
    protected $redis;

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->getCache()->get($key);
    }

    /**
     * @return mixed
     */
    protected function getCache()
    {
        if (!isset($this->redis)) {
            $this->redis = Redis::connection(env("REDIS_NAME"));
        }

        return $this->redis;
    }

    /**
     * @param string $key
     * @param string $value
     * @param int $ttl
     * @return bool
     */
    public function set($key, $value, $ttl)
    {
        return $this->getCache()->set($key, $value, "EX", $ttl >= 0 ? $ttl : self::CACHE_TTL);
    }
}