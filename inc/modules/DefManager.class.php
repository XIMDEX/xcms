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



/**
 *  
 */
class DefManager {

	var $configFilename;
	var $configData;
	
	var $prefix;
	var $postfix;
	
	/**
	 *  @public
	 */
	function DefManager($fileName, $prefix = '', $postfix = '') {
 		
		$this->configFilename = $fileName;

		// Check config presence.
		if ( !file_exists($this->configFilename) ) {
			$this->createConfigFile();
		}
    	
		// Acquire data	
		if ( is_readable($this->configFilename) ) {
    		
			$this->configData = file($this->configFilename);
    		
			// Clean data.
			foreach ($this->configData as $idx => $line) {
				$line = rtrim($line);
				$line = ltrim($line);
				$this->configData[$idx] = $line;
			}
			} else {
				printf("* ERROR: File is not readable. [$this->configFilename]\n");
			}	
	}
	
	function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
	
	function setPostfix($postfix) {
		$this->postfix = $postfix;
	}
	
	/**
	 *  @protected
	 */
	function createConfigFile() {
		
		//TODO: Check if we can open it!
		$file_hnd = fopen($this->configFilename, "w");
		
		fwrite($file_hnd, "<?php\n  ");
		//input modules path 
		fwrite($file_hnd, self::get_modules_path() );
		fwrite($file_hnd, "?>");
		
		fclose($file_hnd);
	}

	function get_modules_path() {
		$modMngr = new ModulesManager();
		$consts = '';
		$modules = $modMngr->getModules();
		foreach($modules as $id => $module ) {
			$consts .= "define('".PRE_DEFINE_MODULE.strtoupper($module['name']).POST_PATH_DEFINE_MODULE."', '".$module["path"]."');\n";
		}	

		return $consts;	
	}


	/**
	 *  @public
	 */
	function enableItem($name) {

		if ( $this->isEnabledItem($name) === FALSE ) {
			
			$str = $this->prefix . $name . $this->postfix;
			$end_tag_idx = array_search("?>", $this->configData);
			
			array_splice($this->configData, $end_tag_idx, 0, $str);
			
			$this->writeToConfig();
			
		} 
		/*	
		else {
			printf("nothing to do in $this->configFilename\n");
		}
		*/
	}
	
	/**
     *  @public
     */   
	function disableItem($name) {
	
		if ( ($key = $this->isEnabledItem($name)) !== FALSE ) {
			unset($this->configData[$key]);
			$this->writeToConfig();
		} 
		/*
		else {
			print("nothing to do in $this->configFilename\n");
		}
		*/
	}
	
	function isEnabledItem($name) {
		$str = $this->prefix . $name . $this->postfix;
		return array_search($str, $this->configData);
	}

	function writeToConfig() {

		if ( is_writable($this->configFilename) ) {
			
			$file_hnd = fopen($this->configFilename, "w");
			
			foreach ($this->configData as $line) {
				fwrite($file_hnd, "$line\n");
			}
			fclose($file_hnd);
			
		} else {
			print("* ERROR: " . __CLASS__ . "::" . __FUNCTION__ . ": Cant write to file " . $this->configFilename . "\n");
		}
	}
	
	
}

?>
