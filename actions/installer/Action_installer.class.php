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

//
require_once(XIMDEX_ROOT_PATH . "/inc/auth/Authenticator.class.php");
//


class Action_installer extends ActionAbstract {

	function index() {
		$install_params = file_exists(XIMDEX_ROOT_PATH . '/conf/install-params.conf.php');
		$install_modules = file_exists(XIMDEX_ROOT_PATH .'/conf/install-modules.conf');

		$values = array();
		$this->checkConfigFiles($install_params, $install_modules);

		I18N::setup();
		$values["ximid"] = \App::getValue( "ximid");
		$values["versionname"] = \App::getValue( "VersionName");
		$values["install_params"] = (int) $install_params;
		$values["install_modules"] = (int) $install_modules;
		$values["db_connection"] = (int) DB_CONNECTION;
		$this->render($values, 'index', 'only_template.tpl');
		die();
	 }

	function checkConfigFiles($install_params = 0, $install_modules = 0) {

		if ($install_params && $install_modules && DB_CONNECTION ) {
			header(sprintf("Location: %s", \App::getValue( 'UrlRoot')));
			die();
		}
	}
}
