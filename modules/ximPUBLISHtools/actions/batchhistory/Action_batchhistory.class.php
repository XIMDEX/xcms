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
use Ximdex\Utils\Session;

//ModulesManager::file('/inc/manager/ServerFrameManager.class.php');
//ModulesManager::file('/inc/model/PublishingReport.class.php', 'ximSYNC');

class Action_batchhistory extends ActionAbstract {

    // Main method: shows initial form
    function index() {
        $acceso = true;
        // Initializing variables.
        $userID = Session::get('userID');

        $user = new User();
        $user->SetID($userID);

        if (!$user->HasPermission("view_publication_resume")) {
            $acceso = false;
            $errorMsg = "You have not access to this report. Consult an administrator.";
        }


        $jsFiles = array(
            App::getValue('UrlRoot') . ModulesManager::path('ximPUBLISHtools') . '/actions/managebatchs/resources/js/index.js',
//            App::getValue('UrlRoot') . '/inc/js/ximtimer.js'
        );

        $cssFiles = array(
//            App::getValue('UrlRoot') . ModulesManager::path('ximPUBLISHtools') . '/actions/managebatchs/resources/css/index.css'
        );

        $arrValores = array(
            'acceso' => $acceso,
            'errorBox' => $errorMsg,
            'js_files' => $jsFiles,
            'css_files' => $cssFiles
        );

        $this->render($arrValores, NULL, 'default-3.0.tpl');
    }

}

?>
