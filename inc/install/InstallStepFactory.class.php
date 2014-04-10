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
        define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

require_once(XIMDEX_ROOT_PATH . '/inc/patterns/Factory.class.php');

class InstallStepFactory {


	const STEP_PATH = "/inc/install/steps";
	/**
	 *
	 * 
	 * @return unknown_type
	 */
	public static function getStep ($steps, $currentState) {
		
		$currentStep = null;
		$posStep = 0;
		foreach ($steps as $step) {
			if ($step["state"] == strtolower($currentState)){
				$currentStep = $step;				
				break;
			}
			$posStep++;
		}
		
		$className = "Generic";		
		
		if ($currentStep && $currentStep["class-name"]){
			$className = $currentStep["class-name"];			
		}
		$currentState = strtolower($className);
		$stepPath = XIMDEX_ROOT_PATH.self::STEP_PATH."/".$currentState;
		$factory = new Factory($stepPath, $className);
		$stepObject = $factory->instantiate("InstallStep");
		$stepObject->setCurrentStep($posStep);
		return $stepObject;


	}
}


?>