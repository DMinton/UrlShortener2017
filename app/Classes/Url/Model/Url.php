<?php

namespace App\Classes\Url\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Url extends Model
{
    protected $table = 'url';

    public static function init ()
    {
        return new self;
    }

    public function scopefindShortenedUrl($query, $shortened) {
        return $query->where('shortenedUrl', $shortened)->get();
    }

    public function scopefindUrlHash($query, $url) {
        return $query->where('hashUrl', md5($url))->get();
    }

    public function scopefindUrl($query, $url) {
        return $query->where('fullUrl', $url)->get();
    }

    public static function getMostVisits($count) {
        return self::orderBy('visits', 'DESC')
            ->where('visits', '>', 0)
            ->limit($count)
            ->get();
    }

    public function addOneVisit($id) {
        self::where('id', $id)
            ->increment('visits');
    }

    public function saveNewUrl(Array $urlData) {
        $this->shortenedUrl = $urlData['shortenedUrl'];
        $this->fullUrl = $urlData['fullUrl'];
        $this->hashUrl = md5($urlData['fullUrl']);
        
        $this->save();

        return $this;
    }
}
