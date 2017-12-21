<?php

use Ximdex\Modules\Module;

/**
 * Class Module_xBlog
 */
class Module_xBlog extends Module
{


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