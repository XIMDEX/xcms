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

require_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/Request.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/persistence/Config.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/auth/Authenticator.class.php');
require_once(XIMDEX_ROOT_PATH . '/conf/stats.conf');
ModulesManager::file('/inc/i18n/I18N.class.php');

class Action_login extends ActionAbstract {

    function index() {
		if(isset($_COOKIE['expired'])){
			$this->showLogin('Your session has expired. Please, enter your data again.');
		}else {
			$this->showLogin();
		}
		$this->logSuccessAction();
    }

	function showLogin($msg = NULL) {
		$values = $this->getDefaultVars();
		$this->addJs("/xmd/js/browser_checker.js");

		if(!empty($msg) )
			$values["message"] = _($msg);
		else
			$values["message"] = NULL;

		//Login change if ximDEMOS is active and the Smarty exists.
		if (ModulesManager::isEnabled('ximDEMOS') && file_exists(XIMDEX_ROOT_PATH."/actions/login/template/Smarty/indexDEMO.tpl"))
		    $this->render($values, 'indexDEMO', 'only_template.tpl');
		else
		    $this->render($values, 'index', 'only_template.tpl');
		die();
	}

	function getDefaultVars() {
		$values = array();

		I18N::setup();
		$values["ximid"] = Config::getValue("ximid");
		$values["versionname"] = Config::getValue("VersionName");
		$values["news_content"] = $this->get_news();
		$values["title"] = sprintf(_("Access to %s"), Config::getValue("VersionName") );

		return $values;
	}

	function get_news() {
		$REMOTE_NEWS = STATS_SERVER."/stats/getnews.php";

		$ctx = stream_context_create(array(
			'http' => array(
				'timeout' => 3
				)
			)
		);

			//$url =
		$REMOTE_NEWS."?lang=".strtolower(DEFAULT_LOCALE)."&ximid=".Config::getValue('ximid');

		$url = $REMOTE_NEWS."?lang=".strtolower(DEFAULT_LOCALE);

		//get remote content
		$news_content = @file_get_contents($url, 0, $ctx);
		if(empty($news_content) ) {
			$file = "index_".strtolower(DEFAULT_LOCALE).".html";
			if (file_exists(XIMDEX_ROOT_PATH."/xmd/news/$file" ) )  {
				return file_get_contents(XIMDEX_ROOT_PATH."/xmd/news/$file");
			}else {
				return file_get_contents(XIMDEX_ROOT_PATH."/xmd/news/index.html");
			}
		}
		return $news_content;
	}

	function check() {
		$stopper = file_exists(Config::getValue("AppRoot") . Config::getValue("TempRoot") . "/login.stop");
		$user_lower = strtolower(Request::post('user'));
		$user = Request::post('user');
		$password = Request::post('password');
		$formsent = Request::post('login');

		$this->check_disk_space();
		setcookie("expired","",time()-3600);

		if (empty($user)  && empty($password)) {
			$this->showLogin('You should write a username and password.');
		} elseif(empty($user)) {
			$this->showLogin('You should write a username.');
		} elseif(empty($password)) {
			$this->showLogin('You should write a password.');
		}


		if ( 'ximdex'!= $user_lower && $stopper ) {
			$this->showLogin('Access blocked. At this moment maintenance tasks are being performed. Sorry for the inconveniences.');
		}

		$authenticator = new Authenticator();
		$success = $authenticator->login($user, $password);

		if ($success) {
			$userObject = new User();
			$userObject->setByLogin($user);
			$userObject->afterLogin();
			XSession::set('context', 'ximdex');
			$this->logSuccessAction();

			if (Request::get('backto')) {
				header(sprintf('Location: %s', base64_decode(Request::get('backto'))));
			}else {
					header(sprintf("Location: %s", Config::getValue('UrlRoot')));
			}

			die();

		} else {
			$this->logUnsuccessAction();
			$this->showLogin('Username or password are incorrect. Please, try again.');
		}
	}

	function check_disk_space() {
		$space = include(XIMDEX_ROOT_PATH."/conf/diskspace.conf");

		$critical_space = DiskUtils::transform($space["fatal_limit"], "MB");
		$space_now = DiskUtils::disk_free_space("MB");

		if($space_now < $critical_space) {
			$msg = sprintf(_("Without space in hard disk. Available only %sMB when it's need %sMB"), $space_now, $critical_space);
			$this->showLogin($msg);
		}
	}
}
