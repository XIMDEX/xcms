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

class ConnectionFtp extends Connector implements IConnector
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $handler;
    private $defaultPort = 21;
    const TIMEOUT = 90;

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::connect()
     */
    public function connect(string $host = null, int $port = null) : bool
    {
        if (empty($port)) {
            $port = $this->defaultPort;
        }
        Logger::info("Connecting to FTP server {$host}:{$port}");
        if (! empty($host)) {
            $this->host = $host;
        }
        if (! empty($port)) {
            $this->port = $port;
        }
        try {
            $handler = ftp_connect($this->host, $this->port, ConnectionFtp::TIMEOUT);
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            return false;
        }
        if (! $handler) {
            $this->handler = false;
            Logger::error("Could't connect to server {$host}:{$port} using FTP protocol");
            return false;
        }

        // Setting timeout to 300 seconds
        ftp_set_option($handler, FTP_TIMEOUT_SEC, 300);
        Logger::info("Connected to FTP server {$host}:{$port} correctly");
        $this->handler = $handler;
        return true;
    }

   /**
    * {@inheritDoc}
    * @see \Ximdex\IO\Connection\IConnector::disconnect()
    */
    public function disconnect() : bool
    {
        try {
            $result = ftp_close($this->handler);
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            return false;
        }
        if (! $result) {
            Logger::error("Disconnect from FTP server {$this->host}:{$this->port} failed");
            return false;
        }
        $this->handler = null;
        Logger::info("Disconnect from FTP server {$this->host}:{$this->port} correctly");
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::isConnected()
     */
    public function isConnected() : bool
    {
        if (! $this->handler) {
            return false;
        }
        return ($this->pwd() !== false);
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::login()
     */
    public function login(string $username = null, string $password = null) : bool
    {
        if ($this->handler === null) { // False in handler means connection error
            if (! $this->connect()) {
                return false;
            }
        }
        if (! empty($username)) {
            $this->username = $username;
        }
        if (! empty($password)) {
            $this->password = $password;
        }
        try {
            if (! ftp_login($this->handler, $this->username, $this->password)) {
                Logger::error("Could't log into FTP server {$this->host}:{$this->port} with the given user and password");
                $this->handler = null;
                return false;
            }
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            $this->handler = null;
            return false;
        }
        Logger::info('Login with user ' . $this->username . ' success');

        // Ftp in passive mode (always after a correct login into the FTP server
        if (! ftp_pasv($this->handler, true))
        {
            Logger::error("Could't set to passive mode in {$this->host}:{$this->port} using FTP protocol");
            $this->handler = null;
            return false;
        }
        Logger::info('Set to passive mode success');
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::cd()
     */
    public function cd(string $dir) : bool
    {
        if (empty($dir)) {
            $dir = '/';
        }
        try {
            return (bool) ftp_chdir($this->handler, $dir);
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            return false;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::pwd()
     */
    public function pwd()
    {
        try {
            return ftp_pwd($this->handler);
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            return false;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::mkdir()
     */
    public function mkdir(string $dir, int $mode = 0755, bool $recursive = false) : bool
    {
        if (! $recursive) {
            return $this->_mkdir($dir, $mode);
        }
        $localPath = $this->pwd();
        $dirElements = explode('/', $dir);
        if (! count($dirElements)) {
            Logger::error('Invalid Path for FTP::mkdir ' . $dir);
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
            if (! $result) {
                break;
            }
        }
        $this->cd($localPath);
        return $result;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::chmod()
     */
    public function chmod(string $target, int $mode = 0755, bool $recursive = false) : bool
    {
        if (! $recursive) {
            try {
                return (bool) ftp_site($this->handler, 'CHMOD ' . $mode . ' ' . $target);
            } catch (Exception $e) {
                Logger::error($e->getMessage());
                return false;
            }
        }
        Logger::fatal('Not implemented yet FTPConnection::chmod with recursive = true');
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::rename()
     */
    public function rename(string $renameFrom, string $renameTo) : bool
    {
        try {
            $renameFrom = str_replace('//', '/', $renameFrom);
            $renameTo = str_replace('//', '/', $renameTo);
            if ($this->isFile($renameTo)){
                ftp_delete($this->handler, $renameTo);
            }
            return (bool) ftp_rename($this->handler, $renameFrom, $renameTo);
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
            return ftp_size($this->handler, $file);
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
        return (bool) ftp_nlist($this->handler, $dir);
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::rm()
     */
    public function rm(string $path, int $id = null) : bool
    {
        try {
            if ($this->isDir($path)) {
                return (bool) ftp_rmdir($this->handler, $path);
            } else {
                return (bool) ftp_delete($this->handler, $path);
            }
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            return false;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::isDir()
     */
    public function isDir(string $path) : bool
    {
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
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::isFile()
     */
    public function isFile(string $path): bool
    {
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
        if (empty($folder)) {
            $folder = '/';
        }
        if ($this->cd($folder)) {
            $folder = str_replace('//','/',$folder);
            $file = str_replace('//','/',$file);
            if ($this->pwd() == $folder || $this->pwd() . '/' == $folder) {
                $fileList = ftp_nlist($this->handler, $folder);
                $isFile = in_array($folder . $file, $fileList);
            }
        }
        $this->cd($pwd);
        return $isFile;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::get()
     */
    public function get(string $sourceFile, string $targetFile, int $mode = FTP_BINARY): bool
    {
        try {
            return (bool) ftp_get($this->handler, $targetFile, $sourceFile, $mode);
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            return false;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::put()
     */
    public function put(string $localFile, string $targetFile, int $mode = FTP_BINARY): bool
    {
        try {
            return (bool) ftp_put($this->handler, $targetFile, $localFile, $mode);
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            return false;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::dirIsEmpty()
     */
    public function dirIsEmpty(string $path): bool
    {
        $content = (bool) ftp_exec($this->handler, 'ls -a ' . $path . ' | head -n 1  | wc -l');
        if ($content === false) {
            Logger::error('Cannot obtain the files count in order to know is a folder is empty: ' . $path);
            return false;
        }
        return ($content - 2) == 0;
    }
    
    /**
     * Private mkdir function who supports the recursive mkdir
     * this function only should create a folder if all the ancestors exists
     * 
     * @param string $dir
     * @param int $mode
     * @return bool
     */
    private function _mkdir(string $dir, int $mode = 0755) : bool
    {
        try {
            $result = (bool) ftp_mkdir($this->handler, $dir);
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            return false;
        }
        if ($result) {
            // $result = $this->chmod($dir, $mode);
        }
        return $result;
    }
}
