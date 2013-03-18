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
 *  @version $Revision: 8401 $
 */


ModulesManager::file('/inc/model/locale.inc');
ModulesManager::file('/inc/search/QueryProcessor.class.php');
ModulesManager::file('/inc/xvfs/XVFS.class.php');
ModulesManager::file('/inc/serializer/Serializer.class.php');
ModulesManager::file('/inc/model/NodeSets.class.php');
ModulesManager::file('/inc/model/SearchFilters.class.php');
ModulesManager::file('/actions/browser3/inc/GenericDatasource.class.php');
ModulesManager::file('/inc/model/ActionsStats.class.php');


class Action_browser3 extends ActionAbstract {

	const CSS_PATH = '/actions/browser3/resources/css';
	const JS_PATH = '/actions/browser3/resources/js';

	// Used previously for session cache
	const ACTIONS_INTERSECTION = 'browser_actions_intersection';

	public function index() {
		if (!is_string(XSession::get('activeTheme'))) {
			XSession::set('activeTheme', 'ximdex_theme');
		}

		$params = $this->request->getParam('params');
		$loginName = XSession::get('user_name');
		$userID = (int) XSession::get('userID');

		/*Test Session*/
		$session_info = session_get_cookie_params();
		$session_lifetime = $session_info['lifetime']; // session cookie lifetime in seconds
		//$session_duration = session_cache_expire(); // in minutes
		$session_duration = $session_lifetime != 0 ? $session_lifetime : session_cache_expire() * 60;
		
		$sessionExpirationTimestamp = XSession::get("loginTimestamp") + $session_duration*60;
		setcookie("loginTimestamp",XSession::get("loginTimestamp"));
		setcookie("sessionLength", $session_duration); 
		/**/

		$locale = new XimLocale();
		$user_locale = $locale->GetLocaleByCode(XSession::get('locale'));
		$locales = $locale->GetEnabledLocales();

		$values = array(
			'params' => $params,
			'time_id'	=> time()."_".XSession::get('userID'), /* For uid for scripts */
			'loginName' => $loginName,
			'user_locale' => $user_locale,
			'locales' => $locales,
			'xinversion' => Config::getValue("VersionName")
		);

		$this->addCss('/xmd/style/jquery/smoothness/jquery-ui-1.8.2.custom.css');
		$this->addActionCss('browser.css');
		if(ModulesManager::isEnabled('ximTOUR'))
			$this->addCss('/modules/ximTOUR/resources/css/tour.css');


		if(ModulesManager::isEnabled('ximADM') ) {
			$time_id = time()."_".$userID;

			$this->addJs('/utils/user_connect.js.php?id='.$time_id.'&lang='.$user_locale["Lang"], 'ximADM');
		}

		$this->addJs('/inc/js/helpers.js');		
		$this->addJs('/inc/js/collection.js');
		$this->addJs('/inc/js/dialogs.js');
		$this->addJs('/inc/js/console.js');
		$this->addJs('/inc/js/sess.js');
		$this->addJs('/inc/js/eventHandler.js');
		$this->addJs(Extensions::JQUERY);
                $this->addJs(Extensions::JQUERY_UI);
                $this->addJs(Extensions::JQUERY_PATH.'/ui/jquery.ui.tabs.min.js');
                $this->addJs(Extensions::JQUERY_PATH.'/ui/jquery.ui.dialog.min.js');
                $this->addJs(Extensions::JQUERY_PATH.'/plugins/jquery-validate/jquery.validate.js');
                $this->addJs(Extensions::JQUERY_PATH.'/plugins/jquery-validate/localization/messages_'.$user_locale["Lang"].'.js');
                $this->addJs(Extensions::JQUERY_PATH.'/plugins/jquery.json/jquery.json-2.2.min.js');
                $this->addJs(Extensions::JQUERY_PATH.'/plugins/jquery.labelwidth/jquery.labelwidth.js');
		$this->addActionJs('controller.js');


		/* *********************************** SPLASH ************************************** */
		define("REMOTE_WELCOME", STATS_SERVER."/stats/getsplash.php");
		$ctx = stream_context_create(array(
			'http' => array(
				'timeout' =>2 
				)
			)
		);

		//$url = REMOTE_WELCOME."?lang=".strtolower(XSession::get("locale"))."&ximid=".Config::getValue('ximid');
		$url = REMOTE_WELCOME."?lang=".strtolower(XSession::get("locale"));
		//get remote content
		$splash_content = @file_get_contents($url, 0, $ctx);
		if(!empty($splash_content) ) {
			$values["splash_content"] = $splash_content;
			$values["splash_file"] = null;
		}elseif (file_exists(XIMDEX_ROOT_PATH."/actions/browser3/template/Smarty/splash/index.tpl")) {
			$values["splash_content"] = null;
			$values["splash_file"] = XIMDEX_ROOT_PATH."/actions/browser3/template/Smarty/splash/index.tpl" ;
		}
		else{
			$values["splash_content"] = "Sorry, splash image temporarily unavaliable.";
                        $values["splash_file"] = null;
		}
		/* ************************************************************************************** */


		$this->render($values, 'index', 'only_template.tpl');
	}

	/**
	 * Refresh the session regenerating the session ID and cookie
	 *
	 */
	public function refreshSession() {
		XSession::refresh();	
	}
	
	/**
	 * Returns templates for actions panel
	 */
	public function actionTemplate() {

		$template = $this->request->getParam('template');
		$template = sprintf('actionPanel%s', ($template === null ? 'Main' : ucfirst(strtolower($template))));

		$values = array();

		$this->render($values, $template, 'only_template.tpl');
	}

	public function addActionCss($css) {
		parent::addCss(sprintf('%s/%s', Action_browser3::CSS_PATH, $css));
	}

	public function addActionJs($js) {
		parent::addJs(sprintf('%s/%s', Action_browser3::JS_PATH, $js));
	}

//	protected function sendJSON($data) {
//		$data = Serializer::encode(SZR_JSON, $data);
//		header('Content-type: application/json');
//		echo $data;
//	}

	protected function sendXML($data) {
		header('Content-type: text/xml');
		echo $data;
	}

	/**
	 * Returns a JSON object with the allowed nodetypes for searches
	 */
	public function nodetypes() {
		$ret = GenericDatasource::nodetypes($this->request);
		$this->sendJSON($ret);
	}

	/**
	 * Returns a JSON document with all parents of the specified node id
	 */
	public function parents() {
		$ret = GenericDatasource::parents($this->request);
		$this->sendJSON($ret);
	}

	/**
	 * Returns a JSON document with all children of the specified node id
	 */
	public function read() {



		$idNode = $this->request->getParam('nodeid');
		$items = $this->request->getParam('items');
		$path = XIMDEX_ROOT_PATH .ModulesManager::path('tolDOX').'/resources/cache/';
		$file = sprintf('%s%s_%s', $path, str_replace('/', '_', $idNode), $items);

		$modeTags = false;
		if (preg_match('/\/Tags/', $idNode) > 0) {
			$modeTags = true;
			if (is_file($file)) {
				$data = FsUtils::file_get_contents($file);
				echo $data;
				return;
			}
		}

		$ret = GenericDatasource::read($this->request);
		$ret['collection'] = $this->checkNodeAction($ret['collection']);

		header('Content-type: application/json');
		$data = Serializer::encode(SZR_JSON, $ret);		
		if ($modeTags) {
			FsUtils::file_put_contents($file, $data);
			
		}



		echo $data;
	}

	/**
	 * Check if the nodes have associated actions
	 */
	protected function checkNodeAction(&$nodes) {

		$db = new DB();
		$sql = 'select count(1) as total from Actions a left join Nodes n using(IdNodeType) where IdNode = %s and a.Sort > 0';
		$sql2 = $sql." AND a.Command='fileupload_common_multiple' ";

		if(!empty($nodes) ) {
			foreach ($nodes as &$node) {
				$nodeid = $node['nodeid'];
				$_sql = sprintf($sql, $nodeid);

				$db->query($_sql);
				$total = $db->getValue('total');
				$node['hasActions'] = $total;


				$db = new DB();
				$sql2 = sprintf($sql2, $nodeid);
				$db->query($sql2);
				$total = $db->getValue('total');
				$node['canUploadFiles'] = $total;
			}

			return $nodes;
		}else {
			XMD_Log::info(_('Empty nodes in checkNodeAction [ browser3 ]'));
			return null;
		}
	}

	/**
	 * Instantiates a QueryHandler based on the "handler" parameter and does
	 * a search with the "query" parameter options.
	 * The "query" parameter could be a XML or JSON string
	 */
	public function search() {

		$handler = strtoupper($this->request->getParam('handler'));
		$handler = empty($handler) ? 'SQL' : $handler;
		$output = strtoupper($this->request->getParam('output'));
		$output = empty($output) ? 'JSON' : $output;
		$query = $this->request->getParam('query');

		$ret = $this->_search($handler, $output, $query);		
		if ($output == 'JSON') {
			$this->sendJSON($ret);
		} else {
			$this->sendXML($ret);
		}
	}

	

	/**
	 * Instantiates a QueryHandler based on the "handler" parameter and does
	 * a search with the "query" parameter options.
	 * The "query" parameter could be a XML or JSON string
	 */
	protected function _search($handler, $output, $query) {

		$request = new Request();
		$request->setParameters(array(
			'handler' => $handler,
			'output' => $output,
			'query' => $query,
			'filters' => $this->request->getParam('filters')
		));

		// By default "listview", used only when it's "treeview"
		$view = isset($query['view']) ? $query['view'] : null;

		$ret = GenericDatasource::search($request);

		if("SQLTREE" != $handler) {
			$handler = QueryProcessor::getInstance($handler);
			$query = $handler->getQueryOptions($query);

			$ret['query'] = $query;
			$ret = $this->resutlsHierarchy($view, $query['parentid'], $ret, $handler);
		}else {


			return $ret;
		}

		return $ret;
	}

	protected function resutlsHierarchy($view, $parentId, $results, $handler) {

		if ($view != 'treeview') return $results;

		$results = $results['data'];
		$data = array();

		foreach ($results as $item) {

			$node = new Node($item['nodeid']);
			if (!($node->get('IdNode') > 0)) continue;

			$ancestors = $node->getAncestors();
			$p = null;
			$i = 0;
			$count = count($ancestors);

			while ($p === null && $i < $count) {
				$id = $ancestors[$i];
				if ($id == $parentId) {
					$p = $ancestors[$i+1];
				}
				$i++;
			}

			if ($p !== null) $data[] = $p;
		}

		$data = array_unique($data);
//debug::log($data);

		$query = array(
			'parentid' => $parentId,
			'depth' => '0',
			'items' => '50',
			'page' => '1',
			'view' => 'treeview',
			'condition' => 'and',
			'filters' => array(
				array(
					'field' => 'nodeid',
					'content' => $data,
					'comparation' => 'in'
				)
			),
			'sorts' => array()
        );

		$results = $handler->search($query);

		return $results;
	}

	/**
	 * Writes data on the configured datasource
	 */
	public function write() {
		$ret = GenericDatasource::write($this->request);
		$this->sendJSON($ret);
	}

	// TODO: Change my name, extend me, do something with validations....
	public function validateFieldName($name) {
		$name = trim($name);
		if (strlen($name) == 0) {
			$name = false;
		}
		return $name;
	}

	// ----- Sets management -----

	/**
	 * Returns a JSON object with all the node sets
	 */
	public function listSets() {

		$idUser = XSession::get('userID');

		$sets = array();
		$it = NodeSets::getSets($idUser);
		while ($set = $it->next()) {
			$sets[] = array(
				'id' => $set->getId(),
				'name' => $set->getName(),
				'items' => $set->getItems(),
			);
		}

		$this->sendJSON($sets);
	}

	/**
	 * Returns a JSON object with all related nodes of a node set
	 */
	public function getSet() {

		$setid = $this->request->getParam('setid');
		$set = new NodeSets($setid);

		$nodes = array();
		$it = $set->getNodes();
		while ($node = $it->next()) {
			$node = $node->getNode();
			$nodes[] = array(
				'nodeid' => $node->get('IdNode'),
				'text' => $node->get('Name'),
				'icon' => $node->nodeType->get('Icon'),
				'isdir' => $node->nodeType->isFolder() ? '1' : '0',
				'path' => $node->getPath(),
			);
		}

		$this->sendJSON($nodes);
	}

	/**
	 * Creates a new node set
	 */
	public function addSet() {

		$name = $this->request->getParam('name');
		$nodes = $this->request->getParam('nodes');
		$nodes = GenericDatasource::normalizeEntities($nodes);
		$users = $this->request->getParam('users');
		$name = $this->validateFieldName($name);

		if ($name === false) {
			$this->sendJSON(
				array(array('type'=>MSG_TYPE_ERROR, 'message'=>_('The set name cannot be empty.')))
			);
			return;
		}

		$set = NodeSets::create($name);
		$errors = $set->messages->messages;

		if ($set->getId() > 0 && $nodes !== null) {
			$ret = $this->addNodeToSet($set->getId(), $nodes);
			$errors = array_merge($errors, $ret);
		}

		$sessionUser = XSession::get('userID');
		$errors = array_merge(
			$errors, $this->addUserToSet(
				$set->getId(),
				$sessionUser,
				RelNodeSetsUsers::OWNER_YES
			)
		);

		if ($set->getId() > 0 && $users !== null) {
			$ret = $this->addUserToSet($set->getId(), $users);
			$errors = array_merge($errors, $ret);
		}

		$this->sendJSON($errors);
	}

	/**
	 * Deletes a node set
	 */
	public function deleteSet() {
		$setid = $this->request->getParam('setid');
		$set = new NodeSets($setid);
		$set->delete();
		$this->sendJSON($set->messages->messages);
	}

	/**
	 * Renames a node set
	 */
	public function renameSet() {
		$setid = $this->request->getParam('setid');
		$name = $this->request->getParam('name');
		$name = $this->validateFieldName($name);
		if ($name === false) {
			$this->sendJSON(
				array(array('type'=>MSG_TYPE_ERROR, 'message'=>_('The set name cannot be empty.')))
			);
			return;
		}
		$set = new NodeSets($setid);
		$set->Name = $name;
		$set->update();
		$this->sendJSON($set->messages->messages);
	}

	/**
	 * Adds multiple nodes to a specific node set.
	 * The nodes parameter must by an array of node ids
	 */
	public function addNodeToSet($idSet=null, $nodes=null) {

		$returnJSON = false;
		if ($idSet === null && $nodes === null) {
			$returnJSON = true;
			$idSet = $this->request->getParam('setid');
			$nodes = $this->request->getParam('nodes');
		}

		if (!is_array($nodes)) {
			$nodes = array($nodes);
		}
		$nodes = GenericDatasource::normalizeEntities($nodes);

		$addedNodes = 0;
		$errors = array();
		$set = new NodeSets($idSet);
		foreach ($nodes as $idNode) {
			$rel = $set->addNode($idNode);
			if ($rel->getId() > 0) $addedNodes++;
			$errors = array_merge($errors, $rel->messages->messages);
		}
		$errors = array_merge(
			array(array('type'=>MSG_TYPE_NOTICE, 'message'=>_("Nodes has been added correctly.") . $addedNodes)),
			$errors
		);

		if ($returnJSON) {
			$this->sendJSON($errors);
		} else {
			return $errors;
		}
	}

	/**
	 * Deletes multiple nodes from a specific node set.
	 * The nodes parameter must by an array of node ids
	 */
	public function deleteNodeFromSet() {
		$setid = $this->request->getParam('setid');
		$nodes = $this->request->getParam('nodes');
		if (!is_array($nodes)) {
			$nodes = array($nodes);
		}
		$nodes = GenericDatasource::normalizeEntities($nodes);
		$deletedNodes = 0;
		$errors = array();
		$set = new NodeSets($setid);
		foreach ($nodes as $idNode) {
			$rel = $set->deleteNode($idNode);
			if (count($rel->messages->messages) == 0) $deletedNodes++;
			$errors = array_merge($errors, $rel->messages->messages);
		}
		$errors = array_merge(
			array(array('type'=>MSG_TYPE_NOTICE, 'message'=>_("Nodes have been deleted successfully.") . $deletedNodes)),
			$errors
		);
		$this->sendJSON($errors);
	}

	/**
	 * Adds multiple users to a specific node set.
	 * The users parameter must by an array of user ids
	 */
	public function addUserToSet($idSet=null, $users=null, $owner=RelNodeSetsUsers::OWNER_NO) {

		$returnJSON = false;
		if ($idSet === null && $users === null) {
			$returnJSON = true;
			$idSet = $this->request->getParam('setid');
			$users = $this->request->getParam('users');
		}

		if (!is_array($users)) {
			$users = array($users);
		}
		$addedUsers = 0;
		$errors = array();

		$set = new NodeSets($idSet);
		foreach ($users as $idUser) {
			if (!empty($idUser) && $idUser > 0) {
				$rel = $set->addUser($idUser, $owner);
				if ($rel->getId() > 0) $addedUsers++;
				$errors = array_merge($errors, $rel->messages->messages);
			}
		}
		$errors = array_merge(
			array(array('type'=>MSG_TYPE_NOTICE, 'message'=>_("Users have been added correctly.") . $addedUsers)),
			$errors
		);

		if ($returnJSON) {
			$this->sendJSON($errors);
		} else {
			return $errors;
		}
	}

	/**
	 * Deletes multiple users from a specific node set.
	 * The users parameter must by an array of user ids
	 */
	public function deleteUserFromSet() {

		$sessionUser = XSession::get('userID');
		$setid = $this->request->getParam('setid');
		$users = $this->request->getParam('users');

		if (!is_array($users)) {
			$users = array($users);
		}

		$sessionUser = RelNodeSetsUsers::getByUserId($setid, $sessionUser);

		$deletedUsers = 0;
		$errors = array();
		$set = new NodeSets($setid);
		foreach ($users as $idUser) {

			// Don't delete my own set subscription
			if ($idUser != $sessionUser->getIdUser()) {

				$user = RelNodeSetsUsers::getByUserId($setid, $idUser);
				// Don't allow a not owner to delete the owner subscription
				if (
					!($sessionUser->getOwner() == RelNodeSetsUsers::OWNER_NO
					&&
					$user->getOwner() == RelNodeSetsUsers::OWNER_YES)
					) {

					$rel = $set->deleteUser($idUser);
					if (count($rel->messages->messages) == 0) $deletedUsers++;
					$errors = array_merge($errors, $rel->messages->messages);
				}
			}
		}
		$errors = array_merge(
			array(array('type'=>MSG_TYPE_NOTICE, 'message'=>_("Users have been deleted successfully.") . $deletedUsers)),
			$errors
		);
		$this->sendJSON($errors);
	}

	/**
	 * Updates the associated users of a set.
	 */
	public function updateSetUsers() {
		$idSet = $this->request->getParam('setid');
		$users = $this->request->getParam('users');
		$rel = new RelNodeSetsUsers();
		$rel->deleteAll('IdSet = %s and Owner = 0', array($idSet));
		$this->addUserToSet();
	}

	/**
	 * Return all users in the system except the current one.
	 * If setid parameter is present, the users in this set will be tagged as "selected".
	 */
	public function getUsers() {

		$sessionUser = XSession::get('userID');
		$idSet = $this->request->getParam('setid');

		$ret = array();
		$aux = array();

		$users = new User();
		$users = $users->find(ALL, 'IdUser <> %s', array($sessionUser));

		if ($users !== null) {
			foreach ($users as $user) {
				$idUser = $user['IdUser'];
				$ret[] = array(
					'id' => $idUser,
					'login' => $user['Login'],
					'name' => $user['Name'],
					'selected' => false,
					'owner' => null,
				);
				$aux[$idUser] =& $ret[count($ret)-1];
			}
		}

		if (!empty($idSet)) {

			$users = new RelNodeSetsUsers();
			$users = $users->find(ALL, 'IdSet = %s', array($idSet));

			if ($users !== null) {
				foreach ($users as $user) {
					$idUser = $user['IdUser'];
					if (isset($aux[$idUser])) {
						$aux[$idUser]['selected'] = true;
						$aux[$idUser]['owner'] = $user['Owner'] == 1 ? true : false;
					}
				}
			}
		}

		$this->sendJSON($ret);
	}

	// ----- Sets management -----

	// ----- Filters management -----

	/**
	 * Returns a JSON object with all the node filters
	 */
	public function listFilters() {

		$filters = array();
		$it = SearchFilters::getFilters();
		while ($filter = $it->next()) {
			$filters[] = array(
				'id' => $filter->getId(),
				'name' => $filter->getName()
			);
		}

		$this->sendJSON($filters);
	}

	/**
	 * Returns a JSON object with all related nodes of a filter
	 */
	public function getFilter() {

		$filterid = $this->request->getParam('filterid');
		$output = $this->request->getParam('output');
		$output = $output !== null ? $output : 'JSON';

		$filter = new SearchFilters($filterid);
		if ($filter->getId() <= 0) {
			$this->sendJSON(
				array(array('type'=>MSG_TYPE_ERROR, 'message'=>_("The filter ") . $filterid . _("does not exists.")))
			);
			return;
		}

		$query = $filter->getFilter();
		$handler = $filter->getHandler();
		$ret = $this->_search($handler, $output, $query);

		if ($output == 'JSON') {
			$this->sendJSON($ret);
		} else {
			$this->sendXML($ret);
		}
	}

	/**
	 * Creates a new filter
	 */
	public function addFilter() {

		$name = $this->request->getParam('name');
		$name = $this->validateFieldName($name);
		if ($name === false) {
			$this->sendJSON(
				array(array('type'=>MSG_TYPE_ERROR, 'message'=>_('The filter name cannot be empty.')))
			);
			return;
		}

		$filter = $this->request->getParam('filter');
		$filter = $this->validateFieldName($filter);
		if ($filter === false) {
			$this->sendJSON(
				array(array('type'=>MSG_TYPE_ERROR, 'message'=>_('The filter cannot be empty.')))
			);
			return;
		}

		$handler = $this->request->getParam('handler');
		$handler = $this->validateFieldName($handler);
		if ($handler === false) {
			$this->sendJSON(
				array(array('type'=>MSG_TYPE_ERROR, 'message'=>_('The filter handler cannot be empty.')))
			);
			return;
		}


		$filter = SearchFilters::create($name, $handler, $filter);
		$this->sendJSON($filter->messages->messages);
	}

	/**
	 * Deletes a filter
	 */
	public function deleteFilter() {
		$filterid = $this->request->getParam('filterid');
		$filter = new SearchFilters($filterid);
		$filter->delete();
		$this->sendJSON($filter->messages->messages);
	}

	/**
	 * Renames a filter
	 */
	public function renameFilter() {
		$filterid = $this->request->getParam('filterid');
		$name = $this->request->getParam('name');
		$name = $this->validateFieldName($name);
		if ($name === false) {
			$this->sendJSON(
				array(array('type'=>MSG_TYPE_ERROR, 'message'=>_('The filter name cannot be empty.')))
			);
			return;
		}
		$filter = new SearchFilters($filterid);
		$filter->Name = $name;
		$filter->update();
		$this->sendJSON($filter->messages->messages);
	}

	// ----- Filters management -----


	// ----- Nodes contextual menus -----


	/**
	 * Returns an instersection of actions on a group of nodes.
	 */
	public function actions() {
		$nodes = $this->request->getParam('nodes');
		$nodes = GenericDatasource::normalizeEntities($nodes);
		$actions = $this->getActions($nodes);
		$this->sendJSON($actions);
	}

	/**
	 * Returns an intersection of sets on a group of nodes.
	 */
	public function nodesets() {
		$nodes = $this->request->getParam('nodes');
		$nodes = GenericDatasource::normalizeEntities($nodes);
		$sets = $this->getSetsIntersection($nodes);
		$this->sendJSON($sets);
	}

	/**
	 * Returns a contextual menu data, composed by actions and sets.
	 */
	public function cmenu() {
		$tolDOX = ModulesManager::isEnabled('tolDOX');
		$processActionName = !$tolDOX;

		$nodes = $this->request->getParam('nodes');
//		$paths = $nodes;
		$nodes = GenericDatasource::normalizeEntities($nodes);
		$sets = $this->getSetsIntersection($nodes);
		$actions = $this->getActions($nodes, $processActionName);

//		$backend = XVFS::_getBackend($paths[0]);

		// workaround
//		if (strtolower(get_class($backend)) == strtolower('XVFS_Backend_tol')) {
		if ($tolDOX) {
 			$actions[] = array(
 				'name' => _('Crear Toldoc'), 'command' => 'createdocument', 'icon' => 'create_server.png',
 				'module' => 'tolDOX', 'params' => '', 'callback' => 'callAction', 'bulk' => '0'
 			);
 			$actions[] = array(
 				'name' => _('Eliminar Toldoc'), 'command' => 'deletenode', 'icon' => 'create_server.png',
 				'module' => '', 'params' => '', 'callback' => 'callAction', 'bulk' => '1'
 			);
		}
		// workaround

		$options = array_merge($sets, $actions);

		foreach($options as $key => $value) {
			$options[$key]['params'] = urlencode($options[$key]['params']);
		}

		$this->sendJSON($options);
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
		$sqlRolesActions = "select idNodeType, Command, Name, Icon,
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
				? "concat(SUBSTRING_INDEX(name, ' ', 1), ' Selecci�n') as Name"
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
//					'actionid' => $db->getValue('IdAction'),
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

	/**
	 * Create contextual menu options for delete nodes from sets
	 */
	protected function getSetsIntersection($nodes=null) {

		$nodes = $nodes !== null ? $nodes : $this->request->getParam('nodes');
		$nodes = !is_array($nodes) ? array() : array_unique($nodes);

		// Calculate which sets need to be shown (intersection)
		$sql = 'select count(1) as c, r.IdSet, s.Name
			from RelNodeSetsNode r left join NodeSets s on s.Id = r.IdSet
			where r.IdNode in (%s)
			group by r.IdSet, s.Name
			having c = %s';
		$db = new DB();
		$db->query(sprintf($sql, implode(',', $nodes), count($nodes)));

		$data = array();
		while (!$db->EOF) {
			$data[] = array(
				'id' => $db->getValue('IdSet'),
				'name' => sprintf('Delete from set "%s"', $db->getValue('Name')),
				'icon' => 'delete_section.png',
//				'setName' => $db->getValue('Name'),
				'callback' => 'deleteFromSet'
			);
			$db->next();
		}

		return $data;
//		$this->sendJSON($data);
	}

	// ----- Nodes contextual menus -----

}

?>
