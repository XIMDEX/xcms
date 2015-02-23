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
 * @version $Revision$
 */


ModulesManager::file("/actions/browser3/Action_browser3.class.php");


class Action_welcome extends ActionAbstract
{
    // Main method: shows the initial form
    function index()
    {
        $values = array();
        if ( !isset($_REQUEST['actionReload']) || $_REQUEST['actionReload'] != 'true') {
            if ($this->tourEnabled(\Ximdex\Utils\Session::get("userID"), "welcome")) {
                $values[] = $this->addJs('/resources/js/start_tour.js', 'ximTOUR');
            }
            if (ModulesManager::isEnabled('ximTOUR')) {
                $values[] = $this->addJs('/actions/welcome/resources/js/tour.js');
                $values[] = $this->addJs('/resources/js/tour.js', 'ximTOUR');
            }
            $this->addCss('/actions/welcome/resources/css/welcome.css');
        }
            $this->addJs('/actions/welcome/resources/js/index.js');

        $this->addJs('/actions/welcome/resources/js/index.js');

        //Getting idaction to check Create new project permissions for user
        $user = new User(\Ximdex\Utils\Session::get("userID"));
        $idNodeRoot = 10000;
        $action = new Action();
        $action->setByCommandAndModule("addfoldernode", $idNodeRoot);
        $permissionsToCreateProject = $user->isAllowedAction($idNodeRoot, $action->get("IdAction"));

        $values["permissionsToCreateProject"] = $permissionsToCreateProject;
        $values["projects_info"] = \Ximdex\Services\Project::getProjectsInfo();
        $values["user"] = \Ximdex\Utils\Session::get("user_name");
        $values["docs"] = $user->getLastestDocs();
        $this->render($values, "index.tpl", 'default-3.0.tpl');
    }
}

