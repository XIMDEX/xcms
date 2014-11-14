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

/**
 * <p>Store interface</p>
 * <p>Represents a information store. Mainly used to store and retrieves nodes </p>
 * 
 */
interface Store
{

    /**
     * <p>Gets the node content from the specific version/subversion</p>
     * 
     * @param integer $nodeId The id of the node which retrieve the content from
     * @param integer $versionId The version id from a node to be retrieved or version number
     * @param integer $subversion The subversion number
     */
    public function getContent($nodeId, $versionId, $subversion = null);
    
    /**
     * <p>Sets the node content for the specific version/subversion</p>
     * 
     * @param string $content The content to be put
     * @param integer $nodeId The id of the node to set the content
     * @param integer $versionId The version id from a node to set the content
     * @param integer $subversion The subversion number
     */
    public function setContent($content, $nodeId, $versionId, $subversion);
    
    /**
     * <p>Removes the node content from a specific version/version</p>
     * 
     * @param integer $nodeId The id of the node which retrieve the content from
     * @param integer $versionId The version id from a node to be retrieved or version number
     * @param integer $subversion The subversion number
     */
    public function deleteContent($nodeId, $versionId, $subversion = null);
}

?>
