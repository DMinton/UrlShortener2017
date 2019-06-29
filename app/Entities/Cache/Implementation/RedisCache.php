<?php

namespace App\Entities\Cache\Implementation;

use App\Entities\Cache\CacheInterface;
use Illuminate\Support\Facades\Redis;

class RedisCache implements CacheInterface
{
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
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value)
    {
        return $this->getCache()->set($key, $value);
    }
}