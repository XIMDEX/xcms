<?php

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)                                *
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>          *
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

require_once(XIMDEX_ROOT_PATH . "/inc/auth/Authenticator.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/persistence/Config.class.php");
ModulesManager::file('/inc/model/NodeEdition.class.php');
ModulesManager::file("/inc/persistence/XSession.class.php");

class Action_logout extends ActionAbstract
{

    function index()
    {
        $userID = (int)XSession::get('userID');
        $nodeEdition = new NodeEdition();
        $nodeEdition->deleteByUser($userID);
        $authenticator = new Authenticator();
        $authenticator->logout();
        header(sprintf("Location: %s/", Config::getValue('UrlRoot')));
    }

}