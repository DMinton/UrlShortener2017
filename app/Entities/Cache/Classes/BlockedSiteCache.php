<?php

namespace App\Entities\Cache\Classes;

use App\Entities\Cache\Cache;
use Illuminate\Database\Eloquent\Collection;

class BlockedSiteCache
{
    const CACHE_KEY = __CLASS__ . ":";

    /**
     * @return mixed
     */
    public function getBlockedSites()
    {
        return Cache::get($this->getAlLKey());
    }

    /**
     * @return string
     */
    private function getAlLKey()
    {
        return self::CACHE_KEY . "All";
    }

    /**
     * @param Collection $sites
     * @return bool
     */
    public function setBlockedSites(Collection $sites)
    {
        return Cache::set($this->getAlLKey(), $sites);
    }
}