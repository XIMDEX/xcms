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


include_once '../../bootstrap/start.php';


require(App::getValue('XIMDEX_ROOT_PATH') . '/conf/log.conf');
include_once(App::getValue('XIMDEX_ROOT_PATH') . "/inc/utils.php");
ModulesManager::file("/inc/persistence/XSession.class.php");
ModulesManager::file('/inc/i18n/I18N.class.php');

XSession::check();

$locale = XSession::get('locale');
// Check coherence with HTTP_ACCEPT_LANGUAGE
I18N::setup($locale);
$userID = XSession::get('userID');

header('Content-type: application/javascript');

echo "\nrenderer = '" . XSession::get("renderer") . "';";
echo "\nurl_root = '" . Config::getValue('UrlRoot') . "';";
echo "\nximdex_root = '" . App::getValue('XIMDEX_ROOT_PATH') . "';";
echo "\nbase_action = '" . XSession::get("base_action") . "';";
echo "\nurl_root = '" . Config::getValue('UrlRoot') . "';";
echo "\napp_root = '" . Config::getValue('AppRoot') . "';";
echo "\nuser_id = '" . XSession::get('userID') . "';";
echo "\nlocale = '" . XSession::get('locale') . "';";
$load_welcome = (int)(ModulesManager::isEnabled("ximDEMOS") && XSession::get('user_demo'));
echo "\nload_welcome =" . $load_welcome . ";";
