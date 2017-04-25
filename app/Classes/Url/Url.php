<?php

namespace App\Classes\Url;

use App\Classes\Url\Model\Url as UrlModel;

class Url {
    
    CONST TIME_FORMAT = 'M j, Y H:i';

    public $id;
    public $shortenedUrl;
    public $fullUrl;
    public $hashUrl;
    public $visits;
    public $created_at;
    public $updated_at;

    public static function init ()
    {
        return new self;
    }

    public function loadByShortenedUrl() {
        if (isset($this->shortenedUrl)) {
            $urlObject = UrlModel::init()->findShortenedUrl($this->shortenedUrl);
            if ($urlObject->isNotEmpty()) {
                $this->setFromModel($urlObject->first());
            }
        }

        return $this->exists();
    }

    public function loadByUrl() {
        if (isset($this->fullUrl)) {
            $urlObject = UrlModel::init()->findUrlHash($this->fullUrl);
            if ($urlObject->isNotEmpty()) {
                $this->setFromModel($urlObject->first());
            }
        }

        return $this->exists();
    }

    public function create() {
        if (!isset($this->id) && isset($this->fullUrl)) {
            $urlObject = $this->createShortenedUrl($this->fullUrl);
            $this->setFromModel($urlObject);
        }

        return $this->exists();
    }

    public function createShortenedUrl($url) {
        do {
            $shortenedUrl = self::createString();
        } while($this->shortenedUrlExists($shortenedUrl));

        return UrlModel::init()->saveNewUrl(array(
            'fullUrl' => $url,
            'shortenedUrl' => $shortenedUrl
        ));
    }

    public function shortenedUrlExists($shortened) {
        return UrlModel::init()->findShortenedUrl($shortened)->isNotEmpty();
    }

    public function addOneVisit() {
        UrlModel::init()->addOneVisit($this->id);
    }

    public function isValidUrl() {
        $ch = curl_init($this->fullUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return 200 == $retcode;
    }

    public static function getMostVisits($count) {
        return UrlModel::getMostVisits($count)->all();
    }

    protected static function createString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function exists() {
        return isset($this->id);
    }

    public function setFromModel(UrlModel $UrlModel) {
        return $this->setId($UrlModel->id)
            ->setShortenedUrl($UrlModel->shortenedUrl)
            ->setFullUrl($UrlModel->fullUrl)
            ->setHashUrl($UrlModel->hashUrl)
            ->setVisits($UrlModel->visits)
            ->setCreatedAt($UrlModel->created_at)
            ->setUpdatedAt($UrlModel->updated_at);
    }

    /**
     * GETTERS
     */

    public function getId() {
        return $this->id;
    }

    public function getShortenedUrl() {
        return $this->shortenedUrl;
    }

    public function getFullUrl() {
        return $this->fullUrl;
    }

    public function getHashUrl() {
        return $this->hashUrl;
    }

    public function getVisits() {
        return $this->visits;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    /**
     * SETTERS
     */ 

    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    public function setShortenedUrl($shortenedUrl) {
        $this->shortenedUrl = $shortenedUrl;

        return $this;
    }

    public function setFullUrl($fullUrl) {
        $this->fullUrl = $fullUrl;

        return $this;
    }

    public function setHashUrl($hashUrl) {
        $this->hashUrl = $hashUrl;

        return $this;
    }

    public function setVisits($visits) {
        $this->visits = $visits;

        return $this;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = date(self::TIME_FORMAT, strtotime($created_at));

        return $this;
    }

    public function setUpdatedAt($updated_at) {
        $this->updated_at = date(self::TIME_FORMAT, strtotime($updated_at));

        return $this;
    }
}
