<?php namespace App\Entities\Models;

use App;

class ModelFactory
{

    /**
     * @return UrlModel
     */
    public static function newUrlStaticModel()
    {
        return App::make(UrlModel::class);
    }

    /**
     * @return UrlModel
     */
    public function newUrlModel()
    {
        return App::make(UrlModel::class);
    }

    /**
     * @return VisitorModel
     */
    public function newVisitorModel()
    {
        return App::make(VisitorModel::class);
    }

    /**
     * @return BlockedSiteModel
     */
    public function newBlockedSiteModel()
    {
        return App::make(BlockedSiteModel::class);
    }
}
