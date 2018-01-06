<?php namespace App\Classes\Url;

use App\Classes\Url\Url;
use App;

class UrlFactory {

    /**
     * @return Url
     */
    public function newInstance() {
        return App::make(Url::class);
    }
}
