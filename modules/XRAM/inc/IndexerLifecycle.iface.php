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

/**
 * <p>Indexer Lifecycle interface</p>
 * <p>Represents the lifecycle of an indexing process</p>
 */
interface IndexerLifecycle
{
    /**
     * <p>Function called before the indexing is performed</p>
     * <p>It allows the developers to modify the document to be indexed</p>
     * 
     * @param $document The document to be indexed
     */
    public function beforeIndex($document);
    
    /**
     * <p>Function called after the indexing process</p>
     * <p>It allows the developers to perform some action after the indexing process</p>
     * 
     * @param $document The document which has been indexed
     */
    public function afterIndex($document);
    
    /**
     * <p>Function called before a document is going to be retrieved</p>
     * <p>It allows the developers to perform some action before retrieving a document</p>
     */
    public function beforeRetrieve();
    
    /**
     * <p>Function called after the retrieving process</p>
     * <p>It allows the developers to perform some action after retrieving a document</p>
     * 
     * @param $document The document which has been retrieved
     * @return the document retrieved after some process has been applied to it
     */
    public function afterRetrieve($document);
    
    /**
     * <p>Function called before a document is going to be deleted</p>
     * <p>It allows the developers to perform some action before deleting a document</p>
     */
    public function beforeDelete($id);
    
    /**
     * <p>Function called after the deletion process</p>
     * <p>It allows the developers to perform some action after deleting a document</p>
     * 
     * @param $document The document which has been deleted
     */
    public function afterDelete($id);
    
}

?>
