<?php namespace App\Http\Controllers\Url;

use App\Entities\Classes\ClassFactory;
use App\Entities\Models\UrlModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    /**
     * @var ClassFactory
     */
    private $classFactory;

    /**
     * @param ClassFactory $ClassFactory
     */
    public function __construct(ClassFactory $ClassFactory)
    {
        $this->middleware(function ($request, $next) use ($ClassFactory) {
            if (strpos($request->path(), "api/topVisits/") === false) {
                $ClassFactory->newVisitorInstance()
                    ->setRequestInformation($request)
                    ->load()
                    ->save();
            }

            return $next($request);
        });

        $this->classFactory = $ClassFactory;
    }

    /**
     * Landing page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('url/url');
    }

    /**
     * Landing page for the shortened URL. Will either redirect
     * to the page if valid or prompt the user if the page does
     * not seem valid.
     *
     * @param string $shortened
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function redirect($shortened)
    {
        $url = $this->getClassFactory()
            ->newUrlInstance()
            ->setShortenedUrl($shortened);

        // site is blocked, do not redirect
        if ($this->getClassFactory()->newBlockedSite()->isBlockedSite($url)) {
            return redirect()->action('Url\UrlController@index');
        }

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
     * @return ClassFactory
     */
    protected function getClassFactory()
    {
        return $this->classFactory;
    }

    /**
     * Create a url that does not exist or
     * return on that already does exist.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        // site is blocked, do not redirect
        if ($this->getClassFactory()->newBlockedSite()->isBlockedSite($request->input('url'))) {
            return response()->json(array('errorMessage' => "failed to create short url. Try again later"), 400);
        }

        $url = $this->getClassFactory()
            ->newUrlInstance()
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
     * @return JsonResponse
     */
    public function topVisits($number = 10)
    {
        $modelUrls = $this->getClassFactory()
            ->newUrlInstance()
            ->getMostVisits(100);

        // loop through and set each of the top visits as an object
        $topVisits = array();
        /** @var UrlModel $model */
        foreach ($modelUrls as $model) {
            if ($this->getClassFactory()->newBlockedSite()->isBlockedSite($model->fullUrl)) {
                continue;
            }

            $topVisits[] = $this->getClassFactory()
                ->newUrlInstance()
                ->setFromModel($model);

            if (count($topVisits) >= $number) {
                break;
            }
        }

        return response()->json(array('topVisits' => $topVisits));
    }
}
