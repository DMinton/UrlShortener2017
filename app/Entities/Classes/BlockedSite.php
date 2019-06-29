<?php

namespace App\Entities\Classes;

use App\Entities\Cache\Cache;
use App\Entities\Models\BlockedSiteModel;
use App\Entities\Models\ModelFactory;
use Illuminate\Support\Collection;

class BlockedSite
{
    const CACHE_KEY = "Model:BlockedSite:";

    /**
     * @var ModelFactory
     */
    protected $modelFactory;

    /**
     * @var Collection
     */
    protected $blockedSites;

    /**
     * BlockedSite constructor.
     * @param ModelFactory $modelFactory
     */
    public function __construct(ModelFactory $modelFactory)
    {
        $this->modelFactory = $modelFactory;
    }

    /**
     * @param string $url
     * @return bool
     */
    public function isBlockedSite($url)
    {
        $blockedSites = $this->getBlockedSites();

        $isBlocked = false;
        if ($blockedSites instanceof Collection) {
            $isBlocked = $blockedSites->contains("host", "=", parse_url($url, PHP_URL_HOST));
        }

        return $isBlocked;
    }

    /**
     * @return BlockedSiteModel[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getBlockedSites()
    {
        if (!isset($this->blockedSites)) {
            $sites = Cache::get(self::CACHE_KEY);
            if (!empty($sites)) {
                $sites = collect($sites);
            }

            if (!isset($sites)) {
                $blockedSite = $this->modelFactory->newBlockedSite();
                $sites = $blockedSite::getBlockedSites();

                Cache::set(self::CACHE_KEY, $sites);
            }

            $this->blockedSites = $sites;
        }

        return $this->blockedSites;
    }
}