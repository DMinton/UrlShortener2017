<?php namespace App\Entities\Models;

use App\Entities\Classes\Visitor;
use Illuminate\Database\Eloquent\Model;

class VisitorModel extends Model
{
    protected $table = 'visitor';

    /**
     * @param Visitor $visitor
     * @return $this
     */
    public function saveNewVisitor(Visitor $visitor)
    {
        $this->ip = $visitor->getIp();
        $this->country = $visitor->getCountry();
        $this->region = $visitor->getRegion();
        $this->city = $visitor->getCity();
        $this->path = $visitor->getPath();
        $this->ip_payload = $visitor->getIp_payload();
        $this->request_payload = $visitor->getRequest_payload();

        $this->save();

        return $this;
    }
}
