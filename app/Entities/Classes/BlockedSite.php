<?php

namespace App\Entities\Classes;

use App\Entities\Models\ModelFactory;
use Illuminate\Support\Collection;

class BlockedSite
{
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
     * @return \App\Entities\Models\BlockedSiteModel[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getBlockedSites()
    {
        if (!isset($this->blockedSites)) {
            $blockedSite = $this->modelFactory->newBlockedSite();
            $this->blockedSites = $blockedSite::getBlockedSites();
        }

        return $this->blockedSites;
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
}