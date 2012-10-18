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



/*
 * AUTH_MECH = { 'SQL, 'LDAP' }
 */
//define('AUTH_MECH', 'LDAP');
define('AUTH_MECH', 'SQL');

/*
 * IF AUTH_MECH == 'SQL'
 */
// Define other source for user info.
// define('SQL_HOST', 'localhost');
// define('SQL_USERNAME', 'username');
// define('SQL_PASSWD', 'passwd');


/*
 * IF AUTH_MECH == 'LDAP'
 */
define('LDAP_URI', 'ldap://ldap.ximdex.net');
define('LDAP_VERSION', '3');

define('LDAP_ANONYMOUS_BIND', true);

/*
 * IF LDAP_ANONYMOUS_BIND == false
 */
define('LDAP_BIND_DN', 'dc=ximdex, dc=com');
define('LDAP_BIND_USER', 'cn=admin, dc=ximdex, dc=com');
define('LDAP_BIND_PASSWD', 'passwd');

define('LDAP_USER_REGEXP', 'uid=%USERNAME%');
define('LDAP_USER_EMAIL_FIELD', 'mail');
define('LDAP_USER_REALNAME_FIELD', 'cn');
define('LDAP_USER_ROLE_FIELD', 201)


?>
