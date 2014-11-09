<?php

/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */
if (!defined('XIMDEX_ROOT_PATH')) {
    define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../"));
}

ModulesManager::file('/inc/persistence/Config.class.php');
ModulesManager::file('/inc/persistence/store/FileSystemStore.class.php');
ModulesManager::file('/inc/persistence/store/ChainedStore.class.php');
ModulesManager::file('/inc/SolrStore.class.php', 'XRAM');
ModulesManager::file('/inc/SolariumSolrService.class.php', 'XRAM');
ModulesManager::file('/inc/ProcessorFactory.class.php', 'XRAM');

/**
 * <p>Store Factory</p>
 * <p>Creates instances of stores</p>
 * 
 */
class StoreFactory
{

    private static $ACTIVE_REPOSITORY = 'ActiveRepository';

    const SOLR_STORE_CONF_VALUE = "solr";
    const FILESYSTEM_STORE_CONF_VALUE = "filesystem";
    const CHAINED_STORE_CONF_VALUE = "chained";

    /**
     * <p>Default constructor</p>
     */
    public function __construct()
    {
        
    }

    /**
     * <p>Creates an instance of {@link FileSystemStore}</p>
     * @return \FileSystemStore an instance of FileSystemStore store
     */
    public function createFileSystemStore()
    {
        return new FileSystemStore();
    }

    /**
     * <p>Creates an instance of {@link SolrStore}</p>
     * 
     * <p>The values configured in Config table are used to create the Solr store</p>
     * <p>As fallback, if no configuration values are found in Config table
     * localhost, 8983 and '/solr/collection1' are used as default values for Solr server, Solr port
     * and Solr core path respectively</p> 
     * @return \SolrStore an instance of SolrStore store
     * @throws RuntimeException if XRAM module is not enabled (XRAM module contains the SolrStore definition)
     */
    public function createSolrStore()
    {
        if (!ModulesManager::isEnabled('XRAM')) {
            throw new RuntimeException('Module XRAM is not enabled');
        }

        $solrServer = Config::GetValue("SolrServer");
        $solrPort = Config::GetValue("SolrPort");
        $solrCorePath = Config::GetValue("SolrCorePath");

        $store = new SolrStore();
        $solrService = new SolariumSolrService($solrServer, $solrPort, $solrCorePath);
        $store->setSolrService($solrService);
        $processorFactory = new ProcessorFactory();
        $aesProcessor = $processorFactory->createAESProcessor();
        $store->addProcessor($aesProcessor);

        return $store;
    }

    public function createChainedStore()
    {
        $store = new ChainedStore();
        $store->addStore($this->createSolrStore());
        $store->addStore($this->createFileSystemStore());
        return $store;
    }

    /**
     * <p>Gets the default configured store from configuration</p>
     * @return an instance of <code>Store</code>
     */
    public static function getDefaultStore()
    {
        /*
         * Using static variable to initialize only once the store
         */
        static $store;

        if (!isset($store)) {
            $storeFactory = new self(); // $class = __CLASS__; $storeFactory = new $class();
            $activeRepository = Config::getValue(StoreFactory::$ACTIVE_REPOSITORY);
            if ($activeRepository === NULL) {
                $store = $storeFactory->createFileSystemStore();
                return $store;
            }

            switch ($activeRepository) {
                case StoreFactory::SOLR_STORE_CONF_VALUE:
                    $store = $storeFactory->createSolrStore();
                    break;
                case StoreFactory::CHAINED_STORE_CONF_VALUE:
                    $store = $storeFactory->createChainedStore();
                    break;
                default:
                    $store = $storeFactory->createFileSystemStore();
                    break;
            }
        }

        return $store;
    }

}

?>
