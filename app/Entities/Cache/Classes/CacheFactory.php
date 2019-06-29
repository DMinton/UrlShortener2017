<?php

namespace App\Entities\Cache\Classes;

use App;

class CacheFactory
{
    /**
     * @return BlockedSiteCache
     */
    public function newBlockedSiteCache()
    {
        return App::make(BlockedSiteCache::class);
    }

    /**
     * @return UrlCache
     */
    public function newUrlCache()
    {
        return App::make(UrlCache::class);
    }
}