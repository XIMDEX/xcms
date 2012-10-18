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



define('USER_CONTAINER_NODE_ID', 3);

class Mechanism {

	/**
	 * Construct
	 * @return unknown_type
	 */
	function Mechanism() {
	}

	/**
	 * 
	 * @param $username
	 * @return unknown_type
	 */
	function checkUser($username) {

		$user = new User();
		$user->setByLogin($username);

		if ( $user->hasError() ) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * 
	 * @param $username
	 * @param $password
	 * @param $realname
	 * @param $email
	 * @param $role
	 * @return unknown_type
	 */
	function createUserInXimdex($username, $password, $realname, $email, $role) {

		// TODO: Not BaseIO way to add a new user ?
		//$io = new BaseIO();
		//$io->build();

		$nt_user = new NodeType();
		$nt_user->setByName('User');
		$nt_user_id = $nt_user->getID();

		$user = new Node();
		$user->CreateNode($username, USER_CONTAINER_NODE_ID, $nt_user_id, NULL, $realname, $password, $email, $role);
		
		if ($user->hasError) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * 
	 * @param $username
	 * @param $password
	 * @return unknown_type
	 */
	function authenticate($username, $password) {
	
	}

}


?>
