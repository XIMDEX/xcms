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




if(!defined('XIMDEX_ROOT_PATH'))
        define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../"));

require_once(XIMDEX_ROOT_PATH . "/inc/model/user.inc");
require_once(XIMDEX_ROOT_PATH . "/inc/model/group.inc");
require_once(XIMDEX_ROOT_PATH . "/inc/model/NodetypeMode.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/model/orm/RelRolesActions_ORM.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/model/orm/Contexts_ORM.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/model/orm/RelUsersGroups_ORM.class.php");

class Auth {
	/**
	 * 
	 * @param $userId
	 * @param $nodeId
	 * @return unknown_type
	 */
	public static function _checkExistence($userId, $nodeId) {

		return true;
	}

	/**
	 * 
	 * @param $params
	 * @return unknown_type
	 */
	public static function _parseParams($params) {

		$formedParams = array();

		if (is_array($params)) {

			if (isset($params['node_id']) && $params['node_id'] > 0) {

				$nodeId = (int) $params['node_id'];

				if (isset($params['node_type']) && $params['node_type'] > 0) {

					$formedParams['node_id'] = $nodeId;
					$formedParams['node_type'] = (int) $params['node_type'];

				} else {

					$node = new Node($nodeId);
					$idNodeType = $node->GetNodeType();

					$formedParams['node_id'] = $nodeId;
					$formedParams['node_type'] = $idNodeType;

					unset($node);
				}

				return $formedParams;
			}

			if (isset($params['node_type']) && $params['node_type'] > 0) {

				$idNodeType = $params['node_type'];

				// TODO: Check if is a valid nodetype.

				$formedParams['node_type'] = $idNodeType;

				return $formedParams;
			}

		} else {
			return NULL;
		}
	}


	/**
	 * 
	 * @param $userId
	 * @param $nodeId
	 * @return unknown_type
	 */
	public static function _access($userId, $nodeId) {

		// TODO: define as global constans nodeid=10000 && nodeid=2
		if ($nodeId == 1 || $nodeId == 10000 || $nodeId == 2 || $userId == 301) {
			return true;
		}

		$group = new Group();

		$user = new User($userId);
		$userGroupList = $user->getGroupList();
		$generalGroup = array($group->getGeneralGroup());
		$user_groups = array_diff(array($userGroupList), array($generalGroup));

		$node = new Node($nodeId);
		$nodeGroupList = $node->getGroupList();
		$node_groups = array_diff(array($nodeGroupList), array($generalGroup));

		$rel_groups = array_intersect($user_groups, $node_groups);

		if ( (count($rel_groups) > 0) || $user->isOnNode($nodeId, true) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 
	 * @param $userId
	 * @param $params
	 * @return unknown_type
	 */
	public static function canRead($userId, $params) {

		$wfParams = Auth::_parseParams($params);

		$nodeId = $wfParams['node_id'];

		if ( ! Auth::_checkExistence($userId, $nodeId) ) {
			return false;
		}

		$user = new User($userId);
/*
		if ( Auth::_access($userId, $nodeId) || $user->hasPermission('view all nodes') ) {
			return true;
		}
*/

		if ($user->hasPermission('view all nodes')) {
			return true;
		}

		if ( Auth::_access($userId, $nodeId) ) {
			return true;
		}

		return false;
	}

	/**
	 * Comprueba si un usuario puede escribir un nodo
	 *
	 * @param int $userId
	 * @param array $params array asociativo que debe contener las claves node_id o node_type
	 * @return bool (true, si puede escribir, false en caso contrario)
	 */
	public static function canWrite($userId, $params) {

		$wfParams = Auth::_parseParams($params);
		$idPipeline = NULL;
		
		if (isset($wfParams['node_id'])) {

			$nodeId = (int) $wfParams['node_id'];

			if (!Auth::_checkExistence($userId, $nodeId)) {
				return false;
			}

			// Usuario ximdex
			if ($userId == 301) return true;

			if (!Auth::_access($userId, $nodeId)) {
				return false;
			}
			
			$workflow = new WorkFlow($nodeId);
			$idPipeline = $workflow->pipeline->get('id');

		}

		// Should be always set.
		if (!isset($wfParams['node_type'])) {
			return false;
		}

		$nodeTypeId = (int) $wfParams['node_type'];

		if (Auth::_checkContext($userId, $nodeTypeId, CREATE)) {
			return true;
		}

		// Check groups&roles and defined actions...
		$user = new User($userId);
		$userRoles = $user->GetRoles();

		if (!is_array($userRoles)) {
			return false;
		}

		$userRoles = array_unique($userRoles);
		unset($user);

		$nodeType = new NodeType($nodeTypeId);
		$actionId = $nodeType->GetConstructor();

		unset($nodeType);

		if (!$actionId > 0) {
			XMD_Log::warning(sprintf(_("The nodetype %d has no create action associated"), $nodeTypeId));
			return false;
		}

		foreach($userRoles as $userRole) {
			$role = new Role($userRole);
			if ($role->HasAction($actionId,$idPipeline)) {
				return true;
			}
		}

		// Not write action found for roles of userId.
		return false;

	}

	/**
	 * 
	 * @param $userId
	 * @param $params
	 * @return unknown_type
	 */
	public static function canDelete($userId, $params) {

		// TODO: extend relation table with delete actions/nodetypes mapping.

		$wfParams = Auth::_parseParams($params);

		if (!isset($wfParams['node_type'])) {
			return false;
		}

		$nodeTypeId = (int) $wfParams['node_type'];

		if (Auth::_checkContext($userId, $nodeTypeId, DELETE)) {
			return true;
		}

		return Auth::canWrite($userId, $params);
	}

	/**
	 * 
	 * @param $userId
	 * @param $params
	 * @return unknown_type
	 */	
	public static function canModify($userId, $params) {

		// TODO: extend relation table with modify actions/nodetypes mapping.

		$wfParams = Auth::_parseParams($params);

		if (!isset($wfParams['node_type'])) {
			return false;
		}

		$nodeTypeId = (int) $wfParams['node_type'];

		if (Auth::_checkContext($userId, $nodeTypeId, UPDATE)) {
			return true;
		}

		return Auth::canWrite($userId, $params);
	}

	/**
	 * 
	 * @param $idUser
	 * @param $idNodeType
	 * @param $mode
	 * @return unknown_type
	 */
	public static function _checkContext($idUser, $idNodeType, $mode) {
		$nodeTypeMode = new NodetypeMode();
		$idAction = $nodeTypeMode->getActionForOperation($idNodeType, $mode);
		if (!($idAction > 0)) {
			return false;
		}

		$context = XSession::get('context');
		$contextsObject = new Contexts_ORM();
		$result = $contextsObject->find('id', 'Context = %s', array($context), MONO);
		$idContext = count($result) == 1 ? $result[0] : '1';

		$relRolesActions = new RelRolesActions_ORM();
		$result = $relRolesActions->find('IdRol',
			'IdAction = %s AND IdContext = %s',
			array($idAction, $idContext),
			MONO);

		$idRol = count($result) == 1 ? $result[0] : NULL;
		if(!($idRol) > 0) {
			return false;
		}

		$relUserGroup = new RelUsersGroups_ORM();
		$relations = $relUserGroup->find('IdRel',
			'IdUser = %s AND IdRole %s',
			array($idUser, $idRol),
			MONO);

		return (count($relations) > 0);
	}

	/**
	 * 
	 * @param $userId
	 * @param $permission
	 * @return unknown_type
	 */
	public static function hasPermission($userId, $permission) {
		$user = new User($userId);
		return (boolean)$user->hasPermission($permission);
	}

}

?>
