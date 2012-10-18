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

include_once(XIMDEX_ROOT_PATH . "/inc/MPM/MPMProcess.class.php");

class MPMProcessOutFile extends MPMProcess {

	/**
	 * Public constructor
	 *
	 * @param array $args
	 */
	public function __construct($args){
		$numArgs = count($args);
		if (is_array($args) && ($numArgs == 5)){
			parent::__construct($args[0], $args[1], $args[2], $args[3], $args[4]);
		}else{
			throw new Exception("Can not instanciate MPMProcessOutBool, ilegal arguments");
		}
	}
	/**
	 * Extends the run from his parents and change the dataOut format data
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


}

?>
