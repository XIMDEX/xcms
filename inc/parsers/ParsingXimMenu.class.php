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



//include_once(XIMDEX_ROOT_PATH . "/inc/model/node.inc");

class ParsingXimMenu {

	protected $_menu = null;
	protected $_xpath = null;
	protected $_filters = array(
		'hasPermission',
		'allowedRoles',
		'allowedUsers',
		'ifModule',
	);

	/**
	 * @param mixed menu XimMenu descriptor (Filepath | XML String | XmlDomDocument)
	 */
	public function __construct($menu) {

		if (is_object($menu) && $menu instanceof DOMDocument) {
			$this->_menu = $menu;
		} else if (is_string($menu)) {
			$this->_menu = new DOMDocument();
			
			if (file_exists($menu)) {
				$doc->load($menu);
			} else {
				$doc->loadXML($menu);
			}
		}
		$this->_menu->formatOutput = true;
		$this->_xpath = new DOMXPath($this->_menu);
	}

	public function processMenu($asString=false) {

		foreach ($this->_filters as $filter) {
			$method = "filter_$filter";
			if (method_exists($this, $method)) {
				$query = "//menuitem[@$filter]";
				$nodelist = $this->_xpath->query($query);
				foreach ($nodelist as $node) {
					if (!$this->$method($node)) {
						$node->parentNode->removeChild($node);
					} else {
						// Remove hasPermission attribute, don't show permissions names to the users
						$node->removeAttribute($filter);
					}
				}
			}
		}

		$ret = $asString ? $this->_menu->saveXML() : $this->_menu;
		return $ret;
	}

	protected function filter_hasPermission(&$node) {
		$userid = XSession::get('userID');
		$user = new User($userid);
		$perms = $node->getAttribute('hasPermission');
		$perms = explode(',', $perms);
		$hasPermission = false;
		foreach ($perms as $perm) {
			if ($user->hasPermission(trim($perm))) {
				$hasPermission = true;
			}
		}
		return $hasPermission;
	}

	/**
	 * The role can be specified by RoleId or RoleName
	 */
	protected function filter_allowedRoles(&$node) {

		$userid = XSession::get('userID');
		$user = new User($userid);
		$assignedRoles = $user->GetRoles();

		$roles = $node->getAttribute('allowedRoles');
		$roles = explode(',', $roles);
		$allowed = false;

		foreach ($roles as $role) {
			$role = trim($role);
			if (is_numeric($role)) {
				if (in_array($role, $assignedRoles)) {
					$allowed = true;
				}
			} else {
				foreach ($assignedRoles as $roleid) {
					$roleObj = new Role($roleid);
					if (strtoupper($roleObj->getName()) == strtoupper($role)) {
						$allowed = true;
					}
				}
			}
		}

		return $allowed;
	}

	/**
	 * The user can be specified by UserId or UserName
	 */
	protected function filter_allowedUsers(&$node) {

		$userid = XSession::get('userID');
		$user = new User($userid);
		$login = strtoupper($user->GetLogin());
		$allUsers = $user->GetAllUsers();

		$users = $node->getAttribute('allowedUsers');
		$users = explode(',', $users);
		$allowed = false;

		foreach ($users as $usr) {
			$usr = trim($usr);
			if (is_numeric($usr)) {
				if ($usr == $userid) {
					$allowed = true;
				}
			} else {
				foreach ($allUsers as $ausr) {
					$usrObj = new User($ausr);
					$usrLogin = strtoupper($usrObj->GetLogin());
					if ($usrLogin == strtoupper($usr) && $usrLogin == $login) {
						$allowed = true;
					}
				}
			}
		}

		return $allowed;
	}

	protected function filter_ifModule(&$node) {
		$module = $node->getAttribute('ifModule');
		return defined(strtoupper("MODULE_{$module}_ENABLED"));
	}

}

?>
