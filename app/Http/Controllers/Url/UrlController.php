<?php namespace App\Http\Controllers\Url;

use App\Http\Controllers\Controller;
use App\Classes\Url\UrlFactory;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    /**
     * @var UrlFactory
     */
    private $urlFactory;

    public function __construct(UrlFactory $UrlFactory) {
        $this->urlFactory = $UrlFactory;
    }

    /**
     * Landing page
     *
     * @return view
     */
    public function index() {
        return view('url/url');
    }

    /**
     * Landing page for the shortened URL. Will either redirect
     * to the page if valid or prompt the user if the page does
     * not seem valid.
     *
     * @param String $shortened
     * @return view
     */
    public function redirect($shortened) {
        $url = $this->urlFactory
            ->newInstance()
            ->setShortenedUrl($shortened);

        // If the url was not found, redirect to main landing page
        if (!$url->loadByShortenedUrl()) {
            return redirect()->action('Url\UrlController@index');
        }

        // if the url does not seem valid, prompt the user
        if (!$url->isValidUrl()) {
            return view('url/problem')->with(array('url' => $url));
        }

        $url->addOneVisit();

        return redirect($url->getFullUrl());
    }

    /**
     * Create a url that does not exist or
     * return on that already does exist.
     *
     * @param Request $request
     * @return json
     */
    public function create(Request $request) {
        $url = $this->urlFactory
            ->newInstance()
            ->setFullUrl($request->input('url'));

        // if not found, we need to create the url
        if (!$url->loadByUrl()) {
            $url->create();
        }

        return response()->json(array('url' => $url));
    }

    /**
     * Returns a json of the most visited. Take an
     * integer of the length of the requested list.
     *
     * @param int $number
     * @return json
     */
    public function topVisits($number = 10) {
        $modelUrls = $this->urlFactory
            ->newInstance()
            ->getMostVisits($number);

        // loop through and set each of the top visits as an object
        $topVisits = array();
        foreach ($modelUrls as $model) {
            $topVisits[] = $this->urlFactory
                ->newInstance()
                ->setFromModel($model);
        }

        return response()->json(array('topVisits' => $topVisits));
    }
}
