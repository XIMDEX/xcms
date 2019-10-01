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

use Ximdex\Logger;
use Exception;
use Ximdex\Utils\FsUtils;

class ConnectionLocal extends Connector
{	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::connect()
	 */
    public function connect(string $host = null, int $port = null) : bool
	{ 
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::disconnect()
	 */
	public function disconnect() : bool
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::isConnected()
	 */
	public function isConnected() : bool
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::login()
	 */
	public function login(string $username = null, string $password = null) : bool
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::cd()
	 */
	public function cd(string $dir) : bool
	{
		if (! is_dir($dir)) {
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
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::pwd()
	 */
	public function pwd()
	{
		return getcwd();
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::mkdir()
	 */
	public function mkdir(string $dir, int $mode = 0755, bool $recursive = false) : bool
	{
		if (! is_dir($dir)) {
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
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::chmod()
	 */
	public function chmod(string $target, int $mode = 0755, bool $recursive = false) : bool
	{
		if (! $recursive) {
			try {
				return chmod($target, $mode);
			} catch (Exception $e) {
				Logger::error($e->getMessage());
				return false;
			}
		}
		Logger::fatal('Not implemented yet LocalConnection::chmod with recursive = true');
		return false;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::rename()
	 */
	public function rename(string $renameFrom, string $renameTo) : bool
	{
		try {
			return rename($renameFrom, $renameTo);
		} catch (Exception $e) {
			Logger::error($e->getMessage());
			return false;
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::size()
	 */
	public function size(string $file)
	{
		try {
			return stat($file);
		} catch (Exception $e) {
			Logger::error($e->getMessage());
			return false;
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::ls()
	 */
	public function ls(string $dir, int $mode = null) : array
	{
		$blackList = array('.', '..');
		$files = array();
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		        	if (! in_array($dir, $blackList)) {
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
	public function rm(string $path, int $id = null) : bool
	{
		try {
			if (is_file($path)) {
				return unlink($path);
			} elseif (is_dir($path)) {
				return rmdir($path);
			}
			
		} catch (Exception $e) {
			Logger::error($e->getMessage());
			return false;
		}
		Logger::error('Path ' . $path . ' is not a file or directory');
		return false;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::get()
	 */
	public function get(string $sourceFile, string $targetFile, int $mode = 0755): bool
	{
		return $this->copy($sourceFile, $targetFile, $mode);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::put()
	 */
	public function put(string $localFile, string $targetFile, int $mode = 0755): bool
	{
		return $this->copy($localFile, $targetFile, $mode);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::isDir()
	 */
	public function isDir(string $path) : bool
	{
		return is_dir($path);
	}
	
    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::isFile()
     */
	public function isFile(string $path): bool
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
	
	private function copy(string $sourceFile, string $targetFile, int $mode) : bool
	{
	    if (is_dir($sourceFile)) {
	        Logger::error('COPY: The first argument can not be a directory: ' . $sourceFile);
	        return false;
	    }
	    if ($result = copy($sourceFile, $targetFile)) {
	        $result = $this->chmod($targetFile, $mode);
	    }
	    return $result;
	}
}
