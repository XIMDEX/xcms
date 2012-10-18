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

ModulesManager::file('/inc/model/language.inc');
ModulesManager::file('/inc/model/channel.inc');
ModulesManager::file('/actions/manageproperties/inc/InheritedPropertiesManager.class.php');
class Action_infonode extends ActionAbstract {

    function index() {
 		$idNode	= (int) $this->request->getParam("nodeid");

		$node = new Node($idNode);
		$info = $node->loadData();

		//actions
		$actions = $this->getActions($idNode);
		//channels
		$channel = new Channel();
		$channels = $channel->getChannelsForNode($idNode);

		//langs
		$nodeLanguages = $node->getProperty('language', true);
		$languages = array();
		if(!empty($nodeLanguages) ) {
			$i = 0;
			foreach($nodeLanguages as $_lang) {
				$_node = new Node($_lang);
				$languages[$i]["Id"] = $_lang;
				$languages[$i]["Name"] = $_node->get("Name");
			}
		}

		$transformer = $node->getProperty('Transformer', true);

 		$values = array(
			'id_node' => $idNode,
			'info' => $info,
			'actions' => $actions,
			'channels' => $channels,
			'languages' => $languages,
			'transformer' => $transformer
		);


		$this->render($values, 'index', 'default-3.0.tpl');
    }



/**
	 * Calculates the posible actions for a group of nodes.
	 * Returns the actions on a JSON string.
	 */
	protected function getActions($nodes=null, $processActionName=true) {

		$idUser = XSession::get('userID');
		$nodes = $nodes !== null ? $nodes : $this->request->getParam('nodes');
		if (!is_array($nodes)) $nodes = array($nodes);

		$actions = $this->getActionsOnNodeList($idUser, $nodes, $processActionName);

		return $actions;
	}

	/**
	 * Calculates the posible actions for a group of nodes.
	 * It depends on roles, states and nodetypes of nodes.
	 * Returns an array of actions.
	 */
	protected function getActionsOnNodeList($idUser, $nodes, $processActionName=true) {

		$db = new DB();

		$nodes = array_unique($nodes);

		// Used for commands intersection (1)
		$arrNodeTypes = array();
		// Used for actions intersection (2)
		$arrStates = array("0");
		// Used for groups intersection (3)
		$arrNodes2 = array();

		// ---------------------------- Step 1 -----------------------------

		// Find groups for the node list:
		// 1) User groups
		// 2) Node groups
		//		If NodeType::CanAttachGroups == 0 find parent groups until CanAttachGroup == 1


		// This way is better for a reduced group of IdNodes

//		$db2 = new DB();
//		$sqlNodeInfo = 'select n.idNode, n.idParent, n.idNodeType, IFNULL(n.idState, 0) as idState, nt.canAttachGroups
//			from Nodes n join NodeTypes nt using(idNodeType)
//			where n.idnode in (%s)';
//
//		$db->query(sprintf($sqlNodeInfo, implode(',', $nodes)));
//		while (!$db->EOF) {

//			$arrNodeTypes[] = $db->getValue('idNodeType');
//			$arrStates[] = $db->getValue('idState');
//			$db->next();
//
//			$canAttachGroups = $db->getValue('canAttachGroups');
//			$idNode = $db->getValue('idNode');
//			$idParent = $db->getValue('idParent');
//			$nodeHasGroups = true;
//
//			while ($canAttachGroups == 0 && $nodeHasGroups) {
//				$db2->query(sprintf($sqlNodeInfo, $idParent));
//				if (!$db2->EOF) {
//					$idNode = $db2->getValue('idNode');
//					$idParent = $db2->getValue('idParent');
//					$canAttachGroups = $db2->getValue('canAttachGroups');
//					$db2->next();
//				} else {
//					$nodeHasGroups = false;
//				}
//			}

//			if ($nodeHasGroups) $arrNodes2[] = $idNode;
//		}

		// This way is better for a large group of IdNodes


		XMD_Log::debug(sprintf(_('Debugging actions intersection with nodes - [%s]'), implode(', ', $nodes)));

		for ($i=0; $i<count($nodes); $i++) {

			$idNode = $nodes[$i];
			//First get actions no allowed
			$noActions = array();
			$sqlNoActions = "select IdAction From `NoActionsInNode` WHERE IdNode = {$idNode} order by IdAction ASC";

			$db->query($sqlNoActions);
			while (!$db->EOF) {
				$noActions[] = $db->getValue('IdAction');
				$db->next();
			}
			$noActions = implode(",", $noActions);

			$sqlNodeInfo = 'select n.idNode, n.idParent, ft.depth, n.idNodeType, n.name, IFNULL(n.idState, 0) as idState,
				nt.canAttachGroups
				from FastTraverse ft join Nodes n using(idNode) join NodeTypes nt using(idNodeType)
				where ft.idChild = %s
				order by ft.depth';

			$sqlNodeInfo = sprintf($sqlNodeInfo, $idNode);

			XMD_Log::debug(sprintf('sqlNodeInfo - [%s]', $sqlNodeInfo));

			$db->query($sqlNodeInfo);

			if ($db->EOF) {
				continue;
			}

			$arrNodeTypes[] = $db->getValue('idNodeType');

			$arrStates[] = $db->getValue('idState');

			$canAttachGroups = $db->getValue('canAttachGroups');
			$idParent = $db->getValue('idParent');
			$nodeHasGroups = true;

			while ($canAttachGroups != 1 && $nodeHasGroups) {
				$db->next();
				if (!$db->EOF) {
					$idNode = $db->getValue('idNode');
					$idParent = $db->getValue('idParent');
					$canAttachGroups = $db->getValue('canAttachGroups');
				} else {
					$nodeHasGroups = false;
				}
			}

			if ($nodeHasGroups) $arrNodes2[] = $idNode;
		}

		// At this point we have all idnodes needed for obtain the groups
		// plus a few necessary node attributes.

		// Find the roles of each group, wich depends on user and nodes groups. (3)

		// Used for actions intersection. (4)
		$roles = array();
		$sqlGroupsIntersection = 'select ug.idRole
			from RelUsersGroups ug join RelGroupsNodes gn using(idGroup)
			where ug.idUser = %s and gn.idNode in (%s)
			group by ug.idRole';

		$arrNodes2 = array_unique($arrNodes2);
		$sqlGroupsIntersection = sprintf($sqlGroupsIntersection, $idUser, implode(',', $arrNodes2));

		XMD_Log::debug(sprintf('sqlGroupsIntersection - [%s]', $sqlGroupsIntersection));

		$db->query($sqlGroupsIntersection);
		while (!$db->EOF) {
			$roles[] = $db->getValue('idRole');
			$db->next();
		}

		// ---------------------------- Step 1 -----------------------------


		// ---------------------------- Step 2 -----------------------------

		// Find the actions intersection:
		// 1) Actions depending on nodetypes.
		// 2) Actions depending on states.

		// We need to group de actions by command, module and params so the web interface
		// don't repeat the same action many times.

		// Used for actions intersection. (5)
		$commands = array();
		$arrNodeTypes = array_unique($arrNodeTypes);
		$strNodeTypes = implode(',', $arrNodeTypes);

		// This query finds the commands intersection (1)

		$sqlCommandIntersection = "select count(1) as c, Command,
			ifnull(Params, '') as aliasParams,
			ifnull(Module, '') as aliasModule
			from Actions
			where IdNodeType IN (%s) ";
			if(!empty($noActions) )
				$sqlCommandIntersection .= " AND IdAction NOT IN({$noActions}) ";

			$sqlCommandIntersection .= "group by Command, aliasParams, aliasModule
			having c = %s";
		$sqlCommandIntersection = sprintf($sqlCommandIntersection, $strNodeTypes, count($arrNodeTypes));

		XMD_Log::debug(sprintf('sqlCommandIntersection - [%s]', $sqlCommandIntersection));

		$db->query($sqlCommandIntersection);
		while (!$db->EOF) {
			$command = $db->getValue('Command');
			$commands[] = $command;
			$db->next();
		}

		// Now find the actions attributes depending on the commands intersection before,
		// the nodetypes, the roles and the node states
		// (1, 2, 4, 5)

		$actions = array();
		$sqlRolesActions = "select IdAction, idNodeType, Command, Name, Icon,
				ifnull(Params, '') as aliasParams,
				ifnull(Module, '') as aliasModule,
				%s,
				IsBulk
			from Actions a inner join RelRolesActions ra using(idAction)
			where idNodeType in (%s)
				and a.Command in ('%s')
				and Sort > 0 ";
		if (!empty($roles)) {
			$sqlRolesActions .=  sprintf(" and idRol in (%s) " , implode(',', $roles));
		}

		if (!empty($arrStates)) {
			$sqlRolesActions .=  sprintf(" and ifnull(idState, 0) in (%s) ", implode(',', $arrStates));
		}
		$sqlRolesActions .= " group by command, aliasParams, aliasModule
			order by Sort";

		$actionName = $processActionName === true && count($nodes) > 1
				? "concat(SUBSTRING_INDEX(name, ' ', 1), ' Selección') as Name"
				: 'name as Name';


		$sqlRolesActions = sprintf(
			$sqlRolesActions,
			$actionName,
			$strNodeTypes,
			implode("','", $commands)
		);

		XMD_Log::debug(sprintf('sqlRolesActions - [%s]', $sqlRolesActions));

		$db->query($sqlRolesActions);

		while (!$db->EOF) {

//			if ($action->getSort() && !$this->actionIsExcluded($db->getValue('IdAction'), $idNode)) {

				$actions[] = array(
					'actionid' => $db->getValue('IdAction'),
					'name' => _($db->getValue('Name')),
					'command' => $db->getValue('Command'),
					'icon' => $db->getValue('Icon'),
					'module' => $db->getValue('aliasModule'),
					'params' => $db->getValue('aliasParams'),
					'callback' => 'callAction',
					'bulk' => $db->getValue('IsBulk')
//					'desc' => $db->getValue('Description'),
				);
//			}
			$db->next();
		}

		// ---------------------------- Step 2 -----------------------------

		return $actions;
	}

	/**
	 *
	 */
	protected function actionIsExcluded($idAction, $idNode) {
		$node = new Node($idNode);
		$nodeTypeName = $node->nodeType->GetName();
		$ret = true;
		if ($nodeTypeName == 'XimletContainer') {
			$parent = new Node($node->GetParent());
			$nodeTypeNameParent = $parent->nodeType->GetName();
			$action = new Action($idAction);
			$command = $action->GetCommand();

			if ($nodeTypeNameParent == 'XimNewsColector' && $command == 'deletenode') {
				$ret = false;
			}
		}
		return $ret;
	}

}

