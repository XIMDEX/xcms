<?php

namespace Ximdex\Tasks;

 
use Pheanstalk\Pheanstalk;
use stdClass;
use Ximdex\Runtime\App ;

class Manager
{
    /**
     * @var null|Pheanstalk
     */
    private $queueServer = null;

    public function __construct()
    {
        $this->queueServer = new Pheanstalk('127.0.0.1');


    }

    public function getQueueServer()
    {

        return $this->queueServer;
    }

    static public function sendTask($name = "xbuk", $data = "")
    {

        $queueName = App::getValue( 'queueName', 'ximdex');
        $queueManager = new Pheanstalk('127.0.0.1');
 
        $job = new stdClass();
        $job->function = $name; 
        $job->user_data = $data;
        $queueManager->useTube( $queueName )->put(json_encode($job));

    }
}