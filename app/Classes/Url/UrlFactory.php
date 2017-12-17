<?php

namespace App\Classes\Url;

use App\Classes\Url\Url as Url;

class UrlFactory {

    /**
     * @return Url
     */
    public function newInstance() {
        return App::make(Url::class);
    }
}
