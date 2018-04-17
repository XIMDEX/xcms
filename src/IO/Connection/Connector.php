<?php

namespace Ximdex\IO\Connection;

use Ximdex\Models\Server;

class Connector
{
    protected $server;
    protected $error;
    
    public function __construct(Server $server = null)
    {
        $this->server = $server;
        $this->error = null;
    }
    
    public function getError() : ?string
    {
        return $this->error;
    }
}