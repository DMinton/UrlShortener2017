<?php

namespace App\Entities\Classes;

use App\Entities\Cache\Classes\CacheFactory;
use App\Entities\Models\BlockedSiteModel;
use App\Entities\Models\ModelFactory;
use Illuminate\Support\Collection;

class BlockedSite
{

    /**
     * @var ModelFactory
     */
    protected $modelFactory;

    /**
     * @var CacheFactory
     */
    protected $cacheFactory;

    /**
     * @var Collection
     */
    protected $blockedSites;

    /**
     * BlockedSite constructor.
     * @param ModelFactory $modelFactory
     * @param CacheFactory $cacheFactory
     */
    public function __construct(ModelFactory $modelFactory, CacheFactory $cacheFactory)
    {
        $this->modelFactory = $modelFactory;
        $this->cacheFactory = $cacheFactory;
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
            $blockedSiteCache = $this->cacheFactory->newBlockedSiteCache();
            $sites = $blockedSiteCache->getBlockedSites();
            if (!empty($sites)) {
                $sites = collect($sites);
            }

            if (!isset($sites)) {
                $blockedSite = $this->modelFactory->newBlockedSiteModel();
                $sites = $blockedSite::getBlockedSites();

                $blockedSiteCache->setBlockedSites($sites);
            }

            $this->blockedSites = $sites;
        }

        return $this->blockedSites;
    }
}