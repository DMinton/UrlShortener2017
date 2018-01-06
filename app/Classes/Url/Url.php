<?php namespace App\Classes\Url;

use App\Classes\Url\Model\UrlModelFactory;
use App\Classes\Url\Model\UrlModel;
use App\Classes\Url\Url;
use App;

class Url {

    CONST TIME_FORMAT = 'M j, Y H:i';

    public $id;
    public $shortenedUrl;
    public $fullUrl;
    public $hashUrl;
    public $visits;
    public $created_at;
    public $updated_at;

    private $urlModelFactory;

    public function __construct(UrlModelFactory $UrlModelFactory) {
        $this->urlModelFactory = $UrlModelFactory;
    }

    /**
     * Attempt to load by the shortened url
     *
     * @return Boolean
     */
    public function loadByShortenedUrl() {
        if (isset($this->shortenedUrl)) {
            $urlObject = $this->urlModelFactory
                ->newInstance()
                ->findShortenedUrl($this->shortenedUrl);

            // if we found the shortened url, set it
            if ($urlObject->isNotEmpty()) {
                $this->setFromModel($urlObject->first());
            }
        }

        return $this->exists();
    }

    /**
     * Attempt to load by the full url
     *
     * @return Boolean
     */
    public function loadByUrl() {
        if (isset($this->fullUrl)) {
            $urlObject = $this->urlModelFactory
                ->newInstance()
                ->findUrlHash($this->fullUrl);

            // if we found the full url, set it
            if ($urlObject->isNotEmpty()) {
                $this->setFromModel($urlObject->first());
            }
        }

        return $this->exists();
    }

    /**
     * Attempt to create the shortened url
     *
     * @return Boolean
     */
    public function create() {
        if (!isset($this->id) && isset($this->fullUrl)) {
            $urlObject = $this->createShortenedUrl($this->fullUrl);
            $this->setFromModel($urlObject);
        }

        return $this->exists();
    }

    /**
     * Create shortened url and return the model
     *
     * @param String $url
     * @return Model
     */
    public function createShortenedUrl($url) {
        // loop until we create a shortened url that does not already exist
        do {
            $shortenedUrl = self::createString();
        } while($this->shortenedUrlExists($shortenedUrl));

        return $this->urlModelFactory
            ->newInstance()
            ->saveNewUrl(array(
                'fullUrl' => $url,
                'shortenedUrl' => $shortenedUrl
        ));
    }

    /**
     * Check if shortened url exists
     *
     * @param String $shortened
     * @return Boolean
     */
    public function shortenedUrlExists($shortened) {
        return $this->urlModelFactory
            ->newInstance()
            ->findShortenedUrl($shortened)
            ->isNotEmpty();
    }

    /**
     * Add one visit to the url
     *
     * @return void
     */
    public function addOneVisit() {
        $this->urlModelFactory
            ->newInstance()
            ->addOneVisit($this->id);
    }

    /**
     * Determine if the url seems to be valid. Only
     * checks if the site returns a 200 or not.
     *
     * @return boolean
     */
    public function isValidUrl() {
        $ch = curl_init($this->fullUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return 200 == $retcode;
    }

    /**
     * Get a collection of th most visited sites
     *
     * @param Integer $count
     * @return Collection
     */
    public static function getMostVisits($count) {
        return UrlModelFactory::newStaticInstance()::getMostVisits($count)->all();
    }

    /**
     * Randomly generates a shortened url
     *
     * @param int $length
     * @return String
     */
    protected static function createString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Determines if the url exists by if the id is set or not
     *
     * @return Boolean
     */
    public function exists() {
        return isset($this->id);
    }

    /**
     * Sets all of the class variables
     *
     * @param UrlModel $UrlModel
     * @return this
     */
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
