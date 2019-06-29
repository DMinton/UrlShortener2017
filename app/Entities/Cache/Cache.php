<?php

namespace App\Entities\Cache;

use App;
use App\Entities\Cache\Implementation\RedisCache;

class Cache
{
    /**
     * @var CacheInterface
     */
    protected static $cache;

    /**
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        return json_decode(self::getCache()->get($key));
    }

    /**
     * @return mixed
     */
    protected static function getCache()
    {
        if (!isset(static::$cache)) {
            static::$cache = App::make(RedisCache::class);
        }

        return static::$cache;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set($key, $value)
    {
        return self::getCache()->set($key, json_encode($value));
    }
}