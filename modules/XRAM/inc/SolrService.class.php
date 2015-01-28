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

require_once(XIMDEX_ROOT_PATH . "/inc/utils.php");
require_once(XIMDEX_ROOT_PATH . "/inc/log/Log.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/log/logger/Logger_error.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/db/db.php");
require_once(XIMDEX_ROOT_PATH . "/inc/model/Versions.php");
//
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php' );
require("ISolrService.iface.php");

/**
 * <p>SolrService class</p>
 * <p>Solr backend used to store node information</p>
 */
class SolrService implements ISolrService
{

    private $solrClient;

    /**
     *
     * <p>Class constructor</p>
     * <p>Creates an instance of Solr Service</p>
     *
     * <p>If no parameters are passed to this function, the values configured in Config table are used</p>
     * <p>As fallback, if no configuration values are found in Config table
     *	 localhost, 8983 and '/solr/collection1' are used as default values for $solrServer, $solrPort
     *	 and $solrCorePath respectively</p>
     *
     *
     * @param string $solrServer The Solr server. Default localhost
     * @param string $solrPort The Solr server port. Default 8983
     * @param string $solrCorePath The path to the Solr Core to be used. Default /solr/collection1
     *
     */
    public function __construct($solrServer = 'localhost', $solrPort = 8983, $solrCorePath = '/solr/collection1')
    {
        $options = array(
                   'hostname' => $solrServer,
	           'port' => $solrPort,
    	           'path' => $solrCorePath
         );
        $this->solrClient = new SolrClient($options);
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
		
	$solrDocument = $this->createSolrDocumentFromVersion($version, $content);
	$response = $this->solrClient->addDocument($solrDocument, false, 0);	
	    
	$response = $response->getResponse();
	$response = $response['responseHeader']['status'] == 0;
        if ($response && ($commitNode || is_null($commitNode))) {
            $this->performCommit();
        }
        
        return $response['responseHeader']['status'] === 0;
    }
    
    /**
     * <p>Creates a SolrInputDocument from a Version object to be send to Solr</p>
     *
     * @param object $version The Version object to be transformed into SolrInputDocument
     * @param string $content The content of the version
     *
     * return SolrInputDocument An instance of SolrDocument
     */
    private function createSolrDocumentFromVersion($version, $content) {
        
    	$newDocument = new SolrInputDocument();

        $newDocument->addField("id", $version->get('IdVersion'));
        $newDocument->addField("nodeid", $version->get('IdNode'));
        $newDocument->addField('version', $version->get('Version'));
        $newDocument->addField('subversion', $version->get('SubVersion'));
        $newDocument->addField('user', $version->get('IdUser'));
        $newDocument->addField('date', $version->get('Date'));
        
        if (!(is_null($version->get('Comment')))) {
            $newDocument->addField('comment', $version->get('Comment'));
        }
        
        if (!(is_null($version->get('MimeType')))) {
            $newDocument->addField('mimetype', $version->get('MimeType'));
        }
        
        $newDocument->addField("content", $content);

		return $newDocument;
    }
    
    /**
     * <p>Performs a Solr Commit </p>
     * @param boolean $isSoftCommit Optional. Indicates whether the commit is a soft or hard commit
     *                              Default is Hard Commit
     */
    private function performCommit($isSoftCommit = false)
    {

        $attributes = $isSoftCommit ? "softCommit='true'" : "";
        $commitRequest = "<commit " . $attributes . "/>";
        $this->solrClient->request($commitRequest);
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
		
        $getVersionQuery = new SolrQuery();
        $strQuery = "id:" . $version->get('IdVersion');

        $getVersionQuery->setQuery($strQuery);
        $getVersionQuery->addField("*");
        $getVersionQuery->setRows(1);

        $queryResponse = $this->solrClient->query($getVersionQuery);
        $resp = $queryResponse->getResponse();
        $docs = $resp["response"]["docs"];
        if (!is_array($docs)) {
            XMD_Log::warning('Could not retrieve the node version');
            return null;
        }
		
	$doc = $docs[0];
	return $doc;
    
	}
	
    /*
     * <p>Delete a node version from Solr</p>
     * 
     * @param int $idVersion The node version id to be deleted
     */
    public function deleteNode($idVersion) {
        $solrUpdateResponse = $this->solrClient->deleteById($idVersion);
        $this->performCommit();
        return true;
    }

}

?>
