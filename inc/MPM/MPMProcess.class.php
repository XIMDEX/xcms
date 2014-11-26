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

include_once(XIMDEX_ROOT_PATH . "/inc/MPM/MPMManager.class.php");
include_once(XIMDEX_ROOT_PATH . "/inc/MPM/Semaphore.class.php");
include_once(XIMDEX_ROOT_PATH . "/inc/MPM/SharedMemory.class.php");


class MPMProcess {


	const MPM_PROCESS_OUT_BOOL = "OutBool";
	const MPM_PROCESS_OUT_FILE = "OutFile";


	protected $numMaxChild=6;
	protected $callbackFunction;
	protected $pid;
	protected $ppid;
	//key for de dataOut var in sharedMemory
	protected $keyOutVar;
	//Shared Memory for read and write dataIN and dataOut
	protected $sharedMemory;
	//Key for access to the array position in dataIn and dataOut array
	protected $key;
	//Array DataIn
	protected $dataIn;
	protected $method;
	//boolean that said us if the callback it's a static function in a class or an function
	protected $callbackIsClass;
	//boolean that said us if the callback it's a path, instanciate with Factory class
	protected $callbackIsPath;

	protected $classPath;
	protected $className;
	protected $functionName;
	protected $function;

	/**
	 * Public constructor
	 *
	 * @param mixed $callback --> function to exec
	 * @param array $data --> array to data
	 * @param int $key --> key for access to dataIn and dataOut
	 * @param SharedMemory $sharedMemory
	 * @param int $keyOutVar --> for the dataOut var in sharedMemory
	 */
	public function __construct($callback, $dataIn, $key, $sharedMemory, $keyOutVar) {

		//CHECK THE CORRECT VALUES FOR THE CONSTRUCTOR
		if (is_array($dataIn)){
			$this->dataIn = $dataIn;
		}else{
			throw new Exception("DataIn in MPMProcess have to be an array");
		}
		if (array_key_exists($key,$dataIn)){
			$this->key = $key;
		}else{
			throw new Exception("The key for the data in MPMProcess not exits in dataIn array");
		}
		if ($sharedMemory != null){
			$this->sharedMemory = $sharedMemory;
		}else{
			throw new Exception("The sharedMemory can not be null in MPMProcess");
		}
		if ($keyOutVar > 0){
			$this->keyOutVar = $keyOutVar;
		}else{
			throw new Exception("The key for the shared memory have to be > 0");
		}
		if (MPMManager::is_callback($callback)){
			$this->callbackFunction = $callback;
		}else{
			throw new Exception("Instanciate MPMProcess with a not valid callback function");
		}

		//Get the pid and the ppid
		$this->pid = posix_getpid();
		$this->ppid = posix_getppid();
	}
	/**
	 * Main method, invoke the callback function and return the dataOut
	 *
	 */
	public function run(){
		$this->getMethod();

		//Note:  Call private, protected or abstract methods throw an exception in the invoke method
		//Note: For static method, you have to call invoke() with NULL in the first argument
		//      In other cases, you have to pass an instance of the class
		//TODO: check the correct arguments for the function
		$ret = $this->method->invoke(NULL,$this->dataIn[$this->key]);
		$dataOut = $this->sharedMemory->getVar($this->keyOutVar);
		$dataOut[$this->key]= $ret;
		$this->sharedMemory->putVar($this->keyOutVar, $dataOut);

	}

	protected function getMethod(){
		if (is_array($this->callbackFunction) && count($this->callbackFunction) == 2) {
			try{
				//callback it's a class with a static function
				$var = array_values($this->callbackFunction);
				$className = $this->callbackFunction[0];
				$methodName = $this->callbackFunction[1];

				if ($this->isPath($className)){
					//the className is a path, instanciate by factory class
					$factory = new \Ximdex\Utils\Factory(XIMDEX_ROOT_PATH . $this->classPath, $this->className);
					$this->factoryClass = $factory->instantiate();
					$this->functionName = $methodName;
				}else{
					$isObj = is_object($className);
					$class = new ReflectionClass($isObj ? get_class($className) : $className);
					$this->method = $class->getMethod($methodName);
				}
			}catch (Exception $e){
				throw new Exception("Error invoking a class for Reflection --> ".$e->getMessage());
			}
			$this->callbackIsClass = true;

		} elseif (is_string($this->callbackFunction) && function_exists($this->callbackFunction)) {
			try {
				//	TODO: check the correct arguments for the function
				$this->method = new ReflectionFunction($this->callbackFunction);
			}catch (Exception $e){
				throw new Exception("Error invoking a method for Reflection --> ".$e->getMessage());
			}
			$this->callbackIsClass = false;
		}
	}
	/**
	 * Return true if the first character is a /
	 *
	 * @param string $callback
	 * @return boolean
	 */
	private function isPath($callback){
		if ($callback[0]=='/'){
			$lastPos = strrpos($callback, "/");
			$size = strlen($callback);
			$this->className = substr($callback,$lastPos+1, $size - $lastPos);
			$this->classPath = substr($callback,0,$lastPos);
			
			$this->callbackIsPath = true;
			return true;
		}else{
			$this->callbackIsPath = false;
			return false;
		}
	}

	//AUXILIARY FUNCTIONS
	public function __set($name, $value) {
		$this->$name = $value;
	}
	public function __get($name) {
		return $this->$name;
	}
	public function __isset($name) {
		return isset($this->$name);
	}
	public function __unset($name) {
		unset($this->$name);
	}
}
?>