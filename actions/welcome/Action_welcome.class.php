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


ModulesManager::file("/services/ProjectService.class.php");
ModulesManager::file("/actions/browser3/Action_browser3.class.php");


class Action_welcome extends ActionAbstract {
    // Main method: shows the initial form
    function index () {
		$values=array();
		if ($_REQUEST['actionReload'] != 'true' && $this->tourEnabled(XSession::get("userID"), "welcome")){
			$values[] = $this->addJs('/resources/js/start_tour.js','ximTOUR');
        }
		if (ModulesManager::isEnabled('ximTOUR')){
			$values[] = $this->addJs('/actions/welcome/resources/js/tour.js');
			$values[] = $this->addJs('/resources/js/tour.js','ximTOUR');			
		}
		$user = new User(XSession::get("userID"));
		$this->addJs('/actions/welcome/resources/js/index.js');
		$this->addCss('/actions/welcome/resources/css/welcome.css');

	 $permissionsToCreateProject=false;
                $idNodeRoot = 10000;
                $actionBrowser3 = new Action_browser3();
                $arrayPermissions = $actionBrowser3->getActionsOnNodeList(XSession::get("userID"), array($idNodeRoot));
                if ($arrayPermissions && is_array($arrayPermissions) && count($arrayPermissions)){

                        foreach($arrayPermissions as $permission){
                                if ($permission["command"] == "addfoldernode"){

                                        $permissionsToCreateProject = true;
                                        break;
                                }
                        }
                }

        $values["permissionsToCreateProject"] = $permissionsToCreateProject;
        $values["projects_info"]=ProjectService::getProjectsInfo();
        $values["user"]=XSession::get("user_name");
        $values["docs"]=$user->getLastestDocs();
	    $this->render($values, "index.tpl", 'default-3.0.tpl');
	}
}
?>
