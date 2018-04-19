<?php

namespace Ximdex\IO\Connection;

use Ximdex\Models\Server;

class Connector
{
    protected $server;
    protected $error;
    protected $type;
    const TYPE_API = 'API';
    
    /**
     * @param string $type
     * @param Server $server
     */
    public function __construct(Server $server = null)
    {
        $this->server = $server;
        $this->error = null;
        $this->type = null;
    }
    
    /**
     * @return string|NULL
     */
    public function getError() : ?string
    {
        return $this->error;
    }
    
    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }
    
    /**
     * @param string $type
     */
    public function setType(string $type) : void
    {
        $this->type = $type;
    }
}