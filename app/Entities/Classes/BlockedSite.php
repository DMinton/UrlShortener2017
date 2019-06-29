<?php

namespace App\Entities\Classes;

use App\Entities\Models\ModelFactory;

class BlockedSite
{
    /**
     * @var ModelFactory
     */
    protected $modelFactory;

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
        return $this->modelFactory->newBlockedSite()::isBlockedSite($url);
    }
}