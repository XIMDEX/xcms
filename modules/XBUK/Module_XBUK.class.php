<?php

use Ximdex\Modules\Module;
use Ximdex\Events;
use Ximdex\Runtime\App;

/**
 * Class Module_XBUK
 */
class Module_XBUK extends Module
{
    /**
     *  Listen the XIMDEX_START event
     */
    function init(){
        App::setListener(Events::XIMDEX_START, function($event){
            error_log("Hello from XBUK!");
        });
    }
    //Class constructor
    public function __construct()
    {
        // Call Module constructor.
        parent::__construct("XBUK", dirname(__FILE__));
    }

    function install()
    {
        parent::install();
        return true;
    }

    function uninstall()
    {
        parent::uninstall();
    }
}