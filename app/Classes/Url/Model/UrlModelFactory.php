<?php namespace App\Classes\Url\Model;

use App\Classes\Url\Model\UrlModel;
use App;

class UrlModelFactory {

    /**
     * @return UrlModel
     */
    public function newInstance() {
        return App::make(UrlModel::class);
    }

    /**
     * @return UrlModel
     */
    public static function newStaticInstance() {
        return App::make(UrlModel::class);
    }
}
