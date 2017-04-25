<?php

namespace App\Classes\Url;

use App\Classes\Url\Model\Url as UrlModel;

class Url {
    
    protected $UrlModel;


    public function __construct(UrlModel $UrlModel) {
        $this->UrlModel = $UrlModel;
    }

    public function findOrCreateShortenedUrl($url) {
        $urlObject = $this->findUrl($url);

        if ($urlObject->isNotEmpty()) {
            return $urlObject->first();
        }

        return $this->createShortenedUrl($url);;
    }

    public function findShortenedUrl($shortened) {
        return $this->UrlModel->findShortenedUrl($shortened)->get();
    }

    public function findUrl($url) {
        return $this->UrlModel->findUrlHash($url)->get();
    }

    public function shortenedUrlExists($shortened) {
        return $this->findShortenedUrl($shortened)->isNotEmpty();
    }

    public function createShortenedUrl($url) {
        do {
            $shortenedUrl = $this->createString();
        } while($this->shortenedUrlExists($shortenedUrl));

        return $this->UrlModel->saveNewUrl(array(
            'fullUrl' => $url,
            'shortenedUrl' => $shortenedUrl
        ));
    }

    public static function isValidUrl($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return 200 == $retcode;
    }

    public function getMostVisits($count) {
        return $this->UrlModel->getMostVisits($count)->get()->all();
    }

    protected function createString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
