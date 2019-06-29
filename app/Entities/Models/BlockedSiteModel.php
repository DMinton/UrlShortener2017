<?php

namespace App\Entities\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UrlModel
 * @package App\Entities\Models
 */
class BlockedSiteModel extends Model
{
    protected $table = 'blocked_site';

    /**
     * @param string $url
     * @return bool
     */
    public static function isBlockedSite($url)
    {
        $urlParts = parse_url($url);
        if (isset($urlParts['host'])) {
            return self::where("host", $urlParts['host'])->count() > 0;
        }

        return false;
    }

    /**
     * @return BlockedSiteModel[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getBlockedSites()
    {
        return self::all();
    }
}