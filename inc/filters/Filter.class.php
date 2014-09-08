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
	define("XIMDEX_ROOT_PATH", realpath(dirname( __FILE__) . "/../../"));
}

include_once(XIMDEX_ROOT_PATH . "/inc/lang/AssociativeArray.class.php");
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');

//require_once("FilterDB.class.php");

/**
 *
 */
class Filter {

	/**
	 * 
	 * @var unknown_type
	 */
	var $filter_name;
	/**
	 * 
	 * @var unknown_type
	 */
	var $subclass;
	/**
	 * 
	 * @var unknown_type
	 */
	var $in_file;
	/**
	 * 
	 * @var unknown_type
	 */
	var $out_file;
	/**
	 * 
	 * @var unknown_type
	 */
	var $config;
	
	/**
	 * Constructor
	 * @param $name
	 * @return unknown_type
	 */
	function Filter($name) {

		// If $name is null... trigger error ? default filter (identity filter) ?
		// TODO: check if filter $name exists.
		$this->filter_name = $name;
		
		// Instantiate filter Filter_name
		$this->subclass = Filter::getInstance($name);

		if (is_null($this->subclass)) {
			// Filter can be instantiated.
			return null;
		}

	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function initObject() {
		// Default init for specialized classes.
		$this->config = new AssociativeArray();
	}

	/**
	 * 
	 * @param $name
	 * @return unknown_type
	 */
	function & getInstance($name) {

		$filterClassName = "Filter_" . $name;
		$filterClassFile = "Filter_" . $name . ".class.php";
		$filterClassPath = XIMDEX_ROOT_PATH .ModulesManager::path('filters'). "/{$name}/" . $filterClassFile;

		/*  
		print("filterClassName = $filterClassName\n");  
		print("filterClassFile = $filterClassFile\n");  
		print("filterClassPath = $filterClassPath\n");  
		*/       
		
		if (is_null($name)) {
			return null;
		}

		if (file_exists($filterClassPath)) {
			require_once($filterClassPath);
		} else {
			return null;
		}
		
		$obj = new $filterClassName();
		$obj->initObject();
		
		if ( is_null($obj) ) {
			return null;
		}

		return $obj;
	}
	
	/**
	 * 
	 * @param $name
	 * @return unknown_type
	 */
	function loadConfig($name) {
		$this->subclass->loadConfig($name);
	}
	/**
	 * 
	 * @return unknown_type
	 */
	function printConfig() {
		$this->subclass->printConfig();
	}
	/**
	 * 
	 * @param $content
	 * @return unknown_type
	 */
	function & doFilter(&$content) {

		// Create temporary files.
		$input = $this->createInFile($content);
		if ($input === false) {
			return false;
		}
		$output = $this->createOutFile();
		// Call subclass filter method.
		$this->subclass->filter($input, $output, $header);

		$filtered_content = file_get_contents($output);

		$ret_val["content"] = $filtered_content;
		$ret_val["header"] = $header;
		$ret_val['file'] = $this->out_file;
		// Delete temporary files.
		FsUtils::delete($this->in_file);
		FsUtils::delete($this->out_file);
		$this->subclass->delete_files($this->out_file . '.pdf');

		return $ret_val;
	}

	/**
	 * 
	 * @param $content
	 * @return unknown_type
	 */
	private function createInFile(&$content) {

		$in_str = get_class($this->subclass) . "_in_";
		$this->in_file = tempnam("/tmp", $in_str);

		if ($content) {
			if (!FsUtils::file_put_contents($this->in_file, $content)) {
				return false;
			}
		}

		return $this->in_file;
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function createOutFile() {
		$out_str = get_class($this->subclass) . "_out_";
		$this->out_file = tempnam("/tmp", $out_str);

		return $this->out_file;
	}
	
	/**
	 * 
	 * @param $key
	 * @param $value
	 * @return unknown_type
	 */	
	function setConfig($key, $value) {
		$this->config->set($key, $value);
	}
	/**
	 * 
	 * @param $key
	 * @return unknown_type
	 */
	function getConfig($key) {
		$this->config->get($key);
	}

}