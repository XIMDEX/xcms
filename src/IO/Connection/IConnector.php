<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\IO\Connection;

interface IConnector
{
	/**
	 * Connect to server
	 * 
	 * @param string $host
	 * @param int $port
	 * @return bool
	 */
	public function connect(string $host = null, int $port = null) : bool;
	
	/**
	 * Disconnect from server
	 * 
	 * @return bool
	 */
	public function disconnect() : bool;
	
	/**
	 * Check the status of the connection
	 * 
	 * @return bool
	 */
	public function isConnected() : bool;
	
	/**
	 * Authenticate into server
	 * 
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	public function login(string $username = null, string $password = null) : bool;
	
	/**
	 * Change directory in server
	 * 
	 * @param string $dir
	 * @return bool
	 */
	public function cd(string $dir) : bool;
	
	/**
	 * Get the server folder
	 * 
	 * @return string|bool
	 */
	public function pwd();
	
	/**
	 * Create a folder in the server
	 * 
	 * @param string $dir
	 * @param int $mode
	 * @param bool $recursive
	 * @return bool
	 */
	public function mkdir(string $dir, int $mode = 0755, bool $recursive = false) : bool;
	
	/**
	 * Manage permissions on a file/folder
	 * 
	 * @param string $target
	 * @param int $mode
	 * @param bool $recursive
	 * @return bool
	 */
	public function chmod(string $target, int $mode = 0755, bool $recursive = false) : bool;
	
	/**
	 * Rename a file in the server
	 * 
	 * @param string $renameFrom
	 * @param string $renameTo
	 * @return bool
	 */
	public function rename(string $renameFrom, string $renameTo) : bool;
	
	/**
	 * Get the size of a given file
	 * 
	 * @param string $file
	 * @return bool|int
	 */
	public function size(string $file);
	
	/**
	 * Get the given folder contents
	 * 
	 * @param string $dir
	 * @param int $mode
	 * @return array
	 */
	public function ls(string $dir, int $mode = null) : array;
	
	/**
	 * Removes a file from server
	 * 
	 * @param string $path
	 * @param int $id
	 * @return bool
	 */
	public function rm(string $path, int $id = null) : bool;
	
	/**
	 * Copies a given file from server to local
	 * 
	 * @param string $sourceFile
	 * @param string $targetFile
	 * @param int $mode
	 * @return bool
	 */
	public function get(string $sourceFile, string $targetFile, int $mode = 0755): bool;
	
	/**
	 * Copies a given file from local to server
	 * 
	 * @param string $localFile
	 * @param string $targetFile
	 * @param int $mode
	 * @return bool
	 */
	public function put(string $localFile, string $targetFile, int $mode = 0755): bool;
	
	/**
	 * Checks if the especified path is a folder
	 * 
	 * @param string $path
	 * @return bool
	 */
	public function isDir(string $path) : bool;
	
	/**
	 * Checks if the especified path is a file and is exists
	 * 
	 * @param string $path
	 * @return bool
	 */
	public function isFile(string $path): bool;
	
	/**
	 * Check if the specified folder path is empty
	 * 
	 * @param string $path
	 * @return bool
	 */
	public function dirIsEmpty(string $path): bool;
}
