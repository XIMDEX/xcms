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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
}

require_once (XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
require_once (XIMDEX_ROOT_PATH.Extensions::PHPSECLIB . '/Net/SFTP.php');
require_once (XIMDEX_ROOT_PATH . '/inc/io/connection/I_Connector.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/io/connection/Connection_Local.class.php');

class Connection_Ssh implements I_Connector {
	// Connection location and credentials
	private $host;
	private $port;
	private $username;
	private $password;
	
	private $netSFTP = NULL;


	/**
	 * Connect to server
	 *
	 * @access public
	 * @param host string
	 * @param port int
	 * @return boolean
	 */
	public function connect($host = NULL, $port = NULL) {
	

		if (empty($port)) {
			$port = 22;
		}
		if (!empty($host)) {
			$this->host = $host;
		}
		if (!empty($port)) {
			$this->port = $port;
		}
		if (empty($host) && empty($port)) {
			return false;
		}
		$this->netSFTP = new Net_SFTP($host, $port);



		return true;
	}
	
	/**
	 * Disconnect from server
	 *
	 * @access public
	 * @return boolean
	 */
	public function disconnect() {
		return (boolean) $this->netSFTP->disconnect();
	}
	
	/**
	 * Check the status of the connection
	 *
	 */
	public function isConnected() {
		if (empty($this->netSFTP)) {
			return false;
		}
		$pwd = $this->pwd();
		return !empty($pwd);
	}
	
	/**
	 * Authenticate into server
	 *
	 * @access public
	 * @param login string
	 * @param password string
	 * @return boolean
	 */
	public function login($username = 'anonymous', $password = 'john.doe@example.com') {
		if ($username != 'anonymous') {
			$this->username = $username;
		}
		if ($password != 'john.doe@example.com') {
			$this->password = $password;
		}
		if (!$this->netSFTP) {
			return false;
		}
		return $this->netSFTP->login($username, $password);
	}
	
	/**
	 * Change directory in server
	 *
	 * @access public
	 * @param dir string
	 * @return boolean false if folder no exist
	 */
	public function cd($dir) {
		return $this->netSFTP->chdir($dir);
	}
	
	/**
	 * Get the server folder
	 *
	 * @access public
	 * @param dir string
	 * @return string
	 */
	public function pwd() {
		return $this->netSFTP->pwd();
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
	public function mkdir($dir, $mode = 0755, $recursive = false) {
		if ($this->folderExists($dir)) {
			return true;
		}
		
		$folderComponents = explode('/', $dir);
		if (empty($folderComponents[count($folderComponents) - 1])) {
			unset($folderComponents[count($folderComponents) - 1]);
		}
		unset($folderComponents[count($folderComponents) - 1]);
		$parentFolder = implode('/', $folderComponents);
		if (!$this->folderExists($parentFolder)) {
			if (!$recursive) {
				XMD_Log::warning('Connection_Ssh::mkdir folder not found and recursive flag not set');
				return false;
			}
			$isParentCreated = $this->mkdir($parentFolder, $mode, true);
			
		} else {
			$isParentCreated = true;
		}
		
		if ($isParentCreated) {
			return $this->_mkdir($dir, $mode);
		}
		
		return false;
	}
	
	private function folderExists($dir) {
		$localPath = $this->pwd();
		
		$result = $this->cd($dir);
		$this->cd($localPath);
		
		return $result;
	}
	
	private function _mkdir($dir, $mode) {
		$result = $this->netSFTP->mkdir($dir);
		if ($result) {
			$result = $this->netSFTP->chmod($mode, $dir);
		}
		return $result;
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
	public function chmod($target, $mode = 0755, $recursive = false) {
		if ($recursive) {
			XMD_Log::fatal('Not implemented yet Connection_Ssh::chmod with recursive true');
		}
		
		return $this->netSFTP->chmod($mode, $filename);
	}
	
	/**
	 * Rename a file in the server
	 *
	 * @access public
	 * @param renameFrom string
	 * @param renameTo string
	 * @return boolean
	 */
	public function rename($renameFrom, $renameTo) {
		if (!($result = $this->netSFTP->rename($renameFrom, $renameTo))) {
			$this->netSFTP->delete($renameTo);
			return $this->netSFTP->rename($renameFrom, $renameTo);
		}
		return $result;
	}
	
	/**
	 * Get the size of a file
	 *
	 * @access public
	 * @param file string
	 * @return int
	 */
	public function size($file) {
		XMD_Log::fatal('Not implemented yet Connection_Ssh::size');
	}
	
	/**
	 * Get the folder contents
	 *
	 * @access public
	 * @param dir string
	 * @param mode int
	 * @return mixed
	 */
	public function ls($dir, $mode = NULL) {
		return $this->netSFTP->nlist($dir);
	}
	
	/**
	 * Removes a file from server
	 *
	 * @access public
	 * @param path string
	 * @param recursive boolean
	 * @param filesOnly boolean
	 * @return boolean
	 */
	public function rm($path) {
		$localPath = $this->pwd();
		
		if ($this->cd($path)) {
			$this->cd($localPath);
			return $this->netSFTP->rmdir($path);
		}
		return $this->netSFTP->delete($path);
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
	public function get($sourceFile, $targetFile, $mode = 0755) {
		return $this->netSFTP->get($sourceFile, $targetFile);
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
	public function put($localFile, $targetFile, $mode = 0755) {
		return $this->netSFTP->put($targetFile, $localFile, NET_SFTP_LOCAL_FILE);
	}
	/**
	 * Checks if the especified path is a folder
	 * 
	 * @access public
	 * @param path string
	 * @return boolean
	 */
	public function isDir($path) {
		$result = false;
		$localPath = $this->pwd();
		if ($this->cd($path)) {
			 $result = true;
		}
		$this->cd($localPath);
		return $result;
	}
	
	/**
	 * Checks if the especified path is a file
	 * 
	 * @access public 
	 * @param path string
	 * @return boolean
	 */
	public function isFile($path) {
                if ($this->isDir($path)) {
                        return false;
                }

                $isFile = false;
                $pwd = $this->pwd();
                $matches = array();
                preg_match('/(.*\/)([^\/]+)$/', $path, $matches);

                if (count($matches) == 3) {
                        $folder = $matches[1];
                        $file = $matches[2];
                } else {
                        $file = $path;
                }
                if (empty($folder)) $folder = '/';
                if ($this->cd($folder)) {
                        if ($this->pwd() == $folder || $this->pwd() . '/' == $folder) {
                                $fileList = $this->ls($folder);
                                $isFile = (in_array($file, $fileList));
                        }

                }
                $this->cd($pwd);
                return $isFile;
	
	}
}
?>
