<?php namespace App\Entities\Classes;

use App;

class ClassFactory {

    /**
     * @return Url
     */
    public function newUrlInstance() {
        return App::make(Url::class);
    }

    /**
     * @return Visitor
     */
    public function newVisitorInstance() {
        return App::make(Visitor::class);
    }
}
