<?php


namespace Ximdex\Modules;



interface iModule
{

    public function setup( array $object );
    public function start();
    public function stop();
    public function install();
    public function uninstall();
    public function info();
    public function getName();
}
