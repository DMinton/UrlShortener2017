<?php namespace App\Entities\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UrlModel extends Model
{
    protected $table = 'url';

    /**
     * Locate the shortened url and return it
     *
     * @param Query $query
     * @param String $shortened
     * @return Collection
     */
    public function scopeFindShortenedUrl($query, $shortened) {
        return $query->where('shortenedUrl', $shortened)->get();
    }

    /**
     * This will locate a url by the hash.
     *
     * @param Query $query
     * @param String $url
     * @return Collection
     */
    public function scopeFindUrlHash($query, $url) {
        return $query->where('hashUrl', md5($url))->get();
    }

    /**
     * This will locate a url by the url.
     *
     * @param Query $query
     * @param String $url
     * @return Collection
     */
    public function scopeFindUrl($query, $url) {
        return $query->where('fullUrl', $url)->get();
    }

    /**
     * Return a collection of the most visited url.
     *
     * @param Integer $count
     * @return Collection
     */
    public static function getMostVisits($count) {
        return self::orderBy('visits', 'DESC')
            ->where('visits', '>', 0)
            ->limit($count)
            ->get();
    }

    /**
     * Add one visit to the rul id passed in.
     *
     * @param Integer $id
     * @return void
     */
    public function addOneVisit($id) {
        self::where('id', $id)
            ->increment('visits');
    }

    /**
     * Will create a new url from the array passed in.
     *
     * @param Array $urlData
     * @return Model
     */
    public function saveNewUrl(Array $urlData) {
        $this->shortenedUrl = $urlData['shortenedUrl'];
        $this->fullUrl = $urlData['fullUrl'];
        $this->hashUrl = md5($urlData['fullUrl']);
        
        $this->save();

        return $this;
    }
}
