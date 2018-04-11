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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision: 8735 $
 */

use Ximdex\Logger;
use Ximdex\Models\Action;
use Ximdex\Models\Node;
use Ximdex\Models\NodeSets;
use Ximdex\Models\SearchFilters;
use Ximdex\Models\User;
use Ximdex\Models\XimLocale;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Request;
use Ximdex\Utils\Serializer;
use Ximdex\Runtime\Session;

\Ximdex\Modules\Manager::file('/actions/browser3/inc/search/QueryProcessor.class.php');
\Ximdex\Modules\Manager::file('/actions/browser3/inc/GenericDatasource.class.php');
\Ximdex\Modules\Manager::file('/actions/browser3/inc/FormValidation.class.php');

class Action_browser3 extends ActionAbstract
{

    const CSS_PATH = '/actions/browser3/resources/css';
    const JS_PATH = '/actions/browser3/resources/js';
    // Used previously for session cache
    const ACTIONS_INTERSECTION = 'browser_actions_intersection';

    public function index()
    {
        if (!is_string( Session::get('activeTheme'))) {
            Session::set('activeTheme', 'ximdex_theme');
        }

        $params = $this->request->getParam('params');
        $loginName = Session::get('user_name');
        $userID = (int) Session::get('userID');
 
        $locale = new XimLocale();
        $user_locale = $locale->GetLocaleByCode(Session::get('locale'));
        $locales = $locale->GetEnabledLocales();

        $values = array(
            'params' => $params,
            'userID' => $userID,
            'time_id' => time() . "_" . Session::get('userID'), /* For uid for scripts */
            'loginName' => $loginName,
            'user_locale' => $user_locale,
            'locales' => $locales,
            'xinversion' => App::getValue("VersionName")
        );

        $this->addCss('/assets/style/fonts.css');
        $this->addCss('/assets/style/jquery/smoothness/jquery-ui-1.8.2.custom.css');
        $this->addCss('/vendors/bootstrap/dist/css/bootstrap.min.css');
        $this->addCss('/vendors/ladda/dist/ladda-themeless.min.css');
        $this->addCss('/vendors/humane/flatty.css');
        $this->addCss('/assets/style/jquery/ximdex_theme/widgets/treeview/treeview.css');
        $this->addActionCss('browser.css');
        $this->addCss('/resources/css/tour.css', 'ximTOUR');


        //Old browserwindow styles
        $this->addCss('/assets/style/jquery/ximdex_theme/widgets/browserwindow/actionPanel.css');
        $this->addCss('/assets/style/jquery/ximdex_theme/widgets/browserwindow/browserwindow.css');
        $this->addCss('/assets/style/jquery/ximdex_theme/widgets/browserwindow/icons.css');

        //Old hbox styles
        $this->addCss('/assets/style/jquery/ximdex_theme/widgets/hbox/hbox.css');

        //Old tabs styles
        $this->addCss('/assets/style/jquery/ximdex_theme/widgets/tabs/common_views.css');
        $this->addCss('/assets/style/jquery/ximdex_theme/widgets/tabs/tabs-container.css');
        $this->addCss('/assets/style/jquery/ximdex_theme/widgets/tabs/tabs.css');

        $this->addJs('/assets/js/helpers.js');
        $this->addJs('/assets/js/collection.js');
        $this->addJs('/assets/js/dialogs.js');
        $this->addJs('/assets/js/console.js');
        $this->addJs('/assets/js/sess.js');
        $this->addJs('/assets/js/eventHandler.js');
        $this->addJs(Extensions::JQUERY);
        $this->addJs(Extensions::JQUERY_UI);
        $this->addJs('/assets/js/i18n.js');
        $this->addJs('/vendors/hammerjs/hammer.js/hammer.js');
        $this->addJs('/vendors/angular/angular.min.js');
        $this->addJs('/vendors/react/react-with-addons.min.js');
        $this->addJs('/vendors/react/ngReact.min.js');
        $this->addJs('/vendors/RyanMullins/angular-hammer/angular.hammer.js');
        $this->addJs('/vendors/angular/angular-animate.min.js');
        $this->addJs('/vendors/angular/angular-sanitize.min.js');
        $this->addJs('/vendors/angular-ui-sortable/src/sortable.js');
        $this->addJs('/vendors/ladda/dist/spin.min.js');
        $this->addJs('/vendors/ladda/dist/ladda.min.js');
        $this->addJs('/vendors/humane/humane.min.js');
        $this->addJs(XIMDEX_VENDORS . '/flow/ng-flow-standalone.min.js');
        $this->addJs('/vendors/angular-bootstrap/dist/ui-bootstrap-custom-tpls-0.13.0-SNAPSHOT.min.js');
        $this->addJs(Extensions::JQUERY_PATH . '/ui/jquery-ui-timepicker-addon.js');
        $this->addJs(Extensions::JQUERY_PATH . '/ui/jquery.ui.dialog.min.js');
        $this->addJs(Extensions::JQUERY_PATH . '/plugins/jquery-validate/jquery.validate.js');
        $this->addJs(Extensions::JQUERY_PATH . '/plugins/jquery-validate/localization/messages_' . $user_locale["Lang"] . '.js');
        $this->addActionJs('ximdex.form.validation.js');
        $this->addJs(Extensions::JQUERY_PATH . '/plugins/jquery.json/jquery.json-2.2.min.js');
        $this->addJs(Extensions::JQUERY_PATH . '/plugins/jquery.labelwidth/jquery.labelwidth.js');
        $this->addJs(Extensions::JQUERY_PATH . '/plugins/jquery-file-upload/js/jquery.fileupload.js');
        $this->addJs(Extensions::JQUERY_PATH . '/plugins/jquery-file-upload/js/jquery.fileupload-process.js');
        $this->addJs(Extensions::JQUERY_PATH . '/plugins/jquery-file-upload/js/jquery.fileupload-angular.js');
        $this->addJs('/vendors/d3js/d3.v3.min.js');
        $this->addJs('/vendors/codemirror/Codemirror/lib/codemirror.js');

        //Old browserwindow js
        $this->addJs('/src/Widgets/browserwindow/js/browserwindow.js');
        $this->addJs('/src/Widgets/browserwindow/js/dialogs.js');
        $this->addJs('/src/Widgets/browserwindow/js/actions.js');

        //Old listview js
        $this->addJs('/src/Widgets/listview/js/fix.jquery.events.js');
        $this->addJs('/src/Widgets/listview/js/listview.js');
        $this->addJs('/src/Widgets/listview/js/listviewRenderer_Columns.js');
        $this->addJs('/src/Widgets/listview/js/listviewRenderer_Details.js');
        $this->addJs('/src/Widgets/listview/js/listviewRenderer_Grid.js');
        $this->addJs('/src/Widgets/listview/js/listviewRenderer_Icon.js');
        $this->addJs('/src/Widgets/listview/js/listviewRenderer_List.js');
        $this->addJs('/src/Widgets/listview/js/selections.js');
        $this->addJs('/src/Widgets/listview/js/jquery.fixheadertable.js');


        $this->addJs('/assets/js/angular/app.js');
        $this->addJs('/assets/js/angular/animations/slide.js');
        $this->addJs('/assets/js/angular/services/xTranslate.js');
        $this->addJs('/assets/js/angular/services/xBackend.js');
        $this->addJs('/assets/js/angular/services/xUrlHelper.js');
        $this->addJs('/assets/js/angular/services/xEventRelay.js');
        $this->addJs('/assets/js/angular/services/xDialog.js');
        $this->addJs('/assets/js/angular/services/xCheck.js');
        $this->addJs('/assets/js/angular/services/xMenu.js');
        $this->addJs('/assets/js/angular/services/xTabs.js');
        $this->addJs('/assets/js/angular/services/angularLoad.js');
        $this->addJs('/assets/js/angular/directives/ximButton.js');
        $this->addJs('/assets/js/angular/directives/ximSelect.js');
        $this->addJs('/assets/js/angular/directives/ximValidators.js');
        $this->addJs('/assets/js/angular/directives/xtagsSuggested.js');
        $this->addJs('/assets/js/angular/directives/contenteditable.js');
        $this->addJs('/assets/js/angular/directives/ximFile.js');
        $this->addJs('/assets/js/angular/directives/ximUploader.js');
        $this->addJs('/assets/js/angular/directives/ximFocusOn.js');
        $this->addJs('/assets/js/angular/directives/rightClick.js');
        $this->addJs('/assets/js/angular/directives/ximGrid.js');
        $this->addJs('/assets/js/angular/directives/ximInverted.js');
        $this->addJs('/assets/js/angular/directives/ximFitText.js');
        $this->addJs('/assets/js/angular/directives/ximMenu.js');
        $this->addJs('/assets/js/angular/directives/ximTree.js');
        $this->addJs('/assets/js/angular/directives/ximList.js');
        $this->addJs('/assets/js/angular/directives/ximBrowser.js');
        $this->addJs('/assets/js/angular/directives/datepicker.js');
        $this->addJs('/assets/js/angular/directives/ximTabs.js');
        $this->addJs('/assets/js/angular/directives/treeAssocNodes.jsx.js');
        $this->addJs('/assets/js/angular/directives/treeNode.jsx.js');

        $this->addJs('/assets/js/angular/filters/xFilters.js');
        $this->addJs('/assets/js/angular/controllers/XTagsCtrl.js');
        $this->addJs('/assets/js/angular/controllers/XModifyUserGroupsCtrl.js');
        $this->addJs('/assets/js/angular/controllers/XModifyGroupUsersCtrl.js');
        $this->addJs('/assets/js/angular/controllers/XModifyStates.js');
        $this->addJs('/assets/js/angular/controllers/XModifyStatesRole.js');
        $this->addJs('/assets/js/angular/controllers/AdvancedSearchModalCtrl.js');
        $this->addJs('/assets/js/angular/controllers/AssocNodesCtrl.js');
        $this->addJs('/assets/js/angular/controllers/XSetExtensions.js');
        $this->addJs('/assets/js/angular/controllers/ximPUBLISHtools.js');
        $this->addJs('/assets/js/angular/controllers/XUserMenuCtrl.js');
        $this->addJs('/assets/js/angular/directives/ximAssocNodes.js');
        $this->addActionJs('XMainCtrl.js');
        $this->addActionJs('controller.js');

        /*         * ********************************** SPLASH ************************************** */
        define("REMOTE_WELCOME", STATS_SERVER . "/stats/getsplash.php");
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 2
                )
            )
        );

        $url = REMOTE_WELCOME . "?lang=" . strtolower(\Ximdex\Runtime\Session::get("locale"));
        //get remote content
        $splash_content = @file_get_contents($url, 0, $ctx);
        if (!empty($splash_content)) {
            $values["splash_content"] = $splash_content;
            $values["splash_file"] = null;
        } elseif (file_exists(APP_ROOT_PATH . "/actions/browser3/template/Smarty/splash/index.tpl")) {
            $values["splash_content"] = null;
            $values["splash_file"] = APP_ROOT_PATH . "/actions/browser3/template/Smarty/splash/index.tpl";
        } else {
            $values["splash_content"] = "Sorry, splash image temporarily unavaliable.";
            $values["splash_file"] = null;
        }

        /*         * ************************************************************************************* */

        $this->render($values, 'index', 'only_template.tpl');
    }

    public function addActionCss($css)
    {
        parent::addCss(sprintf('%s/%s', Action_browser3::CSS_PATH, $css));
    }

    public function addActionJs($js)
    {
        parent::addJs(sprintf('%s/%s', Action_browser3::JS_PATH, $js));
    }

    /**
     * Refresh the session regenerating the session ID and cookie
     *
     */
    public function refreshSession()
    {
        \Ximdex\Runtime\Session::refresh();
    }

    /**
     * Returns templates for actions panel
     */
    public function actionTemplate()
    {

        $template = $this->request->getParam('template');
        $template = sprintf('actionPanel%s', ($template === null ? 'Main' : ucfirst(strtolower($template))));

        $values = array();

        $this->render($values, $template, 'only_template.tpl');
    }

    /**
     * Returns a JSON object with the allowed nodetypes for searches
     */
    public function nodetypes()
    {
        $ret = GenericDatasource::nodetypes($this->request);
        $this->sendJSON($ret);
    }

    /**
     * Returns a JSON document with all parents of the specified node id
     */
    public function parents()
    {
        $ret = GenericDatasource::parents($this->request);
        $this->sendJSON($ret);
    }

    /**
     * Returns a JSON document with all children of the specified node id
     */
    public function read()
    {
        $ret = GenericDatasource::quickRead($this->request);
        $ret['collection'] = $this->checkNodeAction($ret['collection']);
        if ($this->request->getParam('nodeid') == "10000") {
            $ret["name"] = _($ret["name"]);
        }
        header('Content-type: application/json');
        $data = Serializer::encode(SZR_JSON, $ret);
        echo $data;
    }

    /**
     * Check if the nodes have associated actions
     */
    /**
     * @param $nodes
     * @return null
     */
    protected function checkNodeAction(&$nodes)
    {

        $db = new \Ximdex\Runtime\Db();
        $sql = 'select count(1) as total from Actions a left join Nodes n using(IdNodeType) where IdNode = %s and a.Sort > 0';
        $sql2 = $sql . " AND a.Command='fileupload_common_multiple' ";

        if (!empty($nodes)) {
            foreach ($nodes as &$node) {
                $nodeid = $node['nodeid'];
                $_sql = sprintf($sql, $nodeid);

                $db->query($_sql);
                $total = $db->getValue('total');
                $node['hasActions'] = $total;


                $db = new \Ximdex\Runtime\Db();
                $sql2 = sprintf($sql2, $nodeid);
                $db->query($sql2);
                $total = $db->getValue('total');
                $node['canUploadFiles'] = $total;
            }

            return $nodes;
        } else {
            Logger::info(_('Empty nodes in checkNodeAction [ browser3 ]'));
            return null;
        }
    }

    /**
     * Returns a JSON document with all children of the specified node id
     * filtered by the filter param
     */
    /**
     *
     */
    public function readFiltered()
    {
        $query = $this->request->getParam('query');

        $this->request->setParam('find', $query);
        $ret = GenericDatasource::readFiltered($this->request);
        $ret['collection'] = $this->checkNodeAction($ret['collection']);

        header('Content-type: application/json');
        $data = Serializer::encode(SZR_JSON, $ret);
        echo $data;
    }

    /**
     * Instantiates a QueryHandler based on the "handler" parameter and does
     * a search with the "query" parameter options.
     * The "query" parameter could be a XML or JSON string
     */
    /**
     *
     */
    public function search()
    {

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
    /**
     * @param $handler
     * @param $output
     * @param $query
     * @return mixed
     */
    protected function _search($handler, $output, $query)
    {

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

        if ("SQLTREE" != $handler) {
            $queryHandler = QueryProcessor::getInstance($handler);
            $query = $queryHandler->getQueryOptions($query);

            $ret['query'] = $query;
            $ret = $this->resultsHierarchy($view, isset($query['parentid']) ? $query['parentid'] : null, $ret, $queryHandler);
        } else {

            return $ret;
        }

        return $ret;
    }

    protected function resultsHierarchy($view, $parentId, $results, $handler)
    {

        if ($view != 'treeview')
            return $results;

        $results = $results['data'];
        $data = array();

        foreach ($results as $item) {

            $node = new Node($item['nodeid']);
            if (!($node->get('IdNode') > 0))
                continue;

            $ancestors = $node->getAncestors();
            $p = null;
            $i = 0;
            $count = count($ancestors);

            while ($p === null && $i < $count) {
                $id = $ancestors[$i];
                if ($id == $parentId) {
                    $p = $ancestors[$i + 1];
                }
                $i++;
            }

            if ($p !== null)
                $data[] = $p;
        }

        $data = array_unique($data);

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

    protected function sendXML($data)
    {
        header('Content-type: text/xml');
        echo $data;
    }

    /**
     * Writes data on the configured datasource
     */
    public function write()
    {
        $ret = GenericDatasource::write($this->request);
        $this->sendJSON($ret);
    }

    // TODO: Change my name, extend me, do something with validations....

    /**
     * Returns a JSON object with all the node sets
     */
    public function listSets()
    {

        $idUser = \Ximdex\Runtime\Session::get('userID');

        $sets = array();
        $it = NodeSets::getSets($idUser);
        while ($set = $it->next()) {
            /**
             * @var $set NodeSets
             */
            $sets[] = array(
                'id' => $set->getId(),
                'name' => $set->getName(),
                'items' => $set->getItems(),
            );
        }

        $this->sendJSON($sets);
    }

    // ----- Sets management -----

    /**
     * Returns a JSON object with all related nodes of a node set
     */
    public function getSet()
    {

        $setid = $this->request->getParam('setid');
        $set = new NodeSets($setid);

        $nodes = array();
        $it = $set->getNodes();
        while ($node = $it->next()) {
            /**
             * @var $node Node
             */
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
    public function addSet()
    {

        $name = $this->request->getParam('name');
        $nodes = $this->request->getParam('nodes');
        $nodes = GenericDatasource::normalizeEntities($nodes);
        $users = $this->request->getParam('users');
        $name = $this->validateFieldName($name);

        if ($name === false) {
            $this->sendJSON(
                array(array('type' => MSG_TYPE_ERROR, 'message' => _('The set name cannot be empty.')))
            );
            return;
        }

        $set = NodeSets::create($name);
        $errors = $set->messages->messages;

        if ($set->getId() > 0 && $nodes) {
            $ret = $this->addNodeToSet($set->getId(), $nodes);
            $errors = array_merge($errors, $ret);
        }

        $sessionUser = \Ximdex\Runtime\Session::get('userID');
        $errors = array_merge(
            $errors, $this->addUserToSet(
            $set->getId(), $sessionUser, RelNodeSetsUsers::OWNER_YES
        )
        );

        if ($set->getId() > 0 && $users) {
            $ret = $this->addUserToSet($set->getId(), $users);
            $errors = array_merge($errors, $ret);
        }

        $this->sendJSON($errors);
    }

    public function validateFieldName($name)
    {
        $name = trim($name);
        if (strlen($name) == 0) {
            $name = false;
        }
        return $name;
    }

    /**
     * Adds multiple nodes to a specific node set.
     * The nodes parameter must by an array of node ids
     */
    /**
     * @param null $idSet
     * @param null $nodes
     * @return array
     */
    public function addNodeToSet($idSet = null, $nodes = null)
    {
        $result = array();

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
            if ($rel->getId() > 0)
                $addedNodes++;
            $errors = array_merge($errors, $rel->messages->messages);
        }
        $errors = array_merge(
            array(array('type' => MSG_TYPE_NOTICE, 'message' => _("Nodes has been added correctly.") . $addedNodes)), $errors
        );

        if ($returnJSON) {
            $this->sendJSON($errors);
        } else {
            return $errors;
        }
        return $result ;
    }

    /**
     * Adds multiple users to a specific node set.
     * The users parameter must by an array of user ids
     */
    /**
     * @param null $idSet
     * @param null $users
     * @param int $owner
     * @return array
     */
    public function addUserToSet($idSet = null, $users = null, $owner = RelNodeSetsUsers::OWNER_NO)
    {
        $result = array();
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
                if ($rel->getId() > 0)
                    $addedUsers++;
                $errors = array_merge($errors, $rel->messages->messages);
            }
        }
        $errors = array_merge(
            array(array('type' => MSG_TYPE_NOTICE, 'message' => _("Users have been added correctly.") . $addedUsers)), $errors
        );

        if ($returnJSON) {
            $this->sendJSON($errors);
        } else {
            return $errors;
        }
        return $result ;
    }

    /**
     * Deletes a node set
     */
    public function deleteSet()
    {
        $setid = $this->request->getParam('setid');
        $set = new NodeSets($setid);
        $set->delete();
        $this->sendJSON($set->messages->messages);
    }

    /**
     * Renames a node set
     */
    public function renameSet()
    {
        $setid = $this->request->getParam('setid');
        $name = $this->request->getParam('name');
        $name = $this->validateFieldName($name);
        if ($name === false) {
            $this->sendJSON(
                array(array('type' => MSG_TYPE_ERROR, 'message' => _('The set name cannot be empty.')))
            );
            return;
        }
        $set = new NodeSets($setid);
        $set->Name = $name;
        $set->update();
        $this->sendJSON($set->messages->messages);
    }

    /**
     * Deletes multiple nodes from a specific node set.
     * The nodes parameter must by an array of node ids
     */
    public function deleteNodeFromSet()
    {
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
            if (count($rel->messages->messages) == 0)
                $deletedNodes++;
            $errors = array_merge($errors, $rel->messages->messages);
        }
        $errors = array_merge(
            array(array('type' => MSG_TYPE_NOTICE, 'message' => _("Nodes have been deleted successfully.") . $deletedNodes)), $errors
        );
        $this->sendJSON($errors);
    }

    /**
     * Deletes multiple users from a specific node set.
     * The users parameter must by an array of user ids
     */
    public function deleteUserFromSet()
    {

        $sessionUser = \Ximdex\Runtime\Session::get('userID');
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
                !($sessionUser->getOwner() == RelNodeSetsUsers::OWNER_NO &&
                    $user->getOwner() == RelNodeSetsUsers::OWNER_YES)
                ) {

                    $rel = $set->deleteUser($idUser);
                    if (count($rel->messages->messages) == 0)
                        $deletedUsers++;
                    $errors = array_merge($errors, $rel->messages->messages);
                }
            }
        }
        $errors = array_merge(
            array(array('type' => MSG_TYPE_NOTICE, 'message' => _("Users have been deleted successfully.") . $deletedUsers)), $errors
        );
        $this->sendJSON($errors);
    }

    /**
     * Updates the associated users of a set.
     */
    public function updateSetUsers()
    {
        $idSet = $this->request->getParam('setid');
        // $users = $this->request->getParam('users');
        $rel = new RelNodeSetsUsers();
        $rel->deleteAll('IdSet = %s and Owner = 0', array($idSet));
        $this->addUserToSet();
    }

    /**
     * Return all users in the system except the current one.
     * If setid parameter is present, the users in this set will be tagged as "selected".
     */
    public function getUsers()
    {

        $sessionUser = \Ximdex\Runtime\Session::get('userID');
        $idSet = $this->request->getParam('setid');

        $ret = array();
        $aux = array();

        $users = new User();
        $users = $users->find(ALL, 'IdUser <> %s', array($sessionUser));

        if ($users) {
            foreach ($users as $user) {
                $idUser = $user['IdUser'];
                $ret[] = array(
                    'id' => $idUser,
                    'login' => $user['Login'],
                    'name' => $user['Name'],
                    'selected' => false,
                    'owner' => null,
                );
                $aux[$idUser] = &$ret[count($ret) - 1];
            }
        }

        if (!empty($idSet)) {

            $users = new RelNodeSetsUsers();
            $users = $users->find(ALL, 'IdSet = %s', array($idSet));
            
            if ($users) {
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
    public function listFilters()
    {

        $filters = array();
        $it = SearchFilters::getFilters();
        while ($filter = $it->next()) {
            /**
             * @var $filter  SearchFilters
             */
            $filters[] = array(
                'id' => $filter->getId(),
                'name' => $filter->getName(),
                'filter' => json_decode($filter->getFilter())
            );
        }

        $this->sendJSON($filters);
    }

    /**
     * Returns a JSON object with all related nodes of a filter
     */
    public function getFilter()
    {

        $filterid = $this->request->getParam('filterid');
        $output = $this->request->getParam('output');
        $output = $output !== null ? $output : 'JSON';

        $filter = new SearchFilters($filterid);
        if ($filter->getId() <= 0) {
            $this->sendJSON(
                array(array('type' => MSG_TYPE_ERROR, 'message' => _("The filter ") . $filterid . _("does not exists.")))
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
    public function addFilter()
    {

        $name = $this->request->getParam('name');
        $name = $this->validateFieldName($name);
        if ($name === false) {
            $this->sendJSON(
                array(array('type' => MSG_TYPE_ERROR, 'message' => _('The filter name cannot be empty.')))
            );
            return;
        }

        $filter = $this->request->getParam('filter');
        if ($filter === false || !is_array($filter) || count($filter) == 0) {
            $this->sendJSON(
                array(array('type' => MSG_TYPE_ERROR, 'message' => _('The filter cannot be empty.')))
            );
            return;
        }

        $handler = $this->request->getParam('handler');
        $handler = $this->validateFieldName($handler);
        if ($handler === false) {
            $this->sendJSON(
                array(array('type' => MSG_TYPE_ERROR, 'message' => _('The filter handler cannot be empty.')))
            );
            return;
        }


        $filter = SearchFilters::create($name, $handler, $filter);
        $this->sendJSON($filter->messages->messages);
    }

    /**
     * Deletes a filter
     */
    public function deleteFilter()
    {
        $filterid = $this->request->getParam('filterid');
        $filter = new SearchFilters($filterid);
        $filter->delete();
        $this->sendJSON($filter->messages->messages);
    }

    /**
     * Renames a filter
     */
    public function renameFilter()
    {
        $filterid = $this->request->getParam('filterid');
        $name = $this->request->getParam('name');
        $name = $this->validateFieldName($name);
        if ($name === false) {
            $this->sendJSON(
                array(array('type' => MSG_TYPE_ERROR, 'message' => _('The filter name cannot be empty.')))
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
    public function actions()
    {
        $nodes = $this->request->getParam('nodes');
        $nodes = GenericDatasource::normalizeEntities($nodes);
        $actions = $this->getActions($nodes);
        $this->sendJSON($actions);
    }

    /**
     * Calculates the posible actions for a group of nodes.
     * @param array $nodes IdNodes array
     * @return array IdActions array
     */
    protected function getActions($nodes = null)
    {
        $idUser = \Ximdex\Runtime\Session::get('userID');
        $nodes = $nodes !== null ? $nodes : $this->request->getParam('nodes');
        if (!is_array($nodes))
        {
            $nodes = array($nodes);
        }
        $actions=[6000];

        $actions += $this->getActionsOnNodeList($idUser, $nodes);

        // Users can modify their account
        if (is_array($nodes) && count($nodes) == 1 && $nodes[0] == $idUser && !in_array(6002, $actions))
        {
            $actions[] = 6002;
        }
        return $actions;
    }

    /**
     * Calculates the posible actions for a group of nodes.
     * It depends on roles, states and nodetypes of nodes.
     * @param int $idUser Current user.
     * @param array $nodes IdNodes array.
     * @return array IdActions array.
     */
    public function getActionsOnNodeList($idUser, $nodes, $processActionName = true)
    {
        unset($processActionName);
        $user = new User($idUser);
        return $user->getActionsOnNodeList($nodes);
    }

    /**
     * Returns an intersection of sets on a group of nodes.
     */
    public function nodesets()
    {
        $nodes = $this->request->getParam('nodes');
        $nodes = GenericDatasource::normalizeEntities($nodes);
        $sets = $this->getSetsIntersection($nodes);
        $this->sendJSON($sets);
    }

    /**
     * Create contextual menu options for delete nodes from sets
     */
    /**
     * @param null $nodes
     * @return array
     */
    protected function getSetsIntersection($nodes = null)
    {

        $nodes = $nodes !== null ? $nodes : $this->request->getParam('nodes');
        $nodes = !is_array($nodes) ? array() : array_unique($nodes);

        // Calculate which sets need to be shown (intersection)
        $sql = 'select count(1) as c, r.IdSet, s.Name
			from RelNodeSetsNode r left join NodeSets s on s.Id = r.IdSet
			where r.IdNode in (%s)
			group by r.IdSet, s.Name
			having c = %s';
        $db = new \Ximdex\Runtime\Db();
        $db->query(sprintf($sql, implode(',', $nodes), count($nodes)));

        $data = array();
        while (!$db->EOF) {
            $data[] = array(
                'id' => $db->getValue('IdSet'),
                'name' => sprintf('Delete from set "%s"', $db->getValue('Name')),
                'icon' => 'delete_section.png',
                'callback' => 'deleteFromSet'
            );
            $db->next();
        }

        return $data;
    }

    /**
     * Get the action params for a node list in frontend.
     * Returns a contextual menu data, composed by actions and sets.
     */
    public function cmenu()
    {
        $nodes = $this->request->getParam('nodes');
        $nodes = GenericDatasource::normalizeEntities($nodes);
        $sets = $this->getSetsIntersection($nodes);
        $actions = $this->getActions($nodes);
        $arrayActionsParams = array();
        
        //For every action, build the params for json response
        foreach ($actions as $idAction)
        {
            $actionsParamsAux = array();
            $action = new Action($idAction);
            $name = $action->get("Name");

            //Changing name when node sets
            if (count($nodes) > 1)
            {
                $auxName = explode(" ", $name);
                $name = $auxName[0] . " " . _("selection");
            }
            $actionsParamsAux["id"] = $action->get("IdAction") ? $action->get("IdAction") : "";
            $actionsParamsAux["name"] = $name;
            $actionsParamsAux["module"] = $action->get("Module") ? $action->get("Module") : "";
            $actionsParamsAux["params"] = $action->get("Params") ? $action->get("Params") : "";
            $actionsParamsAux["command"] = $action->get("Command");
            $actionsParamsAux["icon"] = $action->get("Icon");
            $actionsParamsAux["callback"] = "callAction";
            $actionsParamsAux["bulk"] = $action->get("IsBulk");
            $arrayActionsParams[] = $actionsParamsAux;
        }
        $options = array_merge($sets, $arrayActionsParams);
        $this->sendJSON($options);
    }

    /**
     * Launch a validation from the params values.
     */
    public function validation()
    {
        $request = $this->request->getRequests();
        $method = $this->request->getParam('validationMethod');
        if (empty($method))
        {
            $request_content = file_get_contents("php://input");
            $request = (array)json_decode($request_content);
            if (array_key_exists('validationMethod', $request))
            {
                $method = $request['validationMethod'];
            }
        }
        if (method_exists("FormValidation", $method))
        {
            FormValidation::$method($request);
        }
        die("false");
    }

    /**
     * Disables the tour pop-up
     */
    function disableTour()
    {
        $numRep = $this->request->getParam('numRep');
        App::setValue('ximTourRep', $numRep, true);
        $result["success"] = true;
        $this->sendJSON($result);
    }

    // ----- Nodes contextual menus -----

    /**
     * Return preferences like MaxItemsPerGroup as JSON
     */
    function getPreferences()
    {
        $res["preferences"] = array("MaxItemsPerGroup" => App::getValue("MaxItemsPerGroup"));
        $this->sendJSON($res);
    }
	
    protected function actionIsExcluded($idAction, $idNode)
    {
        $node = new Node($idNode);
        $nodeTypeName = $node->nodeType->GetName();
        $ret = true;
        if ($nodeTypeName == 'XimletContainer') {
            $parent = new Node($node->GetParent());
            $nodeTypeNameParent = $parent->nodeType->GetName();
            $action = new Action($idAction);
            $command = $action->GetCommand();

            if ( $command == 'deletenode') {
                $ret = false;
            }
        }
        return $ret;
    }
}