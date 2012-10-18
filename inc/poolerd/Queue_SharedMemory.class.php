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

require_once POOLER_ROOT_PATH . '/QueueAbstract.class.php';
require_once XIMDEX_ROOT_PATH . '/inc/MPM/SharedMemory.class.php';


class Queue_SharedMemory extends QueueAbstract {

	protected $sharedMemory = null;

	public function __construct(&$queueConf) {
		parent::__construct($queueConf);
	}

	public function & getSharedMemory(&$sharedMemory) {
		return $this->sharedMemory;
	}

	public function setSharedMemory(&$sharedMemory) {
		$this->sharedMemory = $sharedMemory;
		$this->sharedMemory->putVar($this->sharedMemoryId, array());
	}

	public function push($value) {
		$data = $this->sharedMemory->getVar($this->sharedMemoryId);
		$data[] = $value;
		$this->sharedMemory->putVar($this->sharedMemoryId, $data);
	}

	public function pop() {
		$data = $this->sharedMemory->getVar($this->sharedMemoryId);
		$value = array_shift($data);
		$this->sharedMemory->putVar($this->sharedMemoryId, $data);
		return $value;
	}

}

?>
