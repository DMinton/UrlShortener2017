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

        if ($urlObject->isEmpty()) {
            $urlObject = $this->createShortenedUrl($url);
        }

        return $urlObject->first();
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

    public function getMostVisits() {
        return $this->UrlModel->getMostVisits()->get();
    }

    protected function createString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
