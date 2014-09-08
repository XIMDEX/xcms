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



/**
* @interface Backend_XVFS_interface
* @brief Interface for the xvfs backend classes.
*/

interface Backend_XVFS_interface {
	/**
	 * Checks if the given path is a folder
	 * @param $bpath
	 * @return boolean
	 */
	function isDir($bpath);
	/**
	 * Checks if the given path is a file
	 * @param $bpath
	 * @return boolean
	 */ 
	function isFile($bpath);
	/**
	 * Checks if there is a file or folder in the given path
	 * @param $bpath
	 * @return boolean
	 */
	function exists($bpath);
	/**
	 * Checks if current user can read given file
	 * @param $bpath
	 * @return boolean
	 */
	function isReadable($bpath);
	/**
	 * Checks if current user can write given path
	 * @param $bpath
	 * @return boolean
	 */
	function isWritable($bpath);
	/**
	 * Read contents from given folder
	 * @param $bpath
	 * @return entity??
	 */
	function & read($bpath);
	/**
	 * Get contents from a given file
	 * @param $bpath
	 * @return string
	 */
	function & getContent($bpath);
	/**
	 * Set contents into a given file
	 * @param $bpath
	 * @param $content
	 * @return boolean
	 */
	function setContent($bpath, $content);
	/**
	 * Getter for entity descriptors
	 * @param $bpath
	 * @return descriptor
	 */
	function getDescriptor($bpath);
	/**
	 * Getter for entity mimetype
	 * @param $bpath
	 * @return mimetype
	 */
	function getMIME($bpath);
	/**
	 * Creates a folder in the given folder
	 * @param $bpath
	 * @return boolean
	 */
	function mkdir($bpath);
	/**
	 * Create a file with an optional content
	 * @param $bpath
	 * @param $content
	 * @return boolean
	 */
	function append($bpath, $content=null);
	/**
	 * Update a file with a given content
	 * @param $bpath
	 * @param $content
	 * @return boolean
	 */
	function update($bpath, $content=null);
	/**
	 * Deletes a given file/folder
	 * @param $bpath
	 * @return boolean
	 */
	function delete($bpath);
	/**
	 * Rename a file/folder to a new given name
	 * @param $bpath
	 * @param $newName
	 * @return boolean
	 */
	function rename($bpath, $newName);
	/**
	 * Move a file/folder to a new given name
	 * @param $source
	 * @param $target
	 * @return boolean
	 */
	function move($source, $target);
	/**
	 * Copy a source file/folder to a new given target
	 * @param $source
	 * @param $target
	 * @return boolean
	 */
	function copy($source, $target);
}

/**
* @interface Backend_XVFS_interface_searcheable
* @brief Interface for implement the search in a XVFS backend.
*/

interface Backend_XVFS_interface_searcheable {
	/**
	 * Search nodes identified for a bundle of conditions in the backend
	 * @param string query
	 * @return unknown_type
	 */
	function search($query);
	
	/**
	 * Count nodes identified for a bundle of conditions in the backend
	 * @param $conditions
	 * @return unknown_type
	 */
	function count($query);
}
?>