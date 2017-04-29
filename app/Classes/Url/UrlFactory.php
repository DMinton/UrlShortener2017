<?php

namespace App\Classes\Url;

use App\Classes\Url\Url as Url;

class UrlFactory {
    public function newInstance() {
        return Url::init();
    }
}
