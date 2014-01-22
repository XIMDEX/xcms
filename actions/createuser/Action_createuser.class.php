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

ModulesManager::file('/conf/install-params.conf.php');
ModulesManager::file('/inc/model/locale.inc');

class Action_createuser extends ActionAbstract {
    // Main method: shows main init form
    function index() {
		$idNode = $this->request->getParam('nodeid');

        $role = new Role();
        $roles = $role->find('IdRole, Name');

		$locale = new XimLocale();
		$locales = $locale->GetEnabledLocales();

		$values = array(
			'id_node' => $idNode,
			'go_method' => 'createuser',
			'roles' => $roles,
			'locales' => $locales
		);

		$this->render($values, null, 'default-3.0.tpl');
    }

    function createuser($idNode = NULL, $name = NULL, $login = NULL, $pass = NULL, $confirmPass = NULL, $email = NULL, $locale = NULL, $generalrole = NULL, $render = true) {

    	if (empty($idNode)) {
    		$idNode = $this->request->getParam('id_node');
    	}
    	if (empty($name)) {
    		$name = $this->request->getParam('name');
    	}
    	if (empty($login)) {
    		$login = $this->request->getParam('login');
    	}
    	if (empty($pass)) {
    		$pass = $this->request->getParam('pass');
    	}
    	if (empty($confirmPass)) {
    		$confirmPass = $this->request->getParam('confirmpass');
    	}
    	if (empty($email)) {
    		$email = $this->request->getParam('email');
    	}
    	if (empty($generalrole)) {
    		$generalrole = $this->request->getParam('generalrole');
    	}

	if (empty($locale) || !@file_exists(XIMDEX_ROOT_PATH . '/inc/i18n/locale/'.$locale) ) {
    		$locale = $this->request->getParam('locale');
			if(null == $locale || !@file_exists(XIMDEX_ROOT_PATH . '/inc/i18n/locale/'.$locale)) {
				$locale = DEFAULT_LOCALE;
			}
    	}

	  	$nodeType = new NodeType();
		$nodeType->SetByName('User');

		$usuario = new Node();

		if (strcmp($pass, $confirmPass)) {
			$usuario->messages->add(_('Inserted passwords do not match, the user could not be created'), MSG_TYPE_ERROR);
		}

		if (!($usuario->messages->count(MSG_TYPE_ERROR) > 0)) {
			$result = $usuario->CreateNode($login, $idNode, $nodeType->get('IdNodeType'), null, $name, $pass, $email, $locale, $generalrole);
		}

		if($result > 0) {
			$usuario->messages->add(_('User has been successfully inserted'), MSG_TYPE_NOTICE);
		}

		if ($render) {
			$this->reloadNode($idNode);
			$values = array('messages' => $usuario->messages->messages );
			$this->render($values, NULL, 'messages.tpl');
		}

		return $result;
    }
}
?>
