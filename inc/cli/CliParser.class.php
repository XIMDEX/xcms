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


define('TYPE_INT', 1);
define('TYPE_STRING', 2);
/**
 * List separate by ',' without blank spaces
 *	Example: 1,2,3,4,5,6,7,8 ...
 * @var unknown_type
 */
define('TYPE_ARRAY', 3);
/**
 * List separate by ',' and key, values separated by '='
 * Example: a=1,b=2,c=3
 * @var unknown_type
 */
define('TYPE_HASH', 4);
define('TYPE_LIST', 5);
/**
 * List separated by ',' without blank spaces
 * Example: 1,2,3,4,5,6,7,8 ...
 * @var unknown_type
 */
define('TYPE_ARRAY_LIST', 6);
define('PARAM_HELP', '--help');
define('PARAM_FILE', '--paramsFile');

/**
 * Param supported by default:
 *
 * --help: Shows help about supported params
 * --paramsFile: Obtain a part or all the params from a configuration file
 * 		Params sent by console prevail over the obtained from a file.
 *
 */
abstract class CliParser {
	/**
	 * 
	 * @var unknown_type
	 */
	var $messages = null;
	/*
	array(
		name => string
		mandatory => bool
			si mandatory = false
			default => defaultValue
		message => string (when param is obligatory and it is not coming)
		type => int (constants)
		validValues => array valid values allowed in the var
		default => defaultValue just in case mandatory = true and value empty
		group => array('name' => 'global group name', 'key' => local_group_index)
		
	)

*/
	/**
	 * 
	 * @var unknown_type
	 */
	var $_metadata = null;
	/**
	 * 
	 * @var unknown_type
	 */
	private $arguments = null;
	
	/**
	 * Construct
	 *
	 * @param number of args received $argc (not in use)
	 * @param $argv argument values
	 * @return CliParser
	 */
	function CliParser($argc, $argv=NULL) {
		
		$this->messages = new \Ximdex\Utils\Messages();
		$this->messages->displayEncoding = '';
		$this->arguments = array();
		$params = array();
		
		// Explode the argv key = value params
		if(count($argv)>1) {
			foreach ($argv as $value) {
				if (strpos($value, '=') > 0) {
					$elements = explode('=', $value);
					$params = array_merge($params, $elements);
				} else {
					$params[] = $value;
				}
			}
		}
		elseif(count($argv)==1) {
			$params[]=$argv;
		}
		
		if (in_array(PARAM_HELP, $params)) {
			$this->_printHelp();
			$this->messages->displayRaw();
			die();
		}
		if (in_array(PARAM_FILE, $params)) {
			$key = array_search(PARAM_FILE, $params);
			if ($key > 0) { // redundant check, but safer
				if (isset($params[$key + 1])) {
					$fileName = $params[$key + 1];
					if (is_file($fileName)) {
						$fileArguments = file_get_contents($fileName);
						if (!empty($fileArguments)) {
							/* To make all args to be separated by blank spaces*/
							$search = array("\n", '=');
							$replace = array_fill(0, count($search), ' ');
							
							$fileArguments = str_replace($search, $replace, $fileArguments);
							
							$fileArguments = preg_replace('/\s+/', ' ', $fileArguments);
							/*
							  	Exploding args, sending them to a var and they're overwritten by the argv one (mandatory)
							 */
							$arguments = explode(' ', $fileArguments);
							$this->_parseParameters($arguments);
						
						}
					}
				}
			}
		}
		$this->_parseParameters($params);
		$this->_parseMandatory();
		if ($this->messages->count(MSG_TYPE_ERROR) > 0) {
			$this->messages->displayRaw();
			die();
		}
	
	}
	
	/**
	 * 
	 * @param $parameter
	 * @return void
	 */
	function getParameter($parameter) {
		if (array_key_exists($parameter, $this->arguments)) {
			return $this->arguments[$parameter];
		}
		return null;
	}
	/**
	 * 
	 * @param $parameter
	 * @return void
	 */
	private function setParameter($parameter, $value) {
		$this->arguments[$parameter] = $value;
	}
	
	/**
	 * 
	 * @param $index
	 * @return unknown_type
	 */
	function getParameterByIndex($index) {
		return $this->arguments[$index];
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function getParametersArray() {
		return $this->arguments;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function _printHelp() {
		$this->messages->add(_("\n"._("Command params:")."\n"), MSG_TYPE_NOTICE);
		$this->messages->add(sprintf(_("Option: %s Shows this help"), PARAM_HELP)."\n", 
				MSG_TYPE_NOTICE);
		$this->messages->add(
				sprintf(_("Option: %s Allows to include params from a file"), 
						PARAM_FILE)."\n", MSG_TYPE_NOTICE);
		
		reset($this->_metadata);
		while (list (, $token) = each($this->_metadata)) {
			$mandatory = $token['mandatory'] ? _(' Obligatorio ') : ' ';
			$this->messages->add(
					sprintf(_("Option: %s%s%s\n"), $token['name'], $mandatory, 
							$token['message']), MSG_TYPE_NOTICE);
		}
	}
	
	/**
	 * 
	 * @param $argv
	 * @return unknown_type
	 */
	function _parseParameters($params) {
		reset($this->_metadata);
		while (list (, $argument) = each($this->_metadata)) {
			// Checking if name is coming, if not, it is not mandatory
			if (!in_array($argument['name'], $params) && $argument['mandatory']) {
				continue;
			}
			
			$key = array_search($argument['name'], $params);
			if ($key !== false) { // It should
				$value = $params[$key + 1];
				switch ($argument['type']) {
					case TYPE_INT :
						if (!is_numeric($value) && !is_int($value)) {
							$this->messages->add(
									sprintf(_('The argument %s should be numeric'), 
											$argument['name']), MSG_TYPE_ERROR);
						}
						break;
					case TYPE_STRING :
						if (!is_string($value)) {
							$this->messages->add(
									sprintf(_('The argument %s should be a text string'), 
											$argument['name']), MSG_TYPE_ERROR);
						}
						break;
					case TYPE_ARRAY :
						$value = explode(',', $value);
						if (!is_array($value)) {
							$this->messages->add(
									sprintf(_('The argument %s should be an array'), 
											$argument['name']), MSG_TYPE_ERROR);
						}
						break;
					case TYPE_HASH :
						$values = explode(',', $value);
						$value = array();
						reset($values);
						while (list (, $pair) = each($values)) {
							list ($key, $data) = explode('=', $pair);
							$value[$key] = $data;
						}
						if (!is_array($value)) {
							$this->messages->add(
									sprintf(_('The argument %s should be a hash'), 
											$argument['name']), MSG_TYPE_ERROR);
						}
						break;
					case TYPE_LIST :
						if (!in_array($value, $argument['values'])) {
							$this->messages->add(
									sprintf(_('The argument should be one of the following: ')), 
									MSG_TYPE_ERROR);
							foreach ($argument['values'] as $validValue) {
								$this->messages->add($validValue, MSG_TYPE_ERROR);
							}
						}
						break;
					case TYPE_ARRAY_LIST :
						$value = explode(',', $value);
						$auxArray = array_intersect($value, $argument['values']);
						if (!(count($auxArray) == count($value))) {
							$this->messages->add(
									sprintf(_('The argument should be one or more of the following: ')), 
									MSG_TYPE_ERROR);
							foreach ($argument['values'] as $validValue) {
								$this->messages->add($validValue, MSG_TYPE_ERROR);
							}
						}
						break;
					default :
						$this->messages->add(
								sprintf(_('Type of param %s has not been specified'), 
										$argument['name']), MSG_TYPE_ERROR);
						continue;
				}
				
				if (empty($value) && $argument['mandatory']) {
					continue;
				}
				
				if (!empty($value)) {
					$this->setParameter($argument['name'], $value);
					$processedKeys[] = $key;
					$processedKeys[] = ($key + 1);
				}
			
			}
		}
		if(isset($params[0]))
			$this->setParameter('SCRIPT_NAME', $params[0]);
		else
			$this->setParameter('SCRIPT_NAME', $params);
		$processedKeys[] = 0;
		foreach ($processedKeys as $key) {
			unset($params[$key]);
		}
		$this->setParameter('NOT_CATEGORIZED', $params);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function _parseMandatory() {
		$parsedGroups = array();
		foreach ($this->_metadata as $argument) {
			$parameterValue = $this->getParameter($argument['name']);
			if (empty($parameterValue))
				if ($argument['mandatory']) {
					if (isset($argument['group']) && !empty($argument['group'])) {
						$groupedKeys = $this->getGroupedKeys($argument['group']['name'], 
								$argument['group']['value']);
						$valueForGroupFound = false;
						foreach ($groupedKeys as $groupedKey) {
							$parameterValue = $this->getParameter($groupedKey);
							if (!empty($parameterValue)) {
								$valueForGroupFound = true; // The value has been defined in other group
								break;
							}
						}
						if (!$valueForGroupFound) {
							if (!in_array($argument['group']['name'], $parsedGroups)) {
								$parsedGroups[] = $argument['group']['name'];
							} else {
								continue;
							}
							$allGroupedKeys = $this->getGroupedKeys(
									$argument['group']['name'], NULL, true);
							$messageKeys = array();
							foreach ($allGroupedKeys as $argumentsInvolvedInGroup) {
								$messageKeys[] = implode(', ', $argumentsInvolvedInGroup);
							}
							$this->messages->add(
									sprintf(_('(Mandatory some of the following set of values) (%s)'), 
											implode(')(', $messageKeys)), MSG_TYPE_ERROR);
						}
					} else {
						$this->messages->add(sprintf(_('[%s]: (Mandatory) %s'), $argument['name'], 
										$argument['message']), MSG_TYPE_ERROR);
					}
				} else {
					if (!empty($argument['default'])) {
						$this->setParameter($argument['name'], $argument['default']);
					}
				}
		}
		if (!empty($argument['validValues']) && !empty($parameterValue) && !in_array(
				$parameterValue, $argument['validValues'])) {
			$this->messages->add(
					sprintf(_('[%s]: (Not allowed value) %s'), $argument['name'], 
							$argument['message']), MSG_TYPE_ERROR);
		}
	}
	private function getGroupedKeys($groupName, $excludeGroupId = NULL, $groupByKey = false) {
		$groups = array();
		foreach ($this->_metadata as $argument) {
			if (isset($argument['group']) && !empty($argument['group'])) {
				if (($argument['group']['name'] == $groupName)) {
					if (!empty($excludeGroupId) && $argument['group']['value'] == $excludeGroupId)
						continue;
					if ($groupByKey) {
						$groups[$argument['group']['value']][] = $argument['name'];
					} else {
						$groups[] = $argument['name'];
					}
				}
			}
		}
		return $groups;
	
	}

}

?>