<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Logger;
use Exception;
use Ximdex\Utils\FsUtils;

class ConnectionLocal extends Connector implements IConnector
{	
	/**
	 * Connect to server
	 * 
	 * @access public
	 * @param host string
	 * @param port int
	 * @return boolean
	 */
	public function connect($host = null, $port = null)
	{    
		// Nothing to do here
		return true;
	}
	
	/**
	 * Disconnect from server
	 * 
	 * @access public
	 * @return boolean
	 */
	public function disconnect()
	{    
		// Nothing to do here
		return true;
	}
	
	/**
	 * Check the status of the connection
	 */
	public function isConnected()
	{
		return true;
	}
	
	/**
	 * Authenticate into server
	 * 
	 * @access public
	 * @param login string
	 * @param password string
	 * @return boolean
	 */
	public function login($username = 'anonymous', $password = 'john.doe@example.com')
	{
		// Nothing to do here
		return true;
	}
	
	/**
	 * Change directory in server
	 * 
	 * @access public
	 * @param dir string
	 * @return boolean false if folder no exist
	 */
	public function cd($dir)
	{
		if (!is_dir($dir)) {
			return false;
		}
		try {
			chdir($dir);
		} catch (Exception $e) {
			Logger::error($e->getMessage());
			return false;
		}
		$localFolder = $this->pwd();
		if ($localFolder == realpath($dir)) {
			return true;
		}
		return false;
	}
	
	/**
	 * Get the server folder
	 * 
	 * @access public
	 * @param dir string
	 * @return string
	 */
	public function pwd()
	{
		return getcwd();
	}
	
	/**
	 * Create a folder in the server
	 * 
	 * @access public
	 * @param dir string
	 * @param mode int
	 * @param recursive boolean
	 * @return boolean
	 */
	public function mkdir($dir, $mode = 0755, $recursive = false)
	{
		if (!is_dir($dir)) {
			try {
				return mkdir($dir, $mode, $recursive);
			} catch (Exception $e) {
				Logger::error($e->getMessage());
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Manage permissions on a file/folder
	 * 
	 * @access public
	 * @param target string
	 * @param mode string
	 * @param recursive boolean
	 * @return boolean
	 */
	public function chmod($target, $mode = 0755, $recursive = false)
	{
		if (!$recursive) {
			try {
				return chmod($target, $mode);
			} catch (Exception $e) {
				Logger::error($e->getMessage());
				return false;
			}
		}
		Logger::fatal("Not implemented yet LocalConnection::chmod with recursive = true");
		return false;
	}
	
	/**
	 * Rename a file in the server
	 * 
	 * @access public
	 * @param renameFrom string
	 * @param renameTo string
	 * @return boolean
	 */
	public function rename($renameFrom, $renameTo)
	{
		try {
			return rename($renameFrom, $renameTo);
		} catch (Exception $e) {
			Logger::error($e->getMessage());
		}
		return false;
	}
	
	/**
	 * Get the size of a file
	 * 
	 * @access public
	 * @param file string
	 * @return int
	 */
	public function size($file)
	{
		try {
			return stat($file);
		} catch (Exception $e) {
			Logger::error($e->getMessage());
		}
		return false;
	}
	
	/**
	 * Get the folder contents 
	 * 
	 * @access public
	 * @param dir string
	 * @param mode int
	 * @return mixed
	 */
	public function ls($dir, $mode = null)
	{
		$blackList = array('.', '..');
		$files = array();
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		        	if (!in_array($dir, $blackList)) {
		            	$files[] = $file;
		        	}
		        }
		        closedir($dh);
		    }
		}		
		return $files;
	}
	
	/**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::rm()
     */
    public function rm($path, int $id = null)
	{
		try {
			if (is_file($path)) {
				return unlink($path);
			} elseif (is_dir($path)) {
				return rmdir($path);
			}
		} catch (Exception $e) {
			Logger::error($e->getMessage());
		}
		return false;
	}
	
	/**
	 * Copies a file from server to local
	 * 
	 * @access public
	 * @param remoteFile string
	 * @param localFile string
	 * @param overwrite boolean
	 * @param mode
	 * @return boolean
	 */
	public function get($sourceFile, $targetFile, $mode = 0755)
	{
		return $this->copy($sourceFile, $targetFile, $mode);
	}
	
	/**
	 * Copies a file from local to server
	 * 
	 * @access public
	 * @param localFile string
	 * @param remoteFile string
	 * @param overwrite boolean
	 * @param mode
	 * @return boolean
	 */
	public function put($localFile, $targetFile, $mode = 0755)
	{
		return $this->copy($localFile, $targetFile, $mode);
	}
	
	private function copy($sourceFile, $targetFile, $mode)
	{
		$result = false;
		if (is_dir($sourceFile)) {
			Logger::error("COPY: El primer argumento no puede ser un directorio: $sourceFile");
		} else {
			$result = copy($sourceFile, $targetFile);
		}
		if ($result) {
			$result = $this->chmod($targetFile, $mode);
		}
		return $result;
	}
	
	/**
	 * Checks if the especified path is a folder
	 * 
	 * @access public
	 * @param path string
	 * @return boolean
	 */
	public function isDir($path)
	{
		return is_dir($path);
	}
	
    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::isFile()
     */
	public function isFile($path)
	{
		return is_file($path);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::dirIsEmpty()
	 */
	public function dirIsEmpty(string $path): bool
	{
	    return FsUtils::dir_is_empty($path);
	}
}
