<?php namespace App\Entities\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class UrlModel
 * @package App\Entities\Models
 */
class UrlModel extends Model
{
    protected $table = 'url';

    /**
     * Return a collection of the most visited url.
     *
     * @param int $count
     * @return Collection
     */
    public static function getMostVisits($count)
    {
        return self::orderBy('visits', 'DESC')
            ->where('visits', '>', 0)
            ->limit($count)
            ->get();
    }

    /**
     * Locate the shortened url and return it
     *
     * @param Query $query
     * @param string $shortened
     * @return Collection
     */
    public function scopeFindShortenedUrl($query, $shortened)
    {
        return $query->where('shortenedUrl', $shortened)->get();
    }

    /**
     * This will locate a url by the hash.
     *
     * @param Query $query
     * @param string $hashUrl
     * @return Collection
     */
    public function scopeFindUrlHash($query, $hashUrl)
    {
        return $query->where('hashUrl', $hashUrl)->get();
    }

    /**
     * This will locate a url by the url.
     *
     * @param Query $query
     * @param string $url
     * @return Collection
     */
    public function scopeFindUrl($query, $url)
    {
        return $query->where('fullUrl', $url)->get();
    }

    /**
     * Add one visit to the rul id passed in.
     *
     * @param int $id
     * @return void
     */
    public function addOneVisit($id)
    {
        self::where('id', $id)
            ->increment('visits');
    }

    /**
     * Will create a new url from the array passed in.
     *
     * @param array $urlData
     * @return UrlModel
     */
    public function saveNewUrl(array $urlData)
    {
        $this->shortenedUrl = $urlData['shortenedUrl'];
        $this->fullUrl = $urlData['fullUrl'];
        $this->hashUrl = md5($urlData['fullUrl']);

        $this->save();

        return $this;
    }
}
