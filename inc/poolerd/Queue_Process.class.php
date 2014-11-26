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




if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));

if (!defined('POOLER_ROOT_PATH'))
	define('POOLER_ROOT_PATH', realpath(dirname(__FILE__)));

require_once POOLER_ROOT_PATH . '/Queue_SharedMemory.class.php';


class Queue_Process extends Queue_SharedMemory {

	protected $pid = null;
	protected $close = null;
	protected $blocked = null;

	protected $cmd = null;

	public function __construct(&$queueConf) {
		parent::__construct($queueConf);
		$this->close = false;
		$this->blocked = false;

		$path = sprintf('%s/%s/', XIMDEX_ROOT_PATH, $this->command['path']);
		$factory = new \Ximdex\Utils\Factory($path, $this->command['class']);
		$this->cmd = $factory->instantiate();

		if (!is_object($this->cmd) || !method_exists($this->cmd, $this->command['method'])) {
			$this->cmd = null;
		}
	}

	public function getPID() {
		return $this->pid;
	}

	public function setPID($pid) {
		$this->pid = $pid;
	}

	public function run() {

		pcntl_signal(SIGUSR1, array(&$this, 'handleTask'));
		pcntl_signal(SIGUSR2, array(&$this, 'stop'));

		$sleep = (int)$this->wait / 1000;
		while (!$this->close) {
			$this->handleTask();
			sleep($sleep);
		}
		exit(0);
	}

	protected function stop() {
		$this->close = true;
	}

	public function handleTask() {

	if ($this->cmd === null || $this->blocked) return;
	
		$this->blocked = true;
		$data = $this->pop();
		
		if ($data !== null) {
		
		    $method = $this->command['method'];
		    foreach ($data as $params) {
			    $p = array();
			    foreach ($this->command['params'] as $_p) {
				    $p[] = $params[$_p];
			    }
			    call_user_func_array(array(&$this->cmd, $method), $p);
		    }
		}

		$this->blocked = false;
	}

}

?>