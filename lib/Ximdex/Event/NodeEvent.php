<?php
/**
 * Created by PhpStorm.
 * User: jvargas
 * Date: 15/02/16
 * Time: 13:30
 */

namespace Ximdex\Event;


use Symfony\Component\EventDispatcher\Event;

class NodeEvent extends Event
{
    protected $nodeId;

    public function __construct($nodeId)
    {
        $this->nodeId = $nodeId;
    }

    public function getNodeId()
    {
        return $this->nodeId;
    }
}