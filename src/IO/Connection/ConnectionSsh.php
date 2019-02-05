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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\IO\Connection;

use Ximdex\Logger;
use phpseclib\Net\SFTP;

class ConnectionSsh extends Connector implements IConnector
{
    // Connection location and credentials
    private $host;
    private $port;
    private $username;
    private $password;
    private $netSFTP = null;

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::connect()
     */
    public function connect(string $host = null, int $port = null) : bool
    {
        if (empty($port)) {
            $port = 22;
        }
        if (! empty($host)) {
            $this->host = $host;
        }
        if (! empty($port)) {
            $this->port = $port;
        }
        if (empty($host) && empty($port)) {
            return false;
        }
        $this->netSFTP = new SFTP($host, $port);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::disconnect()
     */
    public function disconnect() : bool
    {
        return (bool) $this->netSFTP->disconnect();
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::isConnected()
     */
    public function isConnected() : bool
    {
        if (empty($this->netSFTP)) {
            return false;
        }
        $pwd = $this->pwd();
        return ! empty($pwd);
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::pwd()
     */
    public function pwd()
    {
        Logger::debug('Call to pwd');
        $res = $this->netSFTP->pwd();
        Logger::debug('Call to pwd returns: ' . $res);
        return $res;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::login()
     */
    public function login(string $username = null, string $password = null) : bool
    {
        if ($username) {
            $this->username = $username;
        }
        if ($password) {
            $this->password = $password;
        }
        if (! $this->netSFTP) {
            return false;
        }
        return (bool) $this->netSFTP->login($username, $password);
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::mkdir()
     */
    public function mkdir(string $dir, int $mode = 0755, bool $recursive = false) : bool
    {
        if ($this->folderExists($dir)) {
            return true;
        }
        $folderComponents = explode('/', $dir);
        if (empty($folderComponents[count($folderComponents) - 1])) {
            unset($folderComponents[count($folderComponents) - 1]);
        }
        unset($folderComponents[count($folderComponents) - 1]);
        $parentFolder = implode('/', $folderComponents);
        if (! $this->folderExists($parentFolder)) {
            if (! $recursive) {
                Logger::warning('Connection_Ssh::mkdir folder not found and recursive flag not set');
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

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::cd()
     */
    public function cd(string $dir) : bool
    {
        Logger::debug('Call to cd: ' . $dir);
        $res = $this->netSFTP->chdir($dir);
        Logger::debug('Call to cd returns: ' . $res);
        return (bool) $res;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::chmod()
     */
    public function chmod(string $target, int $mode = 0755, bool $recursive = false) : bool
    {
        if ($recursive) {
            Logger::fatal('Not implemented yet Connection_Ssh::chmod with recursive true');
        }
        return (bool) $this->netSFTP->chmod($mode, $target);
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::rename()
     */
    public function rename(string $renameFrom, string $renameTo) : bool
    {
        if ($this->netSFTP->stat($renameFrom)) {
            if (! $result = $this->netSFTP->rename($renameFrom, $renameTo)) {
                if ($this->netSFTP->stat($renameFrom) && $this->netSFTP->stat($renameTo)) {
                    $this->netSFTP->delete($renameTo);
                }
                $result = $this->netSFTP->rename( $renameFrom, $renameTo );
            }
        } else {
            return true;
        }
        return (bool) $result;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::size()
     */
    public function size(string $file)
    {
        return $this->netSFTP->filesize($file);
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::rm()
     */
    public function rm(string $path, int $id = null) : bool
    {
        $localPath = $this->pwd();
        if ($this->cd($path)) {
            $this->cd($localPath);
            return (bool) $this->netSFTP->rmdir($path);
        }
        return (bool) $this->netSFTP->delete($path);
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::get()
     */
    public function get(string $sourceFile, string $targetFile, int $mode = 0755): bool
    {
        return (bool) $this->netSFTP->get($sourceFile, $targetFile);
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::put()
     */
    public function put(string $localFile, string $targetFile, int $mode = 0755): bool
    {
        $size = filesize($localFile);
        if ($size === false) {
            return false;
        }
        return (bool) $this->netSFTP->put($targetFile, $localFile, SFTP::SOURCE_LOCAL_FILE, -1, -1, 
            function($sent) use ($targetFile, $size) {
                $sent = round($sent * 100 / $size);
                $size = round($size / 1024);
                Logger::debug("Uploading file {$targetFile} progress: {$sent}% from {$size} Kbytes");
            }
        );
    }

   /**
    * {@inheritDoc}
    * @see \Ximdex\IO\Connection\IConnector::isFile()
    */
    public function isFile(string $path): bool
    {
        if ($this->isDir($path)) {
            Logger::error('Resouce ' . $path . ' is a folder, not a file');
            return false;
        }
        $isFile = false;
        $matches = array();
        preg_match('/(.*\/)([^\/]+)$/', $path, $matches);
        Logger::debug('isFile matches: ' . print_r($matches, true), false, 'magenta');
        if (count($matches) == 3) {
            $folder = $matches[1];
            $file = $matches[2];
        } else {
            $file = $path;
        }
        if (empty($folder)) $folder = '/';
        if ($this->cd($folder)) {
            if (rtrim($this->pwd(), '/') == rtrim($folder, '/')) {
                $isFile = (bool) $this->netSFTP->file_exists($file);
            }
        }
        Logger::debug($file . (! $isFile ? ' not' : '') . ' exists');
        Logger::debug('Moving to ' . $this->pwd());
        $this->cd($this->pwd());
        return $isFile;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::isDir()
     */
    public function isDir(string $path) : bool
    {
        $result = false;
        $localPath = $this->pwd();
        if ($this->cd($path)) {
            $result = true;
        }
        $this->cd($localPath);
        return $result;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::ls()
     */
    public function ls(string $dir, int $mode = null) : array
    {
        Logger::debug('Call to ls: ' . $dir . ' (mode: ' . $mode . ')');
        $res = $this->netSFTP->nlist($dir);
        Logger::debug('Call to ls returns: ' . $res);
        return $res;
    }
    
    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::dirIsEmpty()
     */
    public function dirIsEmpty(string $path): bool
    {
        $content = $this->netSFTP->exec('ls -a ' . $path . ' | head -n 5 | wc -l');
        if ($content === false) {
            Logger::error('Cannot obtain the files count in order to know is a folder is empty: ' . $path);
            return false;
        }
        return ($content - 2) == 0;
    }
    
    private function _mkdir(string $dir, int $mode) : bool
    {
        $result = $this->netSFTP->mkdir($dir);
        if ($result) {
            $result = $this->netSFTP->chmod($mode, $dir);
        }
        return (bool) $result;
    }
    
    private function folderExists(string $dir) : bool
    {
        $localPath = $this->pwd();
        $result = $this->cd($dir);
        $this->cd($localPath);
        return $result;
    }
}
