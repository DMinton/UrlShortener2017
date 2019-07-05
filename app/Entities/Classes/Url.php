<?php namespace App\Entities\Classes;

use App;
use App\Entities\Cache\Classes\CacheFactory;
use App\Entities\Cache\Classes\UrlCache;
use App\Entities\Models\ModelFactory;
use App\Entities\Models\UrlModel;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class Url
{

    CONST CACHE_KEY = "Class:Url";
    CONST TIME_FORMAT = 'M j, Y H:i';

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $shortenedUrl;

    /**
     * @var string
     */
    public $fullUrl;

    /**
     * @var string
     */
    public $hashUrl;

    /**
     * @var int
     */
    public $visits;

    /**
     * @var string
     */
    public $created_at;

    /**
     * @var string
     */
    public $updated_at;

    /**
     * @var ModelFactory
     */
    protected $modelFactory;

    /**
     * @var Client
     */
    protected $guzzleClient;

    /**
     * @var CacheFactory
     */
    protected $cacheFactory;

    /**
     * @param ModelFactory $ModelFactory
     * @param CacheFactory $cacheFactory
     * @param Client $client
     */
    public function __construct(ModelFactory $ModelFactory, CacheFactory $cacheFactory, Client $client)
    {
        $this->modelFactory = $ModelFactory;
        $this->cacheFactory = $cacheFactory;
        $this->guzzleClient = $client;
    }

    /**
     * Get a collection of th most visited sites
     *
     * @param int $count
     * @return Collection
     */
    public static function getMostVisits($count)
    {
        $urlCache = UrlCache::getInstance();
        $mostVisitedSites = $urlCache->getMostVisited();
        if (!is_null($mostVisitedSites)) {
            $mostVisitedSites = collect($mostVisitedSites);
        } else {
            $urlModelInstance = ModelFactory::newUrlStaticModel();
            $mostVisitedSites = $urlModelInstance::getMostVisits($count)->all();

            $urlCache->setMostVisited($mostVisitedSites);
        }

        return $mostVisitedSites;
    }

    /**
     * Attempt to load by the shortened url
     *
     * @return Boolean
     */
    public function loadByShortenedUrl()
    {
        if (!empty($this->getShortenedUrl())) {
            $urlCache = $this->cacheFactory->newUrlCache();
            $cachedUrl = $urlCache->getShortenedUrl($this->getShortenedUrl());
            if (!isset($cachedUrl)) {
                $urlObject = $this->getUrlModel()
                    ->findShortenedUrl($this->getShortenedUrl());

                // if we found the shortened url, set it
                if ($urlObject->isNotEmpty()) {
                    $this->setFromObject($urlObject->first());
                    $urlCache->setShortenedUrl($this->getShortenedUrl(), $urlObject->first());
                }
            } else {
                $this->setFromObject($cachedUrl);
            }
        }

        return $this->exists();
    }

    /**
     * @return string
     */
    public function getShortenedUrl()
    {
        return $this->shortenedUrl;
    }

    /**
     * @param string $shortenedUrl
     * @return $this
     */
    public function setShortenedUrl($shortenedUrl)
    {
        $this->shortenedUrl = $shortenedUrl;

        return $this;
    }

    /**
     * @return UrlModel
     */
    public function getUrlModel()
    {
        return $this->modelFactory->newUrlModel();
    }

    /**
     * Sets all of the class variables
     *
     * @param object $UrlModel
     * @return Url
     */
    public function setFromObject($UrlModel)
    {
        return $this->setId($UrlModel->id)
            ->setShortenedUrl($UrlModel->shortenedUrl)
            ->setFullUrl($UrlModel->fullUrl)
            ->setHashUrl($UrlModel->hashUrl)
            ->setVisits(isset($UrlModel->visits) ? $UrlModel->visits : 0)
            ->setCreatedAt($UrlModel->created_at)
            ->setUpdatedAt($UrlModel->updated_at);
    }

    /**
     * Determines if the url exists by if the id is set or not
     *
     * @return Boolean
     */
    public function exists()
    {
        return !empty($this->getId());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Attempt to load by the full url
     *
     * @return bool
     */
    public function loadByUrl()
    {
        $urlCache = $this->cacheFactory->newUrlCache();
        $hashedUrl = md5($this->getFullUrl());
        $cachedUrl = $urlCache->getUrlHash($hashedUrl);
        if (!isset($cachedUrl)) {
            $urlObject = $this->getUrlModel()
                ->findUrlHash($hashedUrl);

            // if we found the shortened url, set it
            if ($urlObject->isNotEmpty()) {
                $this->setFromObject($urlObject->first());
                $urlCache->setUrlHash($hashedUrl, $urlObject->first());
            }
        } else {
            $this->setFromObject($cachedUrl);
        }

        return $this->exists();
    }

    /**
     * @return string
     */
    public function getFullUrl()
    {
        return $this->fullUrl;
    }

    /**
     * @param string $fullUrl
     * @return $this
     */
    public function setFullUrl($fullUrl)
    {
        $this->fullUrl = $fullUrl;

        return $this;
    }

    /**
     * Attempt to create the shortened url
     *
     * @return bool
     */
    public function create()
    {
        if (empty($this->getId()) && !empty($this->getFullUrl())) {
            $urlObject = $this->createShortenedUrl($this->getFullUrl());
            $this->setFromObject($urlObject);

            $urlCache = $this->cacheFactory->newUrlCache();
            $urlCache->setUrlHash($this->getHashUrl(), $urlObject);
            $urlCache->setShortenedUrl($this->getShortenedUrl(), $urlObject);
        }

        return $this->exists();
    }

    /**
     * Create shortened url and return the model
     *
     * @param string $url
     * @return UrlModel
     */
    public function createShortenedUrl($url)
    {
        // loop until we create a shortened url that does not already exist
        do {
            $shortenedUrl = self::createString();
        } while ($this->shortenedUrlExists($shortenedUrl));

        return $this->getUrlModel()
            ->saveNewUrl(array(
                'fullUrl' => $url,
                'shortenedUrl' => $shortenedUrl
            ));
    }

    /**
     * Randomly generates a shortened url
     *
     * @param int $length
     * @return string
     */
    protected static function createString($length = 5)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Check if shortened url exists
     *
     * @param string $shortened
     * @return Boolean
     */
    public function shortenedUrlExists($shortened)
    {
        return $this->getUrlModel()
            ->findShortenedUrl($shortened)
            ->isNotEmpty();
    }

    /**
     * @return string
     */
    public function getHashUrl()
    {
        return $this->hashUrl;
    }

    /**
     * @param string $hashUrl
     * @return $this
     */
    public function setHashUrl($hashUrl)
    {
        $this->hashUrl = $hashUrl;

        return $this;
    }

    /**
     * Add one visit to the url
     *
     * @return void
     */
    public function addOneVisit()
    {
        $this->getUrlModel()
            ->addOneVisit($this->getId());

        $this->cacheFactory->newUrlCache()->resetMostVisited();
    }

    /**
     * Determine if the url seems to be valid. Only
     * checks if the site returns a 200 or not.
     *
     * @return bool
     */
    public function isValidUrl()
    {
        $urlCache = $this->cacheFactory->newUrlCache();
        $status = $urlCache->getUrlStatus($this->getShortenedUrl());
        if (is_null($status)) {
            try {
                $connectionParams = array('http_errors' => false, 'connect_timeout' => 3);
                $status = $this->getGuzzleClient()->request("GET", $this->getFullUrl(), $connectionParams)->getStatusCode();
            } catch (\Exception $exception) {
                $status = $exception->getCode() == 200 ? 200 : 500;
            } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
                $status = $exception->getCode() == 200 ? 200 : 500;
            }

            $urlCache->setUrlStatus($this->getShortenedUrl(), $status);
        }

        return $status == 200;
    }

    /**
     * Getter for Guzzle
     *
     * @return Client
     */
    public function getGuzzleClient()
    {
        return $this->guzzleClient;
    }

    /**
     * @return int
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * @param int $visits
     * @return $this
     */
    public function setVisits($visits)
    {
        $this->visits = $visits;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = date(self::TIME_FORMAT, strtotime($created_at));

        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param string $updated_at
     * @return $this
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = date(self::TIME_FORMAT, strtotime($updated_at));

        return $this;
    }

    /**
     * @param ModelFactory $modelFactory
     * @return $this
     */
    public function setUrlFactory(ModelFactory $modelFactory)
    {
        $this->modelFactory = $modelFactory;

        return $this;
    }
}
