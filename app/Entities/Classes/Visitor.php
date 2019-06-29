<?php namespace App\Entities\Classes;

use App;
use App\Entities\Models\ModelFactory;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class Visitor
{

    CONST TIME_FORMAT = 'M j, Y H:i';

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $ip;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $region;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $ip_payload;

    /**
     * @var string
     */
    public $request_payload;

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
    private $modelFactory;

    /**
     * @var Client
     */
    private $guzzleClient;

    /**
     * Visitor constructor.
     * @param ModelFactory $ModelFactory
     * @param Client $Client
     */
    public function __construct(ModelFactory $ModelFactory, Client $Client)
    {
        $this->modelFactory = $ModelFactory;
        $this->guzzleClient = $Client;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequestInformation(Request $request)
    {
        $this->setIp($this->findIp())
            ->setPath($request->path())
            ->setRequest_payload(json_encode($request->input()));

        return $this;
    }

    /**
     * @return string
     */
    private function findIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return "";
    }

    /**
     * @return $this
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
     * @return bool
     */
    public function exists()
    {
        return !empty($this->getIp());
    }

    /**
     * Get the value of ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set the value of ip
     *
     * @param string $ip
     *
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get the value of guzzleClient
     *
     * @return Client
     */
    public function getGuzzleClient()
    {
        return $this->guzzleClient;
    }

    /**
     * Set the value of guzzleClient
     *
     * @param Client $guzzleClient
     *
     * @return $this
     */
    public function setGuzzleClient(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;

        return $this;
    }

    /**
     * @param \stdClass $jsonResponse
     */
    public function setFromJsonResponse($jsonResponse)
    {
        $this->setCountry(isset($jsonResponse->country) ? $jsonResponse->country : "")
            ->setRegion(isset($jsonResponse->region) ? $jsonResponse->region : "")
            ->setCity(isset($jsonResponse->city) ? $jsonResponse->city : "")
            ->setIp_payload(json_encode($jsonResponse));
    }

    /**
     * @return $this
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
     * Get the value of modelFactory
     *
     * @return ModelFactory
     */
    public function getModelFactory()
    {
        return $this->modelFactory;
    }

    /**
     * Set the value of modelFactory
     *
     * @param ModelFactory $modelFactory
     *
     * @return $this
     */
    public function setModelFactory(ModelFactory $modelFactory)
    {
        $this->modelFactory = $modelFactory;

        return $this;
    }

    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of country
     *
     * @return  string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of country
     *
     * @param string $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set the value of region
     *
     * @param string $region
     *
     * @return $this
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get the value of city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get the value of path
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of ip_payload
     *
     * @return string
     */
    public function getIp_payload()
    {
        return $this->ip_payload;
    }

    /**
     * Set the value of ip_payload
     *
     * @param string $ip_payload
     *
     * @return $this
     */
    public function setIp_payload($ip_payload)
    {
        $this->ip_payload = $ip_payload;

        return $this;
    }

    /**
     * Get the value of request_payload
     *
     * @return string
     */
    public function getRequest_payload()
    {
        return $this->request_payload;
    }

    /**
     * Set the value of request_payload
     *
     * @param string $request_payload
     *
     * @return $this
     */
    public function setRequest_payload($request_payload)
    {
        $this->request_payload = $request_payload;

        return $this;
    }

    /**
     * Get the value of created_at
     *
     * @return string
     */
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @param string $created_at
     *
     * @return $this
     */
    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of updated_at
     *
     * @return string
     */
    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    /**
     * Set the value of updated_at
     *
     * @param string $updated_at
     *
     * @return $this
     */
    public function setUpdated_at($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
