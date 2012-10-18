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



// TODO
// - Definir metodo para el cierre de la conexion ldap en caso de error.
// - Enviar errores de LDAP a log de ximDEX.
// - Obtener los datos requeridos para el alta del usuario con una consulta ldap y no con el nombre de los campos.

// Constants
define('LDAP_VERSION_DEFAULT', '3');

define('LDAP_USER_EMAIL_DEFAULT', 'nobody@nowhere.com');
define('LDAP_USER_REALNAME_DEFAULT', 'Jhon Doe');
define('LDAP_USER_ROLE_DEFAULT', '1000');


class Mechanism_LDAP extends Mechanism {

	/**
	 * 
	 * @var unknown_type
	 */
	var $ldap_uri;
	/**
	 * 
	 * @var unknown_type
	 */
	var $ldap_version;
	/**
	 * 
	 * @var unknown_type
	 */
	var $ldap_bind_dn;
	/**
	 * 
	 * @var unknown_type
	 */
	var $ldap_bind_user;
	/**
	 * 
	 * @var unknown_type
	 */
	var $ldap_bind_passwd;
	/**
	 * 
	 * @var unknown_type
	 */
	var $ldap_user_filter;

	/**
	 * 
	 * @return unknown_type
	 */
	function Mechanism_LDAP() {

		if ( ! (defined('LDAP_URI') || defined('LDAP_VERSION'))  ) {
			printf(_("ERROR: Selected LDAP as authenticator mechanism but not options provided.")."\n");
			exit();
		}

		$this->ldap_uri = LDAP_URI;
		$this->ldap_version = LDAP_VERSION;

		$this->ldap_bind_dn = LDAP_BIND_DN;
		$this->ldap_bind_user = LDAP_BIND_USER;
		$this->ldap_bind_passwd = LDAP_BIND_PASSWD;

		$this->ldap_user_filter = LDAP_USER_REGEXP;
	}

	/**
	 * 
	 */
	function authenticate($username, $password) {

		// Open connection to LDAP.
		$conn = ldap_connect($this->ldap_uri);

		// Check connection.
		if (!$conn) {
			printf(_("ERROR: Unable to connect to LDAP server @ ")."{$this->ldap_uri}\n");
			printf("LDAP-ERROR: %s\n", ldap_error()); 
			return false;
		}

		// Options
		ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_version);
		ldap_set_option($conn, LDAP_OPT_REFERRALS, 0); 

		if (defined('LDAP_ANONYMOUS_BIND') && LDAP_ANONYMOUS_BIND) {
			// Try to bind anonymously.
			$link = ldap_bind($conn);
		} else {
			// Authenticate binding.
			$link = ldap_bind($conn, $this->ldap_bind_dn, $this->ldap_bind_pw);
		}

		if (!$link) {
			print("ERROR: Unable to bind using DN={$this->bind_dn}\n");
			printf("LDAP-ERROR: %s\n", ldap_error());
			return false;
		}
		

		$filter = str_replace("%USERNAME%", $username, $this->ldap_user_filter);

		$search_ret = ldap_search($conn, $this->ldap_bind_dn, $filter);

		if (!$search_ret) {
			//printf("ERROR: No returning registers.\n");
			return false;
		}

		$result = ldap_get_entries($conn, $search_ret);

		if ($result['count'] == 0) {
			//printf("ERROR: No returning registers.\n");
			return false;
		}

		$ldap_user_dn = $result[0]['dn'];

		if (defined('LDAP_USER_EMAIL_FIELD')) {
			$ldap_user_email = $result[0][LDAP_USER_EMAIL_FIELD][0];
		} else {
			$ldap_user_email = LDAP_USER_EMAIL_DEFAULT;
		}

		if (defined('LDAP_USER_REALNAME_FIELD')) {
			$ldap_user_realname = $result[0][LDAP_USER_REALNAME_FIELD][0];
		} else {
			$ldap_user_realname = LDAP_USER_REALNAME_DEFAULT;
		}

		if (defined('LDAP_USER_ROLE_FIELD')) {
			$ldap_user_rol =  LDAP_USER_ROLE_FIELD;
		} else {
			$ldap_user_rol = LDAP_USER_ROLE_DEFAULT;
		}
		
		$link_user = ldap_bind($conn, $ldap_user_dn, $password);

		if (!$link_user) {
			return false;
		}

		if (parent::checkUser($username)) {
			//printf("El usuario $username existe\n");

			return true;
		} else {
			parent::createUserInXimdex($username, $password, $ldap_user_realname, $ldap_user_email, $ldap_user_rol);
			return true;
		}

		// Close connection to LDAP.
		if ( ! ldap_close($conn) ) {
			printf("LDAP-ERROR: %s\n", ldap_error());
		}

	}

}
?>
