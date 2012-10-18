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



ModulesManager::file('/inc/modules/Module.class.php');
ModulesManager::file('/inc/persistence/Config.class.php');
ModulesManager::file('/inc/fsutils/FsUtils.class.php');

class Module_ximSYNC extends Module {

	var $syncPaths;

	function Module_ximSYNC () {

		// Call Module constructor.
		parent::Module("ximSYNC", dirname (__FILE__));

		// Initialization stuff.
		$this->syncPaths = array (
			'channelframes' => 'pending',
			'serverframes' => 'pending'
		);
	}

	function install () {

        // Install logic.

        // ¿get module from ftp, webdav, subversion, etc...?
        // ¿need to be extracted?
        // extract and copy files to modules location.

        // get constructor SQL
		$this->loadConstructorSQL("ximSYNC.constructor.sql");

        // Install !
		$install_ret = parent::install();
		Shell::exec('php ' . XIMDEX_ROOT_PATH . '/script/orm/generate.php Servers');
		// Success
		$successInstall = true;

		// Create Action Menu Items
		if (!($this->createActionMenuItems())) {

			$this->messages->add (_("* ERROR: creating Action Menu Items."), MSG_TYPE_ERROR);
			$successInstall = false;
		} else {

			$this->messages->add (_("Action Menu Items created."), MSG_TYPE_NOTICE);
		}

		if (!$successInstall) {

			$this->messages->add (_("Undoing changes."), MSG_TYPE_NOTICE);
			$this->messages->add (_("* ERROR installing ximSYNC module. Aborting..."), MSG_TYPE_ERROR);
			$this->uninstall();
		}
		return $successInstall;
	}

	function createActionMenuItems () {

		return true;
	}

	function deleteActionMenuItems () {

		return true;
	}

	function preInstall () {

		$ret = $this->checkDependences (array ());

		if (!is_null ($ret)) {

			$this->messages->add (sprintf(_("* ERROR: dependence '%s' not found"), $ret), MSG_TYPE_ERROR);
			return false;
		} else {

			return true;
		}
	}

	function uninstall() {

		// Sync directories created
		$this->syncPaths = array (
			'channelframes' => 'created',
			'serverframes' => 'created'
		);

		// Uninstall logic.
		$this->deleteActionMenuItems();

        // get destructor SQL
		$this->loadDestructorSQL("ximSYNC.destructor.sql");
		Shell::exec('php ' . XIMDEX_ROOT_PATH . '/script/orm/generate.php Servers');

        // Uninstall!
		parent::uninstall();
	}
}
?>
