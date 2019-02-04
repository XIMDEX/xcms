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

use GuzzleHttp\Client;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;

class ConnectionApi extends Connector implements IConnector
{
    private $client;
    private $connected = false;
    private $host = false;
    
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::connect()
	 */
    public function connect(string $host = null, int $port = null) : bool
	{
	    $this->client = new Client();
	    if ($this->server) {
	        $host = 'http://' . $this->server->get('Host');
	        if ($this->server->get('Port')) {
	            $host .= ':' . $this->server->get('Port');
	        }
	        $host .= '/' . trim($this->server->get('InitialDirectory'), '/');
	    } else {
	        $host .= ':' . $port;
	    }
	    $this->host = $host;
	    try {
	       $res = $this->client->request('GET', $this->host);
	    }
	    catch (\Exception $e) {
	        $this->error = $e->getMessage();
	        $this->connected = false;
	        return false;
	    }
	    if ($res->getStatusCode() != 200) {
	        $this->error = 'The connection to API service return the status code: ' . $res->getStatusCode();
	        $this->connected = false;
	        return false;
	    }
	    $this->connected = true;
		return true;
	}
	
    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::disconnect()
     */
	public function disconnect() : bool
	{
	    $this->client = null;
	    $this->server = null;
	    $this->connected = false;
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::isConnected()
	 */
	public function isConnected() : bool
	{
		return $this->connected;
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
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::pwd()
	 */
	public function pwd()
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::mkdir()
	 */
	public function mkdir(string $dir, int $mode = 0755, bool $recursive = false) : bool
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::chmod()
	 */
	public function chmod(string $target, int $mode = 0755, bool $recursive = false) : bool
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::rename()
	 */
	public function rename(string $renameFrom, string $renameTo) : bool
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::size()
	 */
	public function size(string $file)
	{
		return 1;
	}
	
    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::ls()
     */
	public function ls(string $dir, int $mode = null) : array
	{
		return [];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::rm()
	 */
	public function rm(string $path, int $id = null) : bool
	{
	    try {
	        $res = $this->client->request('DELETE', $this->host . '/' . App::getValue('ximid') . ':' . $id);
	        
	        // Delay time between delete call
	        // usleep(300000);
	    }
	    catch (\Exception $e) {
	        $this->error = $e->getMessage();
	        $this->code = $e->getCode();
	        return false;
	    }
	    if ($res->getStatusCode() == 200) {
	        return true;
	    }
	    $this->error = 'The connection to API service return the status code: ' . $res->getStatusCode();
	    return false;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::get()
	 */
	public function get(string $sourceFile, string $targetFile, int $mode = 0755): bool
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::put()
	 */
	public function put(string $localFile, string $targetFile, int $mode = 0755): bool
	{
	    $content = FsUtils::file_get_contents($localFile);
	    if ($content === false) {
	        return false;
	    }
	    $dom = new \DOMDocument();
	    if ($dom->loadXML($content) === false) {
	        $this->error = 'The content given in PUT request is not a valid XML document';
            return false;
	    }
	    try {
	       $res = $this->client->request('PUT', $this->host, ['body' => $content, 'headers' => ['Content-Type' => 'application/xml']]);
	       
	       // Delay time between put call
	       // usleep(300000);
	    }
	    catch (\Exception $e) {
	        $this->error = $e->getMessage();
	        $this->code = $e->getCode();
	        return false;
	    }
	    if ($res->getStatusCode() == 200) {
	        return true;
	    }
	    $this->error = 'The connection to API service return the status code: ' . $res->getStatusCode();
		return false;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::isDir()
	 */
	public function isDir(string $path) : bool
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::isFile()
	 */
	public function isFile(string $path): bool
	{
		return $this->isFile;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::dirIsEmpty()
	 */
    public function dirIsEmpty(string $path): bool
    {
        return false;
    }
}
