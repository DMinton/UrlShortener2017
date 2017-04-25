<?php

namespace App\Http\Controllers\Url;

use App\Http\Controllers\Controller;
use App\Classes\Url\Url as Url;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function index() {
        return view('url/url');
    }

    public function redirect($shortened) {
        $url = Url::init()->setShortenedUrl($shortened);

        if (!$url->loadByShortenedUrl()) {
            return redirect()->action('Url\UrlController@index');
        }

        if (!$url->isValidUrl()) {
            return view('url/problem')->with(array('url' => $url));
        }

        $url->addOneVisit();

        return redirect($url->getFullUrl());
    }

    public function create(Request $request) {
        $url = Url::init()->setFullUrl($request->input('url'));
        
        if (!$url->loadByUrl()) {
            $url->create();
        }

        return response()->json(array('url' => $url));
    }

    public function topVisits($number = 10) {
        $modelUrls = URL::getMostVisits($number);

        $topVisits = array();
        foreach ($modelUrls as $model) {
            $topVisits[] = Url::init()->setFromModel($model);
        }
        
        return response()->json(array('topVisits' => $topVisits));
    }
}
