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



class PoolerConf {

	protected $dom = null;
	protected $xpath = null;

	public function __construct($path) {
		$this->dom = new DOMDocument('1.0');
		if (!$this->dom->load($path)) {
			throw new Exception('Need a valid XML file.');
		}
		$this->xpath = new DOMXPath($this->dom);
		try {
			$this->parse();
		} catch(Exception $e) {
			//echo $e->getMessage() . "\n";
			throw new Exception($e->getMessage());
		}

		$this->xpath = null;
		$this->dom = null;
	}

	protected function parse() {
		$this->parseServer();
		$this->parseClient();
		$this->parseQueues();
	}

	protected function parseServer() {

		$this->server = array();

		$this->server['address'] = trim($this->xpath->evaluate('string(/pooler/server/address)', $this->dom));
		if ($this->server['address'] == '') {
			throw new Exception('Need a valid bind address.');
		}

		$this->server['port'] = trim($this->xpath->evaluate('string(/pooler/server/port)', $this->dom));
		if ($this->server['port'] == '') {
			throw new Exception('Need a valid port.');
		}

//		$this->server['sockettimeout'] = trim($this->xpath->evaluate('string(/pooler/server/sockettimeout)', $this->dom));
//		if ($this->server['sockettimeout'] == '') {
//			// 1 sec
//			$this->server['sockettimeout'] = 1000;
//		}

		$this->server['pidfile'] = trim($this->xpath->evaluate('string(/pooler/server/pidfile)', $this->dom));
		if ($this->server['pidfile'] == '') {
			$this->server['pidfile'] = '/var/run/poolerd.pid';
		}

		$this->server['user'] = trim($this->xpath->evaluate('string(/pooler/server/user)', $this->dom));
		if ($this->server['user'] == '') {
			throw new Exception('Need a valid user.');
		}

		$this->server['group'] = trim($this->xpath->evaluate('string(/pooler/server/group)', $this->dom));
		if ($this->server['group'] == '') {
			throw new Exception('Need a valid group.');
		}

		$this->server['umask'] = trim($this->xpath->evaluate('string(/pooler/server/umask)', $this->dom));
		if ($this->server['umask'] == '' || !is_numeric($this->server['umask'])) {
			$this->server['umask'] = 0033;
		}
		$this->server['umask'] = sprintf('%04d', (int)$this->server['umask']);

		$this->server['userdir'] = trim($this->xpath->evaluate('string(/pooler/server/userdir)', $this->dom));
		if (!is_dir($this->server['userdir'])) {
			$this->server['userdir'] = '/tmp';
		}

		$this->server['clientSocketBufferSize'] = trim($this->xpath->evaluate('string(/pooler/server/clientSocketBufferSize)', $this->dom));
		if ($this->server['clientSocketBufferSize'] == '') {
			$this->server['clientSocketBufferSize'] = 2048;
		}
		$this->server['clientSocketBufferSize'] = (int)$this->server['clientSocketBufferSize'];
	}

	protected function parseClient() {

		$this->client = array();
		$options = $this->xpath->query('/pooler/client/*', $this->dom);
		foreach ($options as $option) {
			$name = $option->nodeName;
			$this->client[$name] = $option->nodeValue;
		}

	}

	protected function parseQueues() {

		// Shared memory initial ID
		$sharedSpace = 200;
		$this->queues = array();

		$queues = $this->xpath->query('/pooler/queues/queue', $this->dom);
		foreach ($queues as $queue) {
			$aux = array();
			$aux['command'] = array();
			$aux['name'] = strtoupper(trim($this->xpath->evaluate('string(name)', $queue)));
			$aux['sharedMemoryId'] = $sharedSpace++;
			$aux['wait'] = trim($this->xpath->evaluate('string(wait)', $queue));
			$aux['command']['path'] = trim($this->xpath->evaluate('string(command/path)', $queue));
			$aux['command']['class'] = trim($this->xpath->evaluate('string(command/class)', $queue));
			$aux['command']['method'] = trim($this->xpath->evaluate('string(command/method)', $queue));
			$aux['command']['params'] = array();
			$params = $this->xpath->query('command/params/param', $queue);
			foreach ($params as $param) {
				$aux['command']['params'][] = $param->nodeValue;
			}
			$this->queues[] = $aux;

		}
	}

	public function getQueueByName($name) {
		foreach ($this->queues as $queue) {
			if ($queue->name == strtoupper($name)) {
				return $queue;
			}
		}
		return null;
	}

	public function getQueueById($id) {
		foreach ($this->queues as $queue) {
			if ($queue->sharedMemoryId == $id) {
				return $queue;
			}
		}
		return null;
	}

}

?>
