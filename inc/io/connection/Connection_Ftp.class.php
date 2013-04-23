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

require_once (XIMDEX_ROOT_PATH . '/inc/io/connection/I_Connector.class.php');

class Connection_Ftp implements I_Connector {
	
	private $host;
	private $port;
	private $username;
	private $password;
	
	const TIMEOUT = 90;
	private $handler;
	
	private $defaultPort = 21;
	
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
			$port = $this->defaultPort;
		}
		XMD_Log::info("Connecting to {$host}:{$port}");
		if (!empty($host)) {
			$this->host = $host;
		}
		if (!empty($port)) {
			$this->port = $port;
		}
		try {
			$handler = ftp_connect($this->host, $this->port, Connection_Ftp::TIMEOUT);
		} catch (Exception $e) {
			XMD_Log::error($e->getMessage());
			return false;
		}
		
		if (!$handler) {
			$this->handler = false;
			XMD_Log::error("Couldt connect to {$host}:{$port} using FTP protocol");
			return false;
		}
		// Ftp in passive mode
		ftp_pasv($handler, true);
		//setting timeout to 300 seconds
		ftp_set_option($handle, FTP_TIMEOUT_SEC, 300);

		$this->handler = $handler;
		return true;
	}
	
	/**
	 * Disconnect from server
	 * 
	 * @access public
	 * @return boolean
	 */
	public function disconnect() {
		try {
			$result = @ftp_close($this->handler);
		} catch (Exception $e) {
			XMD_Log::error($e->getMessage());
			return false;
		}
		if (!$result) {
			XMD_Log::error("Disconnect from {$host}:{$port} failed");
			return false;
		}
		$this->handler = NULL;
		return true;
	}
	
	/**
	 * Check the status of the connection
	 *
	 */
	public function isConnected() {
		if (!$this->handler) {
			return false;
		}
		return ($this->pwd() !== false);
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
		if ($this->handler === NULL) { // false en el handler es error de conexión
			if (!$this->connect()) {
				return false;
			}
		}
		
		if (!empty($username)) {
			$this->username = $username;
		}
		
		if (!empty($password)) {
			$this->password = $password;
		}
		
		try {
			return @ftp_login($this->handler, $username, $password);
		} catch (Exception $e) {
			XMD_Log::error($e->getMessage());
			return false;
		}
		
		return true;
	}
	
	/**
	 * Change directory in server
	 * 
	 * @access public
	 * @param dir string
	 * @return boolean false if folder no exist
	 */
	public function cd($dir) {
		if (empty($dir)) {
			$dir = '/';
		}
		try {
			return @ftp_chdir($this->handler, $dir);
		} catch (Exception $e) {
			XMD_Log::error($e->getMessage());
			return false;
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
	public function pwd() {
		try {
			return @ftp_pwd($this->handler);
		} catch (Exception $e) {
			XMD_Log::error($e->getMessage());
			return false;
		}
		return false;
	}
	
	/**
	 * Create a folder in the server (Absolute routes only for now)
	 * 
	 * @access public
	 * @param dir string
	 * @param mode int
	 * @param recursive boolean
	 * @return boolean
	 */
	public function mkdir($dir, $mode = 0755, $recursive = false) {
		if (!$recursive) {
			return $this->_mkdir($dir, $mode);
		}
		$localPath = $this->pwd();
		
		$dirElements = explode('/', $dir);
		if (!(count($dirElements) > 0)) {
			XMD_Log::error('Invalid Path for FTP::mkdir ' . $dir);
			return false;
		}
		
		// Due to construction meanings the first element should be empty so will be discarded
		array_shift($dirElements);
		$localFolder = '';
		$result = true;
		foreach ($dirElements as $dirElement) {
			$localFolder .= '/' . $dirElement;
			if ($this->cd($localFolder)) {
				continue;
			}
			$result = $this->_mkdir($localFolder, $mode);
			if (!$result) {
				break;
			}
		}
		$this->cd($localPath);
		return $result;
	}
	
	/**
	 * Private mkdir function who supports the recursive mkdir
	 * this function only should create a folder if all the ancestors exists
	 *  
	 * @access private
	 * @param dir string
	 * @param mode int
	 * @return boolean
	 */
	
	private function _mkdir($dir, $mode = 0755) {
		try {
			$result = (bool)ftp_mkdir($this->handler, $dir);
		} catch (Exception $e) {
			XMD_Log::error($e->getMessage());
			return false;
		}
		if ($result) {
//			$result = $this->chmod($dir, $mode); 
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
		if (!$recursive) {
			try {
				$var =  ftp_site($this->handler, "CHMOD " . $mode . " " . $target);
			} catch (Exception $e) {
				XMD_Log::error($e->getMessage());
				return false;
			}
		}
		XMD_Log::fatal("Not implemented yet FTPConnection::chmod with recursive = true");
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
	public function rename($renameFrom, $renameTo) {
		try {
                        $renameFrom=str_replace('//','/',$renameFrom);
                        $renameTo=str_replace('//','/',$renameTo);
                        if($this->isFile($renameTo)){
                                @ftp_delete($this->handler, $renameTo);
                        }
                        return @ftp_rename($this->handler, $renameFrom, $renameTo);
                } catch (Exception $e) {
                        XMD_Log::error($e->getMessage());
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
	public function size($file) {
		try {
			return @ftp_size($this->handler, $file);
		} catch (Exception $e) {
			XMD_Log::error($e->getMessage());
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
	public function ls($dir, $mode = NULL) {
		return ftp_nlist($this->handler, $dir);
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
		try {
			if ($this->isDir($path)) {
				return @ftp_rmdir($this->handler, $path);
			} else {
				return @ftp_delete($this->handler, $path);
			}
		} catch (Exception $e) {
			XMD_Log::error($e->getMessage());
		}
		return false;
	}
	
	/**
	 * Checks if the especified path is a folder
	 * 
	 * @access private
	 * @param path string
	 * @return boolean
	 */
	public function isDir($path) {
		$isDir = false;
		$pwd = $this->pwd();
		
		if ($this->cd($path)) {
			if ($this->pwd() == $path) {
				$this->cd($pwd);
				$isDir = true;
			}
		}
		$this->cd($pwd);
 		return $isDir;
	}
	
	/**
	 * Checks if the especified path is a file
	 * 
	 * @access private
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
			$folder=str_replace('//','/',$folder);
                        $file=str_replace('//','/',$file);
			if ($this->pwd() == $folder || $this->pwd() . '/' == $folder) {
				$fileList = @ftp_nlist($this->handler, $folder);
				$isFile = in_array($folder.$file, $fileList);
			}

		}
		$this->cd($pwd);
		return $isFile;
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
	public function get($sourceFile, $targetFile, $mode = FTP_BINARY) {
		try {
			return ftp_get($this->handler, $targetFile, $sourceFile, $mode);
		} catch (Exception $e) {
			XMD_Log::error($e->getMessage());
		}
		return false;
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
	public function put($localFile, $targetFile, $mode = FTP_BINARY) {
		try {
			return @ftp_put($this->handler, $targetFile, $localFile, $mode);
		} catch (Exception $e) {
			XMD_Log::error($e->getMessage());
		}
		return false;
	}
}
?>
