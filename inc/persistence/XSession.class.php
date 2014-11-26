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
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

require_once(XIMDEX_ROOT_PATH . '/inc/helper/QueryManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/Response.class.php');

/** @const DEFAULT_SESSION - Name of default session */
define('DEFAULT_SESSION', 'SessionID');
/** @const HTTP_SESSION_STARTED - The session was started with the current request */
define("HTTP_SESSION_STARTED",      1);
/** @const HTTP_SESSION_STARTED - No new session was started with the current request */
define("HTTP_SESSION_CONTINUED",    2);


/**
 *
 */
class XSession {

	// session_set_save_handler, permite modificar la manera en que se gestionan las sesiones. (bd, etc..)

	/**
	 *  Set new name of a session
	 *
	 *  @static
	 *  @access public
	 *  @param string $name New name of a session
	 *
	 */
	public static function name($name = NULL) {
		return isset($name) ? session_name($name) : session_name();
	}

	/**
	 *  Create a session.
	 *  @static
	 */
	public static function start($name = DEFAULT_SESSION, $id = null) {
		XSession::name($name);
		session_cache_limiter('none');
        if(isset($_SERVER["REQUEST_URI"])){
    		session_set_cookie_params(36000,$_SERVER["REQUEST_URI"]);		
        }
		session_cache_expire(60);

		@session_start();

		if (!isset($_SESSION['__HTTP_Session_Info'])) {
			$_SESSION['__HTTP_Session_Info'] = HTTP_SESSION_STARTED;
		} else {
			$_SESSION['__HTTP_Session_Info'] = HTTP_SESSION_CONTINUED;
		}

	}
	
	public static function refresh() {
		$sid = session_id();
		if (empty($sid))
			XSession::start();
		session_regenerate_id();
		setcookie(ini_get("session.name"),
                  session_id(),time()+ini_get("session.cookie_lifetime"),
                  ini_get("session.cookie_path"),
                  ini_get("session.cookie_domain"),
                  ini_get("session.cookie_secure"),
                  ini_get("session.cookie_httponly")
                );

	}

	public static function isNew() {

		return !isset($_SESSION['__HTTP_Session_Info']) ||
			$_SESSION['__HTTP_Session_Info'] == HTTP_SESSION_STARTED;
	}

	public static function set($key, $data) {

		$sid = session_id();
		if (empty($sid)) XSession::start();
		$_SESSION[$key] = $data;
	}

	public static function delete($key) {

		if (XSession::exists($key)) {
			session_unregister($key);
			unset($_SESSION[$key]);
		}
	}

	public static function get($key) {

		$ret = null;
		if (XSession::exists($key)) $ret = $_SESSION[$key];
		return $ret;
	}

	public static function exists($key) {

		$sid = session_id();
		if (empty($sid)) XSession::start();
		$ret = isset($_SESSION[$key]);
		return $ret;
	}

	public static function serialize($key, &$var) {

		$SESSION[$key] = serialize($var);
	}

	public static function & unserialize($key) {

		if (XSession::exists($key)) {
			$o = unserialize($_SESSION[$key]);
			return $o;
		} else {
			return NULL;
		}
	}

	public static function destroy() {

		XSession::start();
		if (!empty($_SESSION)) {
			session_unset();
			$_SESSION = array();
			session_destroy();
		}
	}


	function display() {
		echo "<pre>"; print_r($_SESSION); echo "</pre>";
	}

	function getDisplay() {
		return print_r($_SESSION, true);
	}

	public static function check($redirect = true) {
		XSession::start();
		$queryManager = new QueryManager();
		if(!array_key_exists("action", $_GET) ) {
				$_GET["action"] = null;
		}

		if (!XSession::exists('logged') && "installer" != $_GET["action"]) {
			if($redirect) {
				$response = new Response();
				$response->sendStatus(sprintf("Location: %s/", \App::getValue( 'UrlRoot')), true, 301);
				setcookie("expired", "1", time() + 60);
				die();
			}
			return false;
		}else {
			return true;
		}
	}

	public static function checkUserID() {
		$userID = XSession::get('userID');

		if($userID == '301' )
			return true;
		else
			return false;
	}
}

?>