<?php
/**
 * Created by PhpStorm.
 * User: jvargas
 * Date: 23/02/16
 * Time: 16:12
 */

namespace Ximdex\API;


class APIException extends \Exception
{
    private $status;

    public function __construct($message = "", $status = 0)
    {
        parent::__construct($message, 0, null);
        $this->status = $status;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setStatus($status){
        $this->status = $status;
    }
}