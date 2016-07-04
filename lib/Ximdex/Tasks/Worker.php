<?php

namespace Ximdex\Tasks;


class Worker
{
    private $qm = null;
    private $currentExec = 0 ;

    private $methods = [] ;



    public function __construct()
    {
        $this->qm = new Manager();
        $this->queue = $this->qm->getQueueServer();
    }


    public function addMethod(  $name, $function ) {
        $this->methods[$name] = $function ;
    }



    public function run( $maxExecs = 0 )
    {


        while ( $maxExecs == 0 || $this->currentExec <= $maxExecs ) {

            // grab the next job off the queue and reserve it
            $job = $this->queue->watch('xbuk')
                ->ignore('default')
                ->reserve();

            $job_data = json_decode($job->getData(), false);
            $function = $job_data->function;
            $data = $job_data->user_data;
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