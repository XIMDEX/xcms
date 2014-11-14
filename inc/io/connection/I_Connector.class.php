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
 * 
 * @author jmgomez
 *
 */
interface I_Connector {
	
	/**
	 * Connect to server
	 * 
	 * @access public
	 * @param host string
	 * @param port int
	 * @return boolean
	 */
	public function connect($host = NULL, $port = NULL);
	
	/**
	 * Disconnect from server
	 * 
	 * @access public
	 * @return boolean
	 */
	public function disconnect();
	
	/**
	 * Check the status of the connection
	 *
	 */
	public function isConnected();
	
	/**
	 * Authenticate into server
	 * 
	 * @access public
	 * @param login string
	 * @param password string
	 * @return boolean
	 */
	public function login($username = 'anonymous', $password = 'john.doe@example.com');
	
	/**
	 * Change directory in server
	 * 
	 * @access public
	 * @param dir string
	 * @return boolean
	 */
	public function cd($dir);
	
	/**
	 * Get the server folder
	 * 
	 * @access public
	 * @param dir string
	 * @return string
	 */
	public function pwd();
	
	/**
	 * Create a folder in the server
	 * 
	 * @access public
	 * @param dir string
	 * @param mode int
	 * @param recursive boolean
	 * @return boolean
	 */
	public function mkdir($dir, $mode = 0755, $recursive = false);
	
	/**
	 * Manage permissions on a file/folder
	 * 
	 * @access public
	 * @param target string
	 * @param mode string
	 * @param recursive boolean
	 * @return boolean
	 */
	public function chmod($target, $mode = 0755, $recursive = false);
	
	/**
	 * Rename a file in the server
	 * 
	 * @access public
	 * @param renameFrom string
	 * @param renameTo string
	 * @return boolean
	 */
	public function rename($renameFrom, $renameTo);
	
	/**
	 * Get the size of a given file
	 * 
	 * @access public
	 * @param file string
	 * @return int
	 */
	public function size($file);
	
	/**
	 * Get the given folder contents 
	 * 
	 * @access public
	 * @param dir string
	 * @param mode int
	 * @return mixed
	 */
	public function ls($dir, $mode = NULL);
	
	/**
	 * Removes a file from server
	 * 
	 * @access public
	 * @param path string
	 * @param recursive boolean
	 * @param filesOnly boolean
	 * @return boolean
	 */
	public function rm($path);
	
	/**
	 * Copies a given file from server to local
	 * 
	 * @access public
	 * @param remoteFile string
	 * @param localFile string
	 * @param overwrite boolean
	 * @param mode  TODO Not implemented yet
	 * @return boolean
	 */
	public function get($sourceFile, $targetFile, $mode = 0755);
	
	/**
	 * Copies a given file from local to server
	 * 
	 * @access public
	 * @param localFile string
	 * @param remoteFile string
	 * @param overwrite boolean
	 * @param mode  TODO Not implemented yet
	 * @return boolean
	 */
	public function put($localFile, $targetFile, $mode = 0755);
	
	/**
	 * Checks if the especified path is a folder
	 * 
	 * @access public
	 * @param path string
	 * @return boolean
	 */
	public function isDir($path);
	
	/**
	 * Checks if the especified path is a file
	 * 
	 * @access public 
	 * @param path string
	 * @return boolean
	 */
	public function isFile($path);
	
}

?>