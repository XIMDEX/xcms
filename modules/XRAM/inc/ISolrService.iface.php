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
    define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));
}
interface ISolrService
{
    /**
     *
     * <p>Indexes a node version in Solr identified by the version id</p>
     *
	 *
     * @param string|int $idVersion The id of the node version to be indexed
     * @param boolean $commitNode Boolean indicating if a commit needs to be performed after the indexing process
     *
     */
    public function indexNode($idVersion, $commitNode = true);
    
    /**
     * <p>Retrieves an specific version of a node</p>
     * 
     * @param int $idVersion The version of the node to be retrieved
     * 
     * return array The retrieved node or null if an error ocurred
     */
    public function retrieveNode($idVersion);
    
    /*
     * <p>Delete a node version from Solr</p>
     * 
     * @param int $idVersion The node version id to be deleted
     */
    public function deleteNode($idVersion);
}

?>
