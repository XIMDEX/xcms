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

//include_once(XIMDEX_ROOT_PATH . "/inc/MPM/MPMProcessOutBool.class.php");
//include_once(XIMDEX_ROOT_PATH . "/inc/MPM/MPMProcessOutFile.class.php");
include_once(XIMDEX_ROOT_PATH . "/inc/MPM/Semaphore.class.php");
include_once(XIMDEX_ROOT_PATH . "/inc/MPM/SharedMemory.class.php");
include_once (XIMDEX_ROOT_PATH. "/inc/patterns/Factory.class.php");
include_once (XIMDEX_ROOT_PATH. "/inc/db/db.inc");


//Handler the signal
$callback = array("MPMManager", "singnalHandler");
declare(ticks=1);
//pcntl_signal(SIGTERM, $callback);
//pcntl_signal(SIGINT, $callback);
//pcntl_signal(SIGUSR1, $callback);

class MPMManager {

	private $numMaxChild;
	private $dataIn;
	private $dataOut = array();
	private $callbackFunction;
	private $numChild;
	private $sharedMemory;
	//Seconds for wait when there are the max of process running
	private $timeForSleep;
	//Type of process, View MPMProcess constants
	private $typeProcess;

	//key for $numChild and $dataOut in sharedMemory
	const KEY_NUM_CHILD = 1;
	const KEY_DATAOUT = 2;

	/**
	 * Public constructor
	 *
	 * @param Array $callback -- function to exec for every data in dataIn
	 * @param Array $dataIn
	 * @param int $typeProcess --> type of Process, View MPMProcess constants
	 * @param int $numMaxChild -- num max of process running
	 * @param int $timeForSleep  -- time for wait when there are the max number of process runningS
	 */
	public function __construct($callback, $dataIn, $typeProcess, $numMaxChild = 5, $timeForSleep=1) {

		//CHECK THE PARAMS
		//Init numChild
		$this->numChild = 0;
		$this->timeForSleep = $timeForSleep;
		if ((MPMProcess::MPM_PROCESS_OUT_BOOL == $typeProcess) ||
		(MPMProcess::MPM_PROCESS_OUT_FILE == $typeProcess)){
			$this->typeProcess = $typeProcess;
		}else{
			throw new Exception("Type Process for MPMManager not valid");
		}

		//check numMaxChild
		if ($numMaxChild <= 0){
			throw new Exception("numMaxChild have to be > 0");
		}else{
			$this->numMaxChild = $numMaxChild;
		}
		//check callback
		if(!MPMManager::is_callback($callback)){
			throw new Exception("Instanciate MPMManager with a not valid callback function");
		}else{
			$this->callbackFunction = $callback;
		}
		//check dataIn
		if (!is_array($dataIn)){
			throw new Exception("Instanciate MPMManager with a not valid dataIn format");
		}else{
			$this->dataIn = $dataIn;
		}

		//Create a memory shared for all process
		$SHM_KEY = ftok(__FILE__, chr(1));
		$this->sharedMemory = new SharedMemory($SHM_KEY,100000);
		$this->sharedMemory->create();
		$this->sharedMemory->putVar(MPMManager::KEY_NUM_CHILD,$this->numChild);
		$this->sharedMemory->putVar(MPMManager::KEY_DATAOUT, $this->dataOut);
	}
	/**
	 * Main method
	 *
	 */
	public function run(){

		//Iterate for DataIn, for each key, creating new process
		foreach ($this->dataIn as $key => $value) {

			$this->numChild = $this->sharedMemory->getVar(MPMManager::KEY_NUM_CHILD);
			/*
			//echo "xx $this->numChild child executing ";
			//check if there are more process than $this->numMaxChild, if it's --> sleep $this->timeForSleep seconds
			while ($this->numChild >= $this->numMaxChild){
				//TODO: dangerous, infinite loop!!!
				echo "too much process running ($this->numChild), sleeping $this->timeForSleep seconds\n";
				print_r(debug_backtrace());
				sleep($this->timeForSleep);
				$this->numChild = $this->sharedMemory->getVar(MPMManager::KEY_NUM_CHILD);
			}*/

			//Forking, pid can be:
			// -1 --> Failed
			//  0 --> We are in the child
			//  >0 -> We are in the parent, this name is the pid for the child created
			$pid = pcntl_fork();

			if ($pid == -1){
				//Error, TODO
				exit;
			}else if ($pid == 0){
				//We are in the child, create a new Process
				//echo " child $key created \n";
				$this->createProcess($key);
			}else{
				//We are the parent
				//Inc the numChild
				$this->sharedMemory->incVar(MPMManager::KEY_NUM_CHILD);
			}
		}

		while (pcntl_waitpid(0, $stat) != -1){
			//The parent wait to end all his child
		}

		//Distroy the shared Memory
		$this->sharedMemory->destroyMemory();

		//Reset the database, the database connection is duplicated in the forking and when
        //the child ended, there are some secundary effects
		//With this, we force to the reconnect

		$db = new DB();
		$db->reconectDataBase();

	}
	/**
	 * Create a new Process
	 *
	 * @param int $key
	 * @param unknown_type $dataFunction --> data for call the callback function
	 */
	private function createProcess($key){
		//Call the user function
		try {
			$factory = new Factory(XIMDEX_ROOT_PATH . "/inc/MPM/", "MPMProcess");
			$args = array($this->callbackFunction, $this->dataIn, $key, $this->sharedMemory, MPMManager::KEY_DATAOUT);
			$process = $factory->instantiate($this->typeProcess, $args);

			$process->run();
			//TODO:Can not have the status for the child finish?
			$this->childEnded();
		}catch (Exception $e){
			//If anything was wrong, we kill the process
			//TODO:how send the error message???
			echo "\n".$e->getMessage()."\n";
			exit();
		}
	}
	/**
	 * Check if the parameter is a correct callback function
	 * callback function can be --> array, with [0] = class and [1] = static method
	 *                              string, with the function name declared
	 * @param unknown_type $var
	 * @return boolean
	 */
	public static function is_callback($var){
		if (is_array($var) && count($var) == 2) {
			//var have to be an array, var[0] = class and var[1] = method
			$var = array_values($var);
			$className = $var[0];
			$methodName = $var[1];


			if (MPMManager::isPath($className)){
				//the class name is a path, for instanciate by factory class
				//TODO:not any more??
				return true;

			}else{
				//instanciate by reflection
				if ((!is_string($className) && !is_object($className)) || (is_string($className) && !class_exists($className))) {
					return false;
				}
				$isObj = is_object($className);
				$class = new ReflectionClass($isObj ? get_class($className) : $className);
				if ($class->isAbstract()) {
					return false;
				}
				try {
					$method = $class->getMethod($methodName);
					if (!$method->isPublic() || $method->isAbstract()) {
						return false;
					}
					if (!$isObj && !$method->isStatic()) {
						return false;
					}
				} catch (ReflectionException $e) {
					return false;
				}
				return true;
			}
		} elseif (is_string($var) && function_exists($var)) {
			//var is a function
			return true;
		}
		return false;
	}
	/**
	 *
	 *
	 */
	private function childEnded(){
		//Extract the status code for the child termination
		//$status = pcntl_wexitstatus($status);
		//TODO:check the status

		//dec the num of child created
		$this->sharedMemory->decVar(MPMManager::KEY_NUM_CHILD);
		//kill the process --> This it's important, be careful with the zombies process
		exit();
	}
	/**
	 * Handler for the signal
	 *
	 * @param Signal $signal
	 */
	public static function singnalHandler($signal){
		switch($signal){
			case SIGTERM:
				echo "signal cached SIGTERM";
				exit;
			case SIGINT:
				echo "signal cached SIGINT";
				exit;
			case SIGKILL:
				echo "signal cached SIGALRM";
				exit;
			case SIGUSR1:
				echo "signal cached SIGUSR1";
				exit;
		}
	}
	/**
	 * Return true if a string is a valid path for a class
	 *
	 * @param string $name
	 * @return boolean
	 */
	public static function isPath($name){
		if ($name[0]=='/'){
			return true;
		}else{
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