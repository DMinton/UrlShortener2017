<?php

namespace App\Entities\Cache\Classes;

use App\Entities\Cache\Cache;

class UrlCache
{

    const CACHE_MOST_VISITED_TTL = 14 * 24 * 60 * 60;
    const CACHE_KEY = __CLASS__ . ":";

    /**
     * @return UrlCache
     */
    public static function getInstance()
    {
        return new UrlCache();
    }

    /**
     * @return mixed
     */
    public function getMostVisited()
    {
        return Cache::get($this->getMostVisitedKey());
    }

    /**
     * @return string
     */
    private function getMostVisitedKey()
    {
        return self::CACHE_KEY . "MostVisited";
    }

    /**
     * @param $mostVisited
     * @return bool
     */
    public function setMostVisited($mostVisited)
    {
        return Cache::set($this->getMostVisitedKey(), $mostVisited, self::CACHE_MOST_VISITED_TTL);
    }

    /**
     * @return bool
     */
    public function resetMostVisited()
    {
        return Cache::set($this->getMostVisitedKey(), null);
    }

    /**
     * @param string $shortenedUrl
     * @return mixed
     */
    public function getShortenedUrl($shortenedUrl)
    {
        return Cache::get($this->getShortenedUrlKey($shortenedUrl));
    }

    /**
     * @param string $shortenedUrl
     * @return string
     */
    private function getShortenedUrlKey($shortenedUrl)
    {
        return self::CACHE_KEY . "ShortenedUrl:{$shortenedUrl}";
    }

    /**
     * @param string $shortenedUrl
     * @param mixed $shortenedUrlData
     * @return mixed
     */
    public function setShortenedUrl($shortenedUrl, $shortenedUrlData)
    {
        return Cache::set($this->getShortenedUrlKey($shortenedUrl), $shortenedUrlData);
    }

    /**
     * @param string $urlHash
     * @return mixed
     */
    public function getUrlHash($urlHash)
    {
        return Cache::get($this->getUrlHashKey($urlHash));
    }

    /**
     * @param string $urlHash
     * @return string
     */
    private function getUrlHashKey($urlHash)
    {
        return self::CACHE_KEY . "UrlHash:{$urlHash}";
    }

    /**
     * @param string $urlHash
     * @param mixed $urlHashData
     * @return mixed
     */
    public function setUrlHash($urlHash, $urlHashData)
    {
        return Cache::set($this->getUrlHashKey($urlHash), $urlHashData);
    }

    /**
     * @param string $shortenedUrl
     * @return mixed
     */
    public function getUrlStatus($shortenedUrl)
    {
        return Cache::get($this->getUrlStatusKey($shortenedUrl));
    }

    /**
     * @param string $shortenedUrl
     * @return string
     */
    private function getUrlStatusKey($shortenedUrl)
    {
        return self::CACHE_KEY . "UrlStatus:{$shortenedUrl}";
    }

    /**
     * @param string $shortenedUrl
     * @param mixed $shortenedUrlStatus
     * @return mixed
     */
    public function setUrlStatus($shortenedUrl, $shortenedUrlStatus)
    {
        return Cache::set($this->getUrlStatusKey($shortenedUrl), $shortenedUrlStatus);
    }
}