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
 * XIMDEX_ROOT_PATH
 */
if (!defined('XIMDEX_ROOT_PATH'))
        define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

require_once(XIMDEX_ROOT_PATH . '/inc/auth/Mechanism.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/patterns/Factory.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/persistence/XSession.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/user.php');
// Include Auth Configuration.
include_once(XIMDEX_ROOT_PATH . "/conf/auth.conf");


/**
 * Constants
 */
define('MECH_SQL_TYPE', 'SQL');
define('MECH_LDAP_TYPE', 'LDAP');
define('MECH_DEFAULT_TYPE', MECH_SQL_TYPE);

class Authenticator {

	/**
	 *
	 * @var unknown_type
	 */
	var $mech_factory;
	/**
	 *
	 * @var unknown_type
	 */
	var $mech_type;

	/**
	 * Constructor
	 * @return unknown_type
	 */
	function Authenticator() {

		$mechs_path = XIMDEX_ROOT_PATH . "/inc/auth/mechs/";
		$mechs_root_name = "Mechanism_";

		$this->mech_factory = new Factory($mechs_path, $mechs_root_name);

		// read conf
		if (defined('AUTH_MECH')) {

			switch (AUTH_MECH) {

				case 'LDAP':

					if (extension_loaded('ldap')) {
						$this->mech_type = MECH_LDAP_TYPE;
					} else {
						// If not LDAP present fallback to SQL.
						// log('Authenticator: LDAP extension not present, fallback to SQL authentication.');
						$this->mech_type = MECH_DEFAULT_TYPE;
					}

					break;

				case 'SQL':

					$this->mech_type = MECH_SQL_TYPE;
					break;

				default:

					$this->mech_type = MECH_DEFAULT_TYPE;
			}

		} else {

			print(sprintf(_("ERROR: %s/conf/auth.conf not present or badformed configuration"),$XIMDEX_ROOT_PATH)."\n");
			exit();
		}
	}

	/**
	 *
	 * @param $name
	 * @param $password
	 * @return unknown_type
	 */
	function login($name, $password) {

		// check names which have a fixed mech.
		//if ($name == 'ximdex') { $this->mech_type = MECH_DEFAULT_TYPE }

		// factory Mech to authenticate, fallback to SQL if not selected
		$mech =& $this->mech_factory->instantiate($this->mech_type);
		if (!is_object($mech)) {
			XMD_Log::error($this->mech_factory->getError());
			return false;
		}

		if ( $mech->authenticate($name, $password) ) {

			// Is a valid user !
			$user = new User();
			$user->setByLogin($name);
			$user_id = $user->getID();
			$user = new user($user_id);

			$user_locale = $user->get('Locale');

			if(empty($user_locale) )
				$user_locale = Config::getValue('locale');

		// STOPPER
			$stopperFilePath = Config::getValue("AppRoot") . Config::getValue("TempRoot") . "/login.stop";
			if ( $user->getID() != "301" && file_exists($stopperFilePath)) {
				// login closed
				return false;
			}

			if(ModulesManager::isEnabled("ximDEMOS") ) {
				$user_demo = (int) $user->isDemo();
			}else {
				$user_demo = 0;
			}

			unset($user);

			if (ModulesManager::isEnabled('ximADM')) {
				ModulesManager::file('/inc/Status.class.php', 'ximADM');

				$user_status = new Status();
				$user_status->remove($user_id);
				$user_status->init($user_id);
			}

			// TODO: Add new session system.
			XSession::set('user_name',$name );
			XSession::set('user_demo',$user_demo);
			XSession::set('logged', $user_id);
			XSession::set('userID', $user_id);
			XSession::set('locale', $user_locale);
			XSession::set('loginTimestamp', gmmktime());
			
			return true;
		} else {
			// Not a valid user.

			return false;
		}
	}

	/**
	 *
	 * @return unknown_type
	 */
	function logout() {

		// TODO: Add new session system.
		XSession::destroy();
/*
		@session_start();
		@session_unregister("logged");
		@session_unregister("userID");
		@session_unset();
		@session_destroy();
*/
	}

}


?>