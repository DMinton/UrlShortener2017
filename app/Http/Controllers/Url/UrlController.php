<?php

namespace App\Http\Controllers\Url;

use App\Http\Controllers\Controller;
use App\Classes\Url\Url as Url;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    private $Url;

    public function __construct(Url $Url) {
        $this->Url = $Url;
    }

    public function index() {
        return view('url/url');
    }

    public function redirect($shortened) {
        $urlObject = $this->Url->findShortenedUrl($shortened);

        if ($urlObject->isEmpty()) {
            return redirect()->action('UrlController@index');
        }

        $url = $urlObject->first();

        $url->addOneVisit();

        return redirect($url->fullUrl);
    }

    public function create(Request $request) {
        // create the url and return it
        $url = $this->Url->findOrCreateShortenedUrl($request->input('url'));

        return response()->json(array('url' => $url));
    }

    public function topVisits($number = 10) {
        // create the url and return it
        $topVisits = $this->Url->getMostVisits($number);

        return response()->json(array('topVisits' => $topVisits));
    }
}
