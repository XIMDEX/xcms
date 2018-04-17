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

namespace Ximdex\IO\Connection;

use GuzzleHttp\Client;
use Ximdex\Utils\FsUtils;

class ConnectionApi extends Connector implements IConnector
{
    private $client;
    private $connected = false;
    private $isFile = false;
    private $host = false;
    
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::connect()
	 */
	public function connect($host = null, $port = null)
	{
	    $this->client = new Client();
	    if ($this->server) {
	        $host = 'http://' . $this->server->get('Host');
	        if ($this->server->get('Port')) {
	            $host .= ':' . $this->server->get('Port');
	        }
	        $host .= '/' . trim($this->server->get('InitialDirectory'), '/');
	    }
	    else {
	        $host .= ':' . $port;
	    }
	    $this->host = $host;
	    $res = $this->client->request('GET', $this->host);
	    if ($res->getStatusCode() == 200) {
	        $res = true;
	    }
	    else {
	        $res = false;
	    }
	    $this->connected = $res;
		return $res;
	}
	
    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::disconnect()
     */
	public function disconnect()
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
	public function isConnected()
	{
		return $this->connected;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::login()
	 */
	public function login($username = 'anonymous', $password = 'john.doe@example.com')
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::cd()
	 */
	public function cd($dir)
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
	public function mkdir($dir, $mode = 0755, $recursive = false)
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::chmod()
	 */
	public function chmod($target, $mode = 0755, $recursive = false)
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::rename()
	 */
	public function rename($renameFrom, $renameTo)
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::size()
	 */
	public function size($file)
	{
	    //TODO ajlucena!
		return 1;
	}
	
    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::ls()
     */
	public function ls($dir, $mode = null)
	{
		return [];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::rm()
	 */
	public function rm($path)
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::get()
	 */
	public function get($sourceFile, $targetFile, $mode = 0755)
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::put()
	 */
	public function put($localFile, $targetFile, $mode = 0755)
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
	    $res = $this->client->request('PUT', $this->host, ['body' => $content]);
	    if ($res->getStatusCode() == 200) {
	        $this->isFile = true;
	        return true;
	    }
		return false;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::isDir()
	 */
	public function isDir($path)
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\IO\Connection\IConnector::isFile()
	 */
	public function isFile($path)
	{
		return $this->isFile;
	}	
}