<?php namespace App\Entities\Models;

use App;

class ModelFactory {

    /**
     * @return UrlModel
     */
    public function newUrlInstance()
    {
        return App::make(UrlModel::class);
    }

    /**
     * @return UrlModel
     */
    public static function newUrlStaticInstance()
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
}
