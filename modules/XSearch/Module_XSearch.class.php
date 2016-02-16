<?php


use Ximdex\Event\NodeEvent;
use Ximdex\Modules\Module;
use Ximdex\Events;
use Ximdex\Runtime\App;

ModulesManager::file('/src/SolrExporter.php', 'XSearch');

/**
 * Class Module_XBUK
 */
class Module_XSearch extends Module
{
    /**
     *  Listen the XIMDEX_START event
     */
    function init(){
        App::setListener(Events::NODE_TOUCHED, function(NodeEvent $event){
            $exporter = new SolrExporter;
            $exporter->ExportByNodeId($event->getNodeId());
        });
    }

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