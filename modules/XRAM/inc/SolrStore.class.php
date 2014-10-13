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

if (!defined ('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath (dirname (__FILE__)."/../../../"));
}
require_once(XIMDEX_ROOT_PATH . "/inc/modules/ModulesManager.class.php");
ModulesManager::file('/inc/persistence/store/Store.iface.php');
require_once('SolariumSolrService.class.php');

/**
 * <p>SolrStore class</p>
 * <p>Manages the CRUD operations of nodes using Solr as backend</p>
 * 
 */
class SolrStore implements Store
{
    private $solrService;
    private $processors = array();
    
    public function __construct()
    {
        $this->solrService = new SolariumSolrService();
    }
    
    /**
     * <p>Sets the instace of <code>ISolrService</code> used to communicates with Solr</p>
     * @param ISolrService $solrService the ISolrService instance to be used
     */
    public function setSolrService(ISolrService $solrService) {
        $this->solrService = $solrService;
    }
    
    /**
     * <p>Gets the <code>ISolrService</code> instance being used to interact with Solr
     * @return ISolrService the instance being used
     */
    public function getSolrService() {
        return $this->solrService;
    }
    
    /**
     * <p>Adds a new processor to the list of processors of this store</p>
     * @param IndexerLifecycle $processor A <code>IndexerLifecycle</code> instance to be added to the list of processors for this store
     */
    public function addProcessor($processor) {
        if(!in_array($processor, $this->processors)) {
            array_push($this->processors, $processor);
        }
    }
    
    /**
     * <p>Remove a processor from the list of processors of this store</p>
     * @param IndexerLifecycle $processor
     * @return Boolean indicating if the processor has been deleted sucessfully or not
     */
    public function removeProcessor($processor) {
        $index = array_search($processor, $this->processors);
        if($index !== FALSE) {
            $this->processors = array_splice($this->processors, $index, 1);
            return true;
        }
        
        return false;
    }
    
    /**
     * <p>Checks whether the processor exists in the list of processors</p>
     * @param IndexerLifecycle $processor The processor to be searched
     * @return Boolean indicating whether the processor already exists in the list of processors or not
     */
    public function hasProcessor($processor) {
        return array_search($processor, $this->processors) !== FALSE;
    }
    
    /**
     * <p>Gets the node content from the specified version id in the file system</p>
     * 
     * @param integer $nodeId The id of the node which retrieve the content from
     * @param integer $versionId The version id from a node to be retrieved or version number
     * @param integer $subversion The subversion number
     * 
     * @return string The content of the node for the specified version/subversion or false if an error occurred while retrieving the content
     */
    public function getContent($nodeId, $versionId, $subversion = null)
    {
        $df = new DataFactory($nodeId);
        $version = $df->getVersionId($versionId, $subversion);
	$resultNode = $this->retrieveNode($version);
        if(is_null($resultNode)) {
            XMD_Log::warning('No se ha podido obtener el contenido de Solr. Intentando obtener contenido del sistema de ficheros');
            return false;
	}
	
        $content = $resultNode['content'];
        return $content;
    }

    /**
     * <p>Sets the node content for the specific version/subversion</p>
     * 
     * @param string $content The content to be put
     * @param integer $nodeId The id of the node to set the content
     * @param integer $versionId The version id from a node to set the content
     * @param integer $subversion The subversion number
     */
    public function setContent($content, $nodeId, $versionId, $subversion) 
    {
        $df = new DataFactory($nodeId);
        $idVersion = $df->getVersionId($versionId,$subversion);
	$result = $this->indexNode($idVersion, $content);
	$msg = $result ? "Node with Id Version ".$idVersion." indexed successfully" : "Error indexing node with Id Version ".$idVersion;
	XMD_log::debug($msg);
        
        return $result;
    }

    /**
     * <p>Removes the node content from a specific version/version</p>
     * 
     * @param integer $nodeId The id of the node which retrieve the content from
     * @param integer $versionId The version id from a node to be retrieved or version number
     * @param integer $subversion The subversion number
     */
    public function deleteContent($nodeId, $versionId, $subversion = null)
    {
        $df = new DataFactory($nodeId);
        $versionToDelete = $df->getVersionId($versionId, $subversion);
	$res = $this->deleteNode($versionToDelete, true);
	$msg = $res ? "Node version ".$versionToDelete." deleted successfully" : "Error deleting node version ".$versionToDelete;
	XMD_log::debug($msg);

    }
    /**
     * <p>Retrieves the Solr document representing a node</p>
     * 
     * @param integer $idVersion The id of the version to be retrieved (matches the Solr document ID)
     * @return The Solr document representing the node
     */
    private function retrieveNode($idVersion) {
        if(!is_numeric($idVersion)) {
            XMD_Log::warning('Se ha intentado recuperar un nodo con un IdVersion no valido.');
            return null;	
        }
        
        $this->applyLifecycleMethod('beforeRetrieve');
        $node =	$this->solrService->retrieveNode($idVersion);
        $node = $this->applyLifecycleMethod('afterRetrieve', $node);
	return $node;
    }
    
    /**
     * <p>Apply a Lifecycle method in the configured processors</p>
     * 
     * @param String $method Method name to be executed
     * @param mixed $parameters Optional. The parameters used to execute the method
     * @return mixed. The result of the processors chain
     */
    private function applyLifecycleMethod($method, $parameters = array()) {
        foreach($this->processors as $processor) {
            if(method_exists($processor, $method)) {
                $parameters = call_user_func_array(array($processor, $method), array($parameters));
            }
        }
        
        return $parameters;
    }
    
    /**
     * <p>Deletes the node from Solr</p>
     * @param integer $idVersion the id of the version to be deleted (matches the Solr document ID)
     * @param boolean $commit Boolean indicating whether a commit must be done after delete
     * @return boolean
     */
    private function deleteNode($idVersion, $commit = true) {
	if(!is_numeric($idVersion)) {
            XMD_Log::warning('Se ha intentado eliminar un nodo con un IdVersion no valido.');
            return false;	
	}		
        
        $this->applyLifecycleMethod('beforeDelete', $idVersion);
	$result = $this->solrService->deleteNode($idVersion, $commit);
        $this->applyLifecycleMethod('afterDelete', $idVersion);
	return $result;
    }
    
    /**
     * <p>Indexes a node identified by the version if in Solr</p>
     * 
     * @param type $idVersion The id of the Solr document
     * @param type $content The content of the document
     * @param type $commitNode Boolean indicating whether a commit must be done after insert
     * @return boolean
     */
    private function indexNode($idVersion, $content, $commitNode = true) {
        if (!is_numeric($idVersion)) {
            XMD_Log::warning('Se ha intentado indexar un nodo por un IdVersion no valido.');
            return false;
        }
        
        $nodeToIndex = $this->applyLifecycleMethod('beforeIndex', array('id' => $idVersion, 'content' => $content));
        $result = $this->solrService->indexNode($nodeToIndex['id'], $nodeToIndex['content'], $commitNode);
        $this->applyLifecycleMethod('afterIndex', $nodeToIndex);
        return $result;
}
}

?>
