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




require_once(XIMDEX_ROOT_PATH. '/inc/modules/ModulesManager.class.php');
ModulesManager::file('/inc/db/db.inc');
/**
 *
 */
class Config {

	/**
	 * @access protected.
	 */
	var $configData;
    
	/**
	 * @return none
	 */
	function Config() {
    
		$this->configData = array();
	}
    
	/**
	* @return One and only one instance of Config.
	*/
	public static function & getInstance() {

		static $instance = NULL;

		if ($instance === NULL) {
			$instance = new Config();
		}

		return $instance;
	}

	function & getConfigData() {
	}
   

	public static function exists($key) {

		$conf =& Config::getInstance();
		$conf->getValue($key);

		if (isset($conf->configData[$key])) {
			return true;
		} else {
			return false;
		}
	}
    
	/**
	*
	*/
	public static function getValue($key) {

		$conf =& Config::getInstance();

		if (! isset($conf->configData[$key])) {
			//print("not_set $key\n");

			// Load config from database.
			$dbObj = new DB();
			$query = "SELECT ConfigValue FROM Config WHERE ConfigKey = '$key'";

			$dbObj->Query($query);
			if ($dbObj->numRows > 0) {
				$conf->setValue($key, $dbObj->GetValue('ConfigValue'));
			} else {
				$backtrace = debug_backtrace();
				error_log(sprintf('Intentando obtener un valor de config que no existe [inc/persistence/Config.class.php] script: %s file: %s line: %s valor: %s', 
							$_SERVER['SCRIPT_FILENAME'],
							$backtrace[0]['file'],
							$backtrace[0]['line'],
							$key));
			}
		}
		return isset($conf->configData[$key]) ? $conf->configData[$key] : NULL;
	}
    
	/**
	 *
	 */
	function setValue($key, $value) {
		//print("setValue($key, $value)\n");
        
		$this->configData[$key] = $value;
	}
   
   	function update($key, $value) {

		if (!self::exists($key)) {
			$dbObj = new DB();
			$result = $dbObj->Execute("INSERT INTO Config VALUES (NULL, '$key', '$value')");
			return true;
		}

		$dbObj = new DB();
		$result = $dbObj->Execute("UPDATE Config SET ConfigValue = '$value' WHERE ConfigKey = '$key'");
		
		return true;
   	}

	/**
	 * Implementar lectura de ficheros de configuracion externos.
	 */
	function parseConfig() { 
		// implement with parse_ini_file
	}
    
}

?>
