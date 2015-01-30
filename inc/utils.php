<?php
// DONT USE THIS FILE IS HIGHLY DEPRECATED.

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

if (!defined('XIMDEX_ROOT_PATH'))
        define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../'));


//  Se incluyen las otras librerias de utilidades a partir de utils.php, que es incluida por todo el sistema.

ModulesManager::file('/conf/install-params.conf.php');
ModulesManager::file('/conf/install-modules.conf');
ModulesManager::file('/inc/db/db.php');
ModulesManager::file("/inc/persistence/datafactory.php");
ModulesManager::file("/inc/sync/synchro.php");
ModulesManager::file("/inc/workflow/Workflow.class.php");
ModulesManager::file("/inc/model/user.php");
ModulesManager::file("/inc/model/group.php");
ModulesManager::file("/inc/model/role.php");
ModulesManager::file("/inc/model/permissions.php");
ModulesManager::file("/inc/model/channel.php");
ModulesManager::file("/inc/model/language.php");
ModulesManager::file("/inc/model/node.php");
ModulesManager::file("/inc/model/nodetype.php");
ModulesManager::file("/inc/model/action.php");
ModulesManager::file("/inc/model/structureddocument.php");
ModulesManager::file("/inc/log/Action_log.class.php");
ModulesManager::file("/inc/persistence/XSession.class.php");
ModulesManager::file("/inc/mvc/Request.class.php");