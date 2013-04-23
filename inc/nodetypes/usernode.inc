<?php

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *
 *                                                                            *
 *                                                                            *
 ******************************************************************************/










if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

include_once XIMDEX_ROOT_PATH . "/inc/model/user.inc";
require_once (XIMDEX_ROOT_PATH . "/inc/nodetypes/root.inc");

class UserNode extends Root {

	function CreateNode($login = null, $parentID = null, $nodeTypeID = null, $stateID = null, $realName = null, $pass = null, $email = null,$locale = null, $generalRole = null) {

		$user = new User();
		$idUser = $user->SetByLogin($login);

		if ($idUser > 0) {
			XMD_Log::error("Another user with the same login $login");
			return NULL;
		}

  		$ret = $user->CreateNewUser($realName, $login, $pass, $email, $locale, $generalRole, $this->parent->get('IdNode'));
		$this->UpdatePath();

		return $ret;
	}

	function DeleteNode() {
	 	$user = new User($this->parent->get('IdNode'));
		$user->DeleteUser();
	}
}

?>
