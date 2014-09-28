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
require_once(XIMDEX_ROOT_PATH . "/inc/modules/ModulesManager.class.php");
ModulesManager::file('/inc/persistence/store/Store.iface.php');

/**
 * <p>ChainedStore class</p>
 * <p>Manages the CRUD operations of nodes using a chain of stores</p>
 */
class ChainedStore implements Store
{

    private $stores;

    /**
     * <p>Construct an instance of <code>ChainedStore</code></p>
     * 
     * @param Store|array $stores a simple <code>Store</code> instance or an array of <code>Stores</code>
     */
    public function __construct($stores = array())
    {
        if (!is_array($stores)) {
            $stores = array($stores);
        }

        $this->stores = $stores;
    }

    /**
     * <p>Adds a new Store</p>
     * @param Store $store the <code>Store</code> to be added
     * @param Boolean $atBeginning indicates if the new Store must be added at the beginning or at the end of the chain
     */
    public function addStore(Store $store, $atBeginning = false)
    {
        $atBeginning ? array_unshift($this->stores, $store) : array_push($this->stores, $store);
    }

    /**
     * <p>Removes a Store</p>
     * @param Store $store the <code>Store</code> to be removed
     * @return boolean indicating if the specified Store has been deleted or not
     */
    public function removeStore(Store $store)
    {
        if (in_array($store, $this->stores)) {
            return count(array_splice($this->stores, array_search($store, $this->stores), 1)) > 0 ? true : false;
        }

        return false;
    }

    /**
     * <p>Gets the node content from the specified version</p>
     * <p>It loops over the stores and get the node content from the first available store</p>
     * 
     * @param integer $nodeId The id of the node which retrieve the content from
     * @param integer $versionId The version id from a node to be retrieved or version number
     * @param integer $subversion The subversion number
     * 
     * @return string The content of the node for the specified version/subversion or false if an error occurred while retrieving the content
     */
    public function getContent($nodeId, $versionId, $subversion = null)
    {
        foreach ($this->stores as $store) {
            $content = $store->getContent($nodeId, $versionId, $subversion);
            if ($content !== false) {
                return $content;
            }
        }

        return $false;
    }

    /**
     * <p>Sets the node content for the specific version/subversion</p>
     * <p>It sets the content of the node in all of the stores but if one of the stores fails,
     * the whole process will fail and no rollback will be performed in the stores 
     * which the content was stored successfully</p>
     * 
     * 
     * @param string $content The content to be put
     * @param integer $nodeId The id of the node to set the content
     * @param integer $versionId The version id from a node to set the content
     * @param integer $subversion The subversion number
     */
    public function setContent($content, $nodeId, $versionId, $subversion)
    {
        foreach($this->stores as $store) {
            $result = $store->setContent($content, $nodeId, $versionId, $subversion);
            if($result === false) {
                return $result;
            }
        }
        
        return true;
    }

    /**
     * <p>Removes the node content from a specific version/version</p>
     * <p>It deletes the content of the node in all of the stores but if one of the stores fails,
     * the whole process will fail and no rollback will be performed in the stores 
     * which the content was deleted successfully</p>
     * 
     * @param integer $nodeId The id of the node which retrieve the content from
     * @param integer $versionId The version id from a node to be retrieved or version number
     * @param integer $subversion The subversion number
     */
    public function deleteContent($nodeId, $versionId, $subversion = null)
    {
        foreach($this->stores as $store) {
            $result = $store->deleteContent($nodeId, $versionId, $subversion);
            if($result === false) {
                return $result;
            }
        }
        
        return true;
    }
}

?>
