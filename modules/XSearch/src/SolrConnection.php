<?php

use Ximdex\Runtime\App;

class SolrConnection
{
    /**
     * @var Solarium\Core\Client\Client
     */
    private $client;
    private $server;
    private $port;
    private $path;
    private $core;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $this->server = App::get('SolrServer');
        $this->core = App::get('SolrCore');
        $this->port = App::get('SolrPort');
        $this->path = App::get('SolrPath');

        $config = [
            'endpoint' =>
                ['localhost' =>
                    ['host' => $this->server,
                        'port' => $this->port,
                        'path' => $this->path,
                        'core' => $this->core,
                        'timeout' => 10
                    ]
                ]
        ];
        $this->client = new Solarium\Client($config);
    }

    public function GetClient(){
        return $this->client;
    }
}