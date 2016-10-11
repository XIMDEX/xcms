<?php

namespace Ximdex\Tasks;
use Ximdex\Runtime\App ;


class Worker 
{
    private $queueManager  = null;
    private $currentExec = 0 ;
 
    private $methods = [] ;



    public function __construct()
    {
        $this->queueManager = new Manager();
        $this->queue = $this->queueManager->getQueueServer();
    }


    public function addMethod(  $name, $function ) {
        $this->methods[$name] = $function ;
    }



    public function run( $maxExecs = 0 )
    {
 

        while ( $maxExecs == 0 || $this->currentExec <= $maxExecs ) {

            $queueName = App::getInstance()->getRuntimeValue('queueName', 'ximdex');

            // grab the next job off the queue and reserve it
            $job = $this->queue->watch( $queueName )
                ->ignore('default')
                ->reserve();

            $jobData = json_decode($job->getData(), false);
            $function = $jobData->function;
            $data = $jobData->user_data;
            if ( array_key_exists( $function, $this->methods)) {
                $this->currentExec += $this->methods[$function]( $job, $data ) ;
                continue;

            }
            echo "{$function}  -> Unknown\n";
            $this->queue->release($job);
            $this->currentExec++;
            break ;
        }
    }
}