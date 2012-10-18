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
define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

//TODO: Now, 1 Semaphore class --> 1 semaphore, it's better than 1 class could have more semaphores
class Semaphore{
	public $key;
	public $sem_identifier;
	
	/**
	 * Create a new semaphore
	 *
	 * @param unknown_type $key
	 */
	public function getSemaphore($key){
		//TODO: check if is a valid key
		$this->key = $key;
		$this->sem_identifier =  sem_get($this->key);
	}
	/**
	 * Lock the resource that the semaphore control
	 */
	public function lock(){
		sem_acquire($this->sem_identifier);
	}
	/**
	 * Unlock the resource that the semaphore control
	 *
	 */	
	public function unlock(){
		sem_release($this->sem_identifier);
	}
}

?>