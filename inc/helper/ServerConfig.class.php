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
      
      
      
  
  
  
  
if (!defined('XIMDEX_ROOT_PATH')) {
        define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../..'));
}
class ServerConfig{


	private	$disabledFunctionsInConfig = array();
	private $ximdexRequiredFunctions = array("pcntl_fork","pcntl_waitpid");


	public function __construct(){
	
		$this->disabledFunctionsInConfig = explode(',', ini_get('disable_functions'));
	}

	/**
	* Return boolean if found a function in disabled_functions directive in php.ini_get
	* @param undefined $function non required param. It can be the name of the function to check, an array of functions name or null.
	*/		
	public function hasDisabledFunctions($function=null){

		$disabledFunctionsFounded = $this->findDisabledFunctions($function);
		return is_array($disabledFunctionsFounded) && count($disabledFunctionsFounded);
	}


	/**
	* Check if a function is disabled in php.ini_get
	* @param string $functionName function name to check.
	*/
	
	public function isDisabledFunctions($functionName){
	
		if (is_string($functionName)){			
			return $this->hasDisabledFunctions($functionName);	
		}
		return false;
	}


	private function findDisabledFunctions($function=null){
	
		if (!$function){
			$requiredFunctions = $this->ximdexRequiredFunctions;
		}else if (is_string($function)){
			$requiredFunctions = array($function);
		}else if (is_array($function)){
			$requiredFunctions = $function;
		}
		
		$disabledFunctionsFounded = array();
		
		foreach ($requiredFunctions as $functionName){
			if (in_array($functionName, $this->disabledFunctionsInConfig)){
				$disabledFunctionsFounded[] = $functionName;
			}
		}

		return $disabledFunctionsFounded;
	}

	public function getAllDisabledFunctions(){
		return $this->disabledFunctionsInConfig;
	}


}
?>
