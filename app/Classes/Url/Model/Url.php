<?php

namespace App\Classes\Url\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Url extends Model
{
    protected $table = 'url';

    private static $_instance = null;

    public static function newUrl ()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function scopefindShortenedUrl($query, $shortened) {
        return $query->where('shortenedUrl', $shortened);
    }

    public function scopefindUrlHash($query, $url) {
        return $query->where('hashUrl', md5($url));
    }

    public function scopefindUrl($query, $url) {
        return $query->where('fullUrl', $url);
    }

    public function scopegetMostVisits($query, $count) {
        return $query->orderBy('visits', 'DESC')
            ->where('visits', '>', 0)
            ->limit($count);
    }

    public function addOneVisit() {
        $this->visits++;
        $this->save();
    }

    public function saveNewUrl(Array $urlData) {
        $newUrl = self::newUrl();

        $newUrl->shortenedUrl = $urlData['shortenedUrl'];
        $newUrl->fullUrl = $urlData['fullUrl'];
        $newUrl->hashUrl = md5($urlData['fullUrl']);
        
        $newUrl->save();

        return $newUrl;
    }
}
