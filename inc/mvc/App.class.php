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

require_once(XIMDEX_ROOT_PATH . '/inc/patterns/Registry.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/patterns/Factory.class.php');
/**
 * 
 * @brief Actions global app object who stores some common objects to all actions
 *
 * Global object who stores some common objects to all actions like Messages, QueryManager, DB and XMD_Log
 * actually is pretty deprecated
 */
class App {
	var $registry = NULL;
	var $objects = NULL;
	
	
	/**
	 * Constructor
	 * @return unknown_type
	 */
	function App() {
		$this->registry = new Registry();
		$this->objects = array(
			'Messages' => array(
				'LOCATION' => XIMDEX_ROOT_PATH . '/inc/helper/', 
				'OBJECT' => 'Messages'),
			'QueryManager' => array(
				'LOCATION' => XIMDEX_ROOT_PATH . '/inc/helper/', 
				'OBJECT' => 'QueryManager'),
			'DB' => array(
				'LOCATION' => XIMDEX_ROOT_PATH . '/inc/db/', 
				'OBJECT' => 'DB'),
			'XMD_log' => array(
				'LOCATION' => XIMDEX_ROOT_PATH . '/inc/log/', 
				'OBJECT' => 'XMD_log')
		);
	}
	
	/**
	 * 
	 */
	public static function & getInstance() {

		static $instance = NULL;

		if ($instance === NULL) {
			$instance = new App();
		}

		return $instance;
	}
	
	/**
	 * 
	 * @param $key
	 * @return unknown_type
	 */
	public static function & get($key) {
		$app =& App::getInstance();
		if ($app->registry->exist($key)) {
			return $app->registry->get($key);
		}
		if (isset($app->objects[$key])) {
			$factory = new Factory($app->objects[$key]['LOCATION'], $app->objects[$key]['OBJECT']);
			$instance =  $factory->instantiate();

			if($key && $instance) {
				$app->registry->set($key, $instance);
				return $app->registry->get($key);
			}
		}
		error_log("Error al intentar cargar el objeto". $key);
		return NULL;
	}

	/**
	 * 
	 * @param $key
	 * @param $obj
	 * @return unknown_type
	 */
	public static function set($key, &$obj) {
		$app =& App::getInstance();
		if ($app->registry->exist($key)) {
			return true;
		}
		if (is_array($obj) && isset($obj['FILE']) && isset($obj['NAME'])){
			require_once($obj['FILE']);
			$object = new $obj['NAME'];
			return $app->registry->set($key, $object);
		}
		return $app->registry->set($key, $obj);
	}
}
?>
