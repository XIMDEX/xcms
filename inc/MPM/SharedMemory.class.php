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

include_once(XIMDEX_ROOT_PATH . "/inc/MPM/Semaphore.class.php");

//TODO: Now, 1 SharedMemory class --> 1 shared memory, it's better than 1 class could have more "shared memory"
class SharedMemory{

	private $keySemaphore;
	private $keyMemory;
	private $size;
	private $sem;
	private $dataMemory;
	private $perm;

	/**
	 * Public constructor
	 *
	 * @param int $key
	 * @param int $size -->The memory size. If not provided, default to the sysvshm.init_mem in the php.ini, otherwise 10000 bytes.
	 * @param int $perm -->The optional permission bits. Default to 0666.
	 */
	public function __construct($keySem, $size, $perm = null){
		//TODO: check correct values
		$this->keySemaphore = $keySem;
		$this->size = $size;
		$this->keyMemory = "123456";

		if ($perm != null){
			$this->perm = $perm;
		}
	}
	/**
	 * Creating a shared memory
	 *
	 */
	public function create(){

		//generate a semaphore for the memory shared
		$SHM_KEY = ftok(__FILE__, chr(1));
		$this->sem = new Semaphore();
		$this->sem->getSemaphore($this->keySemaphore);

		if ($this->perm != null){
			$this->dataMemory = shm_attach($this->keyMemory, $this->size, $this->perm);
		}else{
			$this->dataMemory = shm_attach($this->keyMemory, $this->size);
		}

	}
	/**
	 * Put a variable in the shared memory
	 *
	 * @param int $keyVar
	 * @param mixed $value
	 *
	 * @return bool --> Return if the operation it's ok
	 */
	public function putVar($keyVar, $value){
		//TODO:check the space available
		$this->sem->lock();
		$exit = shm_put_var($this->dataMemory, $keyVar, $value);
		$this->sem->unlock();

		return $exit;
	}
	/**
	 * Get a variable from the shared memory
	 *
	 * @param int $key
	 * @return mixed --> var value for this key
	 */
	public function getVar($key){
		//TODO: check if exits this key in shared memory
		$this->sem->lock();
		if ($this->exitsVar($key)){
			$value = shm_get_var($this->dataMemory, $key);
		}else{
			//The key not exits
			$value=null;
		}
		$this->sem->unlock();
		return $value;
	}
	/**
	 * Check if exits a var for that key in the shared memory
	 *
	 * @param int $key
	 */
	public function exitsVar($key){
		//TODO:
		if((@shm_get_var($this->dataMemory, $key)) === FALSE) {
			return false;
		}else{
			return true;
		}
	}

	/**
	 * Inc the value for a var in the shared memory
	 *
	 * @param int $key
	 */
	public function incVar($key){
		//TODO: check if exits a key in the shared memory
		$this->sem->lock();
		$value = shm_get_var($this->dataMemory, $key);
		$value++;
		shm_put_var($this->dataMemory, $key, $value);
		$this->sem->unlock();
	}
	/**
	 * Dec the value for a var in the shared memory
	 *
	 * @param int $key
	 */
	public function decVar($key){
		//TODO: check if exits a key in the shared memory
		$this->sem->lock();
		$value = shm_get_var($this->dataMemory, $key);
		$value--;
		shm_put_var($this->dataMemory, $key, $value);
		$this->sem->unlock();
	}
	/**
	 * Removes a variable from shared memory
	 * @return bool 
	 */
	public function deleteVar($key){
		if ($this->exitsVar($key)){
			$this->sem->lock();
			$ret = shm_remove_var($this->dataMemory,$key);
			$this->sem->unlock();
		}else{
			$ret = false;	
		}
		return $ret;

	}
	/**
	 * Disconnects from shared memory segment
	 * shm_detach() disconnects from the shared memory given by the shm_identifier  created by shm_attach().
	 * Remember, that shared memory still exist in the Unix system and the data is still present.
	 *
	 */
	public function closeMemory(){
		$this->sem->lock();
		$ret = shm_detach($this->dataMemory);
		$this->sem->unlock();
	}
	/**
	 * Removes the shared memory shm_identifier . All data will be destroyed. 
	 *
	 */
	public function destroyMemory(){
		$this->sem->lock();
		$ret = shm_remove($this->dataMemory);
		$this->sem->unlock();
		return $ret;
	}
}


?>