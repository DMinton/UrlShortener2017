<?php namespace App\Entities\Classes;

use App\Entities\Models\ModelFactory;
use App\Entities\Models\VisitorModel;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App;

class Visitor {

    CONST TIME_FORMAT = 'M j, Y H:i';

    /**
     * @var Integer
     */
    public $id;

    /**
     * @var String
     */
    public $ip;

    /**
     * @var String
     */
    public $country;

    /**
     * @var String
     */
    public $region;

    /**
     * @var String
     */
    public $city;

    /**
     * @var String
     */
    public $path;

    /**
     * @var String
     */
    public $ip_payload;

    /**
     * @var String
     */
    public $request_payload;

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
    public function __construct(ModelFactory $ModelFactory, Client $Client) {
        $this->modelFactory = $ModelFactory;
        $this->guzzleClient = $Client;
    }

    /**
     * @param Request $request
     * @return this
     */
    public function setRequestInformation(Request $request)
    {
        $this->setIp($this->findIp())
            ->setPath($request->path())
            ->setRequest_payload(json_encode($request->input()));

        return $this;
    }

    /**
     * @return String
     */
    private function findIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }

        return "";
    }

    /**
     * @return this
     */
    public function load()
    {
        if ($this->exists()) {
            $response = $this->getGuzzleClient()->request("GET", "http://ipinfo.io/{$this->getIp()}/json", ['http_errors' => false]);
        
            if ($response->getStatusCode() == 200) {
                $this->setFromJsonResponse(json_decode((string)$response->getBody()));
            }
        }

        return $this;
    }

    /**
     * @return this
     */
    public function save()
    {
        if ($this->exists()) {
            $this->getModelFactory()
                ->newVisitorModel()
                ->saveNewVisitor($this);
        }

        return $this;
    }

    /**
     * @return Boolean
     */
    public function exists()
    {
        return !empty($this->getIp());
    }

    /**
     * @return stdClass $jsonResponse
     */
    public function setFromJsonResponse($jsonResponse)
    {
        $this->setCountry(isset($jsonResponse->country) ? $jsonResponse->country : "")
            ->setRegion(isset($jsonResponse->region) ? $jsonResponse->region : "")
            ->setCity(isset($jsonResponse->city) ? $jsonResponse->city : "")
            ->setIp_payload(json_encode($jsonResponse));
    }

    /**
     * Get the value of id
     *
     * @return  Integer
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param  Integer  $id
     *
     * @return  this
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of ip
     *
     * @return  String
     */ 
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set the value of ip
     *
     * @param  String  $ip
     *
     * @return  this
     */ 
    public function setIp(String $ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get the value of country
     *
     * @return  String
     */ 
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of country
     *
     * @param  String  $country
     *
     * @return  this
     */ 
    public function setCountry(String $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of region
     *
     * @return  String
     */ 
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set the value of region
     *
     * @param  String  $region
     *
     * @return  this
     */ 
    public function setRegion(String $region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get the value of city
     *
     * @return  String
     */ 
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @param  String  $city
     *
     * @return  this
     */ 
    public function setCity(String $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get the value of path
     *
     * @return  String
     */ 
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @param  String  $path
     *
     * @return  this
     */ 
    public function setPath(String $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of ip_payload
     *
     * @return  String
     */ 
    public function getIp_payload()
    {
        return $this->ip_payload;
    }

    /**
     * Set the value of ip_payload
     *
     * @param  String  $ip_payload
     *
     * @return  this
     */ 
    public function setIp_payload(String $ip_payload)
    {
        $this->ip_payload = $ip_payload;

        return $this;
    }

    /**
     * Get the value of request_payload
     *
     * @return  String
     */ 
    public function getRequest_payload()
    {
        return $this->request_payload;
    }

    /**
     * Set the value of request_payload
     *
     * @param  String  $request_payload
     *
     * @return  self
     */ 
    public function setRequest_payload(String $request_payload)
    {
        $this->request_payload = $request_payload;

        return $this;
    }

    /**
     * Get the value of created_at
     *
     * @return  String
     */ 
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @param  String  $created_at
     *
     * @return  this
     */ 
    public function setCreated_at(String $created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of updated_at
     *
     * @return  String
     */ 
    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    /**
     * Set the value of updated_at
     *
     * @param  String  $updated_at
     *
     * @return  this
     */ 
    public function setUpdated_at(String $updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Get the value of modelFactory
     *
     * @return  ModelFactory
     */ 
    public function getModelFactory()
    {
        return $this->modelFactory;
    }

    /**
     * Set the value of modelFactory
     *
     * @param  ModelFactory  $modelFactory
     *
     * @return  this
     */ 
    public function setModelFactory(ModelFactory $modelFactory)
    {
        $this->modelFactory = $modelFactory;

        return $this;
    }

    /**
     * Get the value of guzzleClient
     *
     * @return  Guzzle
     */ 
    public function getGuzzleClient()
    {
        return $this->guzzleClient;
    }

    /**
     * Set the value of guzzleClient
     *
     * @param  Guzzle  $guzzleClient
     *
     * @return  this
     */ 
    public function setGuzzleClient(Guzzle $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;

        return $this;
    }
}
