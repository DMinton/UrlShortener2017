<?php namespace App\Entities\Classes;

use App\Entities\Models\ModelFactory;
use App\Entities\Models\UrlModel;
use GuzzleHttp\Client;
use App;

class Url {

    CONST TIME_FORMAT = 'M j, Y H:i';

    /**
     * @var Integer
     */
    public $id;

    /**
     * @var String
     */
    public $shortenedUrl;

    /**
     * @var String
     */
    public $fullUrl;

    /**
     * @var String
     */
    public $hashUrl;

    /**
     * @var Integer
     */
    public $visits;

    /**
     * @var String
     */
    public $created_at;

    /**
     * @var String
     */
    public $updated_at;

    /**
     * @var ModelFactory
     */
    private $modelFactory;

    /**
     * @var Guzzle
     */
    private $guzzleClient;

    /**
     * @param ModelFactory $ModelFactory
     */
    public function __construct(ModelFactory $ModelFactory, Client $client) {
        $this->modelFactory = $ModelFactory;
        $this->guzzleClient = $client;
    }

    /**
     * Attempt to load by the shortened url
     *
     * @return Boolean
     */
    public function loadByShortenedUrl() {
        if (!empty($this->getShortenedUrl())) {
            $urlObject = $this->getUrlModel()
                ->findShortenedUrl($this->getShortenedUrl());

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
        if (!empty($this->getFullUrl())) {
            $urlObject = $this->getUrlModel()
                ->findUrlHash($this->getFullUrl());

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
        if (empty($this->getId()) && !empty($this->getFullUrl())) {
            $urlObject = $this->createShortenedUrl($this->getFullUrl());
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

        return $this->getUrlModel()
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
        return $this->getUrlModel()
            ->findShortenedUrl($shortened)
            ->isNotEmpty();
    }

    /**
     * Add one visit to the url
     *
     * @return void
     */
    public function addOneVisit() {
        $this->getUrlModel()
            ->addOneVisit($this->getId());
    }

    /**
     * Determine if the url seems to be valid. Only
     * checks if the site returns a 200 or not.
     *
     * @return boolean
     */
    public function isValidUrl() {
        return $this->getGuzzleClient()->request("GET", $this->getFullUrl(), ['http_errors' => false])->getStatusCode() == 200;
    }

    /**
     * Getter for Guzzle
     *
     * @return Guzzle
     */
    public function getGuzzleClient()
    {
        return $this->guzzleClient;
    }

    /**
     * Get a collection of th most visited sites
     *
     * @param Integer $count
     * @return Collection
     */
    public static function getMostVisits($count) {
        return ModelFactory::newUrlStaticInstance()::getMostVisits($count)->all();
    }

    /**
     * Randomly generates a shortened url
     *
     * @param Integer $length
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
        return !empty($this->getId());
    }

    /**
     * Sets all of the class variables
     *
     * @param UrlModel $UrlModel
     * @return Url
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
     * @return UrlModel
     */
    public function getUrlModel()
    {
        return $this->modelFactory->newUrlInstance();
    }

    /**
    * @return Integer
    */
    public function getId() {
        return $this->id;
    }

    /**
     * @return String
     */
    public function getShortenedUrl() {
        return $this->shortenedUrl;
    }

    /**
     * @return String
     */
    public function getFullUrl() {
        return $this->fullUrl;
    }

    /**
     * @return String
     */
    public function getHashUrl() {
        return $this->hashUrl;
    }

    /**
     * @return Integer
     */
    public function getVisits() {
        return $this->visits;
    }

    /**
     * @return String
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * @return String
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    /**
     * @param ModelFactory $modelFactory
     * @return this
     */
    public function setUrlFactory(ModelFactory $ModelFactory)
    {
        $this->modelFactory = $ModelFactory;

        return $this;
    }

     /**
      * @param Integer $id
      * @return this
      */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * @param String $shortenedUrl
     * @return this
     */
    public function setShortenedUrl($shortenedUrl) {
        $this->shortenedUrl = $shortenedUrl;

        return $this;
    }

    /**
     * @param String $fullUrl
     * @return this
     */
    public function setFullUrl($fullUrl) {
        $this->fullUrl = $fullUrl;

        return $this;
    }

    /**
     * @param String $hashUrl
     * @return this
     */
    public function setHashUrl($hashUrl) {
        $this->hashUrl = $hashUrl;

        return $this;
    }

    /**
     * @param Integer $visits
     * @return this
     */
    public function setVisits($visits) {
        $this->visits = $visits;

        return $this;
    }

    /**
     * @param String $created_at
     * @return this
     */
    public function setCreatedAt($created_at) {
        $this->created_at = date(self::TIME_FORMAT, strtotime($created_at));

        return $this;
    }

    /**
     * @param String $updated_at
     * @return this
     */
    public function setUpdatedAt($updated_at) {
        $this->updated_at = date(self::TIME_FORMAT, strtotime($updated_at));

        return $this;
    }
}
