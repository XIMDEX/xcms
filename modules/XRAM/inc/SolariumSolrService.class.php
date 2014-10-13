<?php

/**
 *  \details &copy; 2014  Open Ximdex Evolution SL [http://www.ximdex.org]
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

require_once(XIMDEX_ROOT_PATH . "/inc/utils.inc");
require_once(XIMDEX_ROOT_PATH . "/inc/log/Log.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/log/logger/Logger_error.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/db/db.inc");
require_once(XIMDEX_ROOT_PATH . "/inc/model/Versions.inc");
require_once(XIMDEX_ROOT_PATH . "/inc/persistence/Config.class.php");
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php' );
require_once("ISolrService.iface.php");
require_once (XIMDEX_ROOT_PATH . '/extensions/solarium/solarium/library/Solarium/Autoloader.php');
Solarium_Autoloader::register();

/**
 * <p>SolrService class</p>
 * <p>Solr backend used to store node information</p>
 */
class SolariumSolrService implements ISolrService
{
    private $solrClient;

    /**
     *
     * <p>Class constructor</p>
     * <p>Creates an instance of Solr Service</p>
     *
     *
     *
     * @param string $solrServer The Solr server. Default localhost
     * @param string $solrPort The Solr server port. Default 8983
     * @param string $solrCorePath The path to the Solr Core to be used. Default /solr/collection1
     *
     */
    public function __construct($solrServer = 'localhost', $solrPort = 8983, $solrCorePath = '/solr/collection1')
    {
        $solrCorePath = substr($solrCorePath,0,1) !== '/' ? '/'.$solrCorePath : $solrCorePath;
        $options = array(
            'adapteroptions' => array(
                    'host' => $solrServer,
                    'port' => $solrPort,
                    'path' => $solrCorePath
            )
        );
        
        $this->solrClient = new Solarium_Client($options);
    }

    /**
     *
     * <p>Indexes a node version in Solr identified by the version id</p>
     *
     *
     * @param string|int $idVersion The id of the node version to be indexed
     * @param string $content The content of the node
     * @param boolean $commitNode Boolean indicating if a commit needs to be performed after the indexing process
     *
     */
    public function indexNode($idVersion, $content, $commitNode = true) {

            $version = new Version($idVersion);
            if (!($version->get('IdVersion') > 0)) {
		XMD_Log::debug("Trying to index a version {$idVersion} that does not exist");
            }

            $node = new Node($version->get('IdNode'));
            if (!($node->get('IdNode') > 0)) {
		$this->Debug('Se ha solicitado indexar una versión de un nodo que no existe');
		return false;
            }
		
            $updateRequest = $this->createSolrUpdateRequestFromVersion($version, $content);
            if($commitNode) {
                $updateRequest->addCommit();
            }
            try{
                $response = $this->solrClient->update($updateRequest);
                return $response->getStatus() === 0 ? true : false;
            }
            catch(Exception $e) {
                XMD_Log::debug($e->getMessage());
                return false;
            }
    }
    
    /**
     * <p>Creates a Solarium_Query_Update from a Version object to be send to Solr</p>
     *
     * @param object $version The Version object to be transformed into SolrInputDocument
     * @param string $content The content of the version
     *
     * return Solarium_Query_Update An instance of Solarium_Query_Update containing the request with the new document already added on it
     */
    private function createSolrUpdateRequestFromVersion($version, $content) {
        
        // Creating update query instance
        $update = $this->solrClient->createUpdate();

        // create a new document for the data
        $newDocument = $update->createDocument();
        
        $newDocument->id = $version->get('IdVersion');
        $newDocument->nodeid = $version->get('IdNode');
        $newDocument->version = $version->get('Version');
        $newDocument->subversion = $version->get('SubVersion');
        $newDocument->user = $version->get('IdUser');
        $newDocument->date = $version->get('Date');
        
        if (!(is_null($version->get('Comment')))) {
            $newDocument->comment = $version->get('Comment');
        }
        
        if (!(is_null($version->get('MimeType')))) {
            $newDocument->mimetype = $version->get('MimeType');
        }
        
//        $newDocument->content = base64_encode($content);
          $newDocument->content = $content;
        
        $update->addDocument($newDocument);
	return $update;
    }
    
    /**
     * <p>Retrieves an specific version of a node</p>
     * 
     * @param int $idVersion The version of the node to be retrieved
     * 
     * return array The retrieved node or null if an error ocurred
     */
     public function retrieveNode($idVersion) {
        $version = new Version($idVersion);
        if (!($version->get('IdVersion') > 0)) {
            XMD_Log::debug("Trying to index a version {$idVersion} that does not exist");
	}
		
        $getVersionQuery = $this->solrClient->createSelect();
        
        $strQuery = "id:" . $version->get('IdVersion');

        $getVersionQuery->setQuery($strQuery);
        $getVersionQuery->setFields(array("*"));
        $getVersionQuery->setRows(1);
        try {
            $queryResponse = $this->solrClient->select($getVersionQuery);
        
            if ($queryResponse->getNumFound() === 0) {
                XMD_Log::warning('Could not retrieve the node version');
                return null;
            }
		
            $doc = array_pop($queryResponse->getDocuments());
            
            $res = array('id' => $doc['id'], 'content' => $doc['content']);
            return $res;
        }
        catch(Exception $e) {
            XMD_Log::debug($e->getMessage());
            return null;
        }
    
    }
	
    /*
     * <p>Delete a node version from Solr</p>
     * 
     * @param int $idVersion The node version id to be deleted
     */
     public function deleteNode($idVersion) {
        $solrUpdateRequest = $this->solrClient->createUpdate();
        $solrUpdateRequest->addDeleteById($idVersion);
        $solrUpdateRequest->addCommit();
        try {
        $this->solrClient->update($solrUpdateRequest);
        }
        catch (Exception $e) {
            XMD_Log::debug($e->getMessage());
            return false;
        }
        return true;
    }

}

?>
