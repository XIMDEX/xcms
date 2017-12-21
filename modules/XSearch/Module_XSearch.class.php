<?php

use Ximdex\Modules\Module;

ModulesManager::file('/src/SolrExporter.php', 'XSearch');

/**
 * Class Module_XSearch
 */
class Module_XSearch extends Module
{


    //Class constructor
    public function __construct()
    {
        // Call Module constructor.
        parent::__construct("XSearch", dirname(__FILE__));
    }

    /**
     * Load SQL constructor
     *
     * @return bool
     */
    function install()
    {
        $this->loadConstructorSQL("XSearch.constructor.sql");
        return parent::install();
    }

    /**
     *
     */
    function uninstall()
    {
        $this->loadDestructorSQL("XSearch.destructor.sql");
        parent::uninstall();
    }
}