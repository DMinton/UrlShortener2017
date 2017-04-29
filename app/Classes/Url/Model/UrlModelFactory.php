<?php

namespace App\Classes\Url\Model;

use App\Classes\Url\Model\UrlModel as UrlModel;

class UrlModelFactory {
    public function newInstance() {
        return UrlModel::init();
    }

    public static function newStaticInstance() {
        return UrlModel::init();
    }
}
