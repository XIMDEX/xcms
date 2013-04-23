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



ModulesManager::file('/inc/persistence/XSession.class.php');
ModulesManager::file('/inc/persistence/Config.class.php');
ModulesManager::file('/inc/dependencies/DepsManager.class.php');


class Action_showassocnodes extends ActionAbstract {

	const COMPOSER_INDEX = 'loadaction.php';

	// Server, Section
	const SEARCHED_NODETYPE = '5014,5015,5300';

	// Tree root
	const TREE_ROOT = 10000;


	public function index()
	{
      	$ximletId = (int) $this->request->getParam("nodeid");
		$actionID = (int) $this->request->getParam("actionid");
		$action = $this->request->getParam("action");

		$this->addJs('/actions/showassocnodes/resources/js/treeSelector.js');
		$this->addCss('/actions/showassocnodes/resources/css/index.css');

		$sections = $this->getReferencedSections($ximletId);

		$query = App::get('QueryManager');
        $actionDelete = $query->getPage() . $query->buildWith(array('method' => 'deleterel'));
        $actionCreate = $query->getPage() . $query->buildWith(array('method' => 'createrel'));

		$values = array(
			'ximletid' => $ximletId,
			'sections' => $sections,
			'treeroot' => self::TREE_ROOT,
			'searchednodetype' => self::SEARCHED_NODETYPE,
			'action_create' => $actionCreate,
			'action_delete' => $actionDelete
		);

		$this->render($values, null, 'default-3.0.tpl');
	}


	protected function getReferencedSections($idNode) {

    	$node = new Node($idNode);
    	$idContainer = $node->getParent();

		$sections = array();
		$depsMngr = new DepsManager();
		$sections1 = $depsMngr->getByTarget(DepsManager::STRDOC_XIMLET, $idNode);
		$sections1 = !is_array($sections1) ? array() : $sections1;
		$sections2 = $depsMngr->getByTarget(DepsManager::SECTION_XIMLET, $idContainer);
		$sections2 = !is_array($sections2) ? array() : $sections2;
		$sections3 = $depsMngr->getByTarget(DepsManager::SECTION_XIMLET, $idNode);
		$sections3 = !is_array($sections3) ? array() : $sections3;

		$sections = array_merge($sections1, $sections2, $sections3);

		$ret = array();

    	if (is_array($sections) && count($sections) > 0) {
    		foreach ($sections as $idsection) {
    			$section = new Node($idsection);
    			if (!($section->get('IdNode') > 0)) {
    				XMD_Log::warning(_("Ximlet with identity "). $idsection . _("has been deleted"));
    				continue;
    			}
    			$ret[$idsection]['path'] = str_replace('/', ' / ', $section->GetPath());
    			$ret[$idsection]['idsection'] = $idsection;
    		}
    	}

    	return $ret;
	}


	public function treecontainer() {

		$values = array(
			'composer_index' =>  "loadaction.php?actionid=$actionID&nodeid=$idNode",
			"debug" => $this->_checkDebug(),
		);

		$this->render($values, "treecontainer", "only_template.tpl");
	}

	public function tree() {

		$rootNode = new Node();
		$rootID = $rootNode->GetRoot();
		$rootNode->SetID($rootID);

		$values = array(
			'composer_index' => "loadaction.php?actionid=$actionID&nodeid=$idNode",
			'nodeName' => $rootNode->GetNodeName(),
			'nodeid' =>  $rootNode->GetID(),
			'nodeicon' => $rootNode->nodeType->GetIcon(),
		);

		$this->render($values, "tree", "only_template.tpl");
	}

	/**
	 * Find all children of a specified nodetype
	 */
	protected function getAllowedTargets($parentId) {

		$query = sprintf(
			"select n.IdNode from FastTraverse ft inner join Nodes n on ft.idchild = n.idnode " .
			"where ft.idnode = %s and n.idnodetype in (%s) order by depth",
			$parentId,
			self::SEARCHED_NODETYPE
		);
		$db = new DB();
		$db->query($query);
		$targets = array();

		while (!$db->EOF) {
			$targets[] = $db->getValue('IdNode');
			$db->next();
		}

		return $targets;
	}

	/**
	 * Only traverse sections
	 */
	function treedata() {
		$this->response->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
		$this->response->set('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
		$this->response->set('Cache-Control', array('no-store, no-cache, must-revalidate', 'post-check=0, pre-check=0'));
		$this->response->set('Pragma', 'no-cache');
		$this->response->set('Content-type', 'text/xml');
		$this->response->sendHeaders();

		echo '<?xml version="1.0" encoding="ISO-8859-1"?>';

		$selectedNodeID = $this->request->getParam('nodeid');
		$ximletId = $this->request->getParam('ximletid');
		$actionId = $this->request->getParam('actionid');
		$action = $this->request->getParam('action');
		$allow_nodetypes = explode(",",self::SEARCHED_NODETYPE);
		$selecteds = $this->getReferencedSections($ximletId);


		$src = "loadaction.php?actionid=$actionId&amp;ximletid=$ximletId&amp;method=treedata";

		$ximlet = new Node($ximletId);
		$idproject = $ximlet->getProject();

		$sections = $this->getAllowedTargets($idproject);

		// Drawing the tree

		echo '<tree>';

		if (count($sections) > 0) {

			$ancestors = array();

			foreach ($sections as $idSection) {
				$refDoc = new Node($idSection);
				$ancestors = array_merge($ancestors, $refDoc->getAncestors($idSection));
			}

			$ancestors = array_unique($ancestors);

			foreach ($ancestors as $childId) {

				$child = new Node($childId);
				$name = $child->get('Name');
				$isdir = $child->nodeType->isFolder();
				$icon = $child->nodeType->get('Icon');
				$nodeTypeName = $child->nodeType->get('Name');
				$idnodetype = $child->nodeType->get('IdNodeType');
				$path = $child->getPathList();
				$canSelected = (int) in_array($idnodetype, $allow_nodetypes);
				$already_selected = (isset($selecteds[$childId]) ) ? 1 : 0;

				$num = sizeof($child->getChildren());

				$showMenu = "showMenu='0'";

				if ($child->get('IdParent') == $selectedNodeID) {

					if (in_array($childId, $sections)) {
						$num = 0;
						$showMenu = "showMenu='1'";
					}

					echo "<tree action='showassocnodes' idaction='$actionId' text='$name' node='$childId' padre='1' nodeid='$childId'
							icon='$icon' idximlet='$ximletId' isdir='$isdir'
							openIcon='$icon' children='$num' src='$src&amp;nodeid=$childId' $showMenu
							nodetypeid='$idnodetype' path='$path' canSelected='$canSelected' already_selected='$already_selected' />";
				}
			}

		}

		echo '</tree>';
	}

	function createrel() {

		$idXimlet = $this->request->getParam('ximletid');
		$idTarget = $this->request->getParam('targetid');
		$recursive = $this->request->getParam('recursive');
		$sections = array($idTarget);

		if ($recursive == 1) {
			$children = $this->getAllowedTargets($idTarget);
			if (count($children) > 0) {
				$sections = array_unique(array_merge($sections, $children));
			}
		}

		$ximlet = new Node($idXimlet);
		$ximletName = $ximlet->get('Name');
		$deps = new DepsManager();

		foreach ($sections as $idSection) {

			$section = new Node($idSection);
			if (!($section->get('IdNode') > 0)) continue;

			$idnodetype = $section->get('IdNodeType');
			$rel = null;

			switch ($idnodetype) {
				case 5014:
				case 5015:
				case 5300:
					$rel = DepsManager::SECTION_XIMLET;
					break;
			}

			if ($rel === null) {
				// TODO: show error
				continue;
			}

			$result = $deps->set($rel, $idSection, $idXimlet);

			$sectionName = $section->get('Name');
			if ($result) {
				$this->messages->add(_("Ximlet") . $ximletName . _("has been associated with section") . $sectionName,
					MSG_TYPE_NOTICE);
			} else {
				$this->messages->add(_("Ximlet") . $ximletName . _("has not been associated with section") . $sectionName,
					MSG_TYPE_NOTICE);
			}
		}

		$values = array(
			'nodeid' => $idXimlet,
			'goback' => true,
			'messages' => $this->messages->messages
		);

		$this->render($values);
	}

	function deleterel() {

		$idXimlet = $this->request->getParam('ximletid');
		$sections = $this->request->getParam('sections');

		if (!is_array($sections) || count($sections) == 0) {
			$this->messages->add(_('No sections have been selected to be dissasociated.'), MSG_TYPE_NOTICE);
			$values = array(
				'messages' => $this->messages->messages,
				'goback' => true
			);
			$this->render($values);
			return;
		}

		$ximlet = new Node($idXimlet);
		$ximletName = $ximlet->get('Name');
		$depsMngr = new DepsManager();

		foreach ($sections as $idSection) {

			$section = new Node($idSection);
			if (!($section->get('IdNode') > 0)) continue;

			$idnodetype = $section->get('IdNodeType');
			$rel = null;

			switch ($idnodetype) {
				case 5014:
				case 5015:
				case 5300:
					$rel = DepsManager::SECTION_XIMLET;
					break;
				case 5312:
				case 5309;
					$rel = DepsManager::STRDOC_XIMLET;
					break;
			}

			if ($rel === null) {
				// TODO: show error
				continue;
			}

			$result = $depsMngr->delete($rel, $idSection, $idXimlet);
			$sectionName = $section->get('Name');
			if ($result) {
				$this->messages->add(_("Section ") . $sectionName . _("has been disassociated with ximLet") . $ximletName,
					MSG_TYPE_NOTICE);
			} else {
				$this->messages->add(_("Section ") . $sectionName . _("has not been disassociated with ximLet") . $ximletName,
					MSG_TYPE_NOTICE);
			}
		}

		$values = array(
			'messages' => $this->messages->messages,
			'goback' => true
		);

		$this->render($values);
	}

}
