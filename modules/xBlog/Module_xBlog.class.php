<?php

use Ximdex\Modules\Module;

/**
 * Class Module_xBlog
 */
class Module_xBlog extends Module
{
    /**
     *  Listen the XIMDEX_START event
     */
    function init(){
        /*
        App::setListener(Events::XIMDEX_START, function($event){
           // error_log("Hello from xBlog!");
        });
        */
    }

    //Class constructor
    public function __construct()
    {
        // Call Module constructor.
        parent::__construct("xBlog", dirname(__FILE__));
    }

    /**
     * Load SQL constructor
     *
     * @return bool
     */
    function install()
    {
        $this->loadConstructorSQL("xBlog.constructor.sql");
        return parent::install();
    }

    /**
     *
     */
    function uninstall()
    {
        $this->loadDestructorSQL("xBlog.destructor.sql");
        parent::uninstall();
    }
}