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
ModulesManager::file('/inc/io/BaseIO.class.php');
ModulesManager::file('/inc/cli/CliParser.class.php');
ModulesManager::file('/inc/cli/CliReader.class.php');
ModulesManager::file('/inc/model/RelRolesActions.class.php');
ModulesManager::file('/inc/model/role.inc');
ModulesManager::file('/inc/model/node.inc');
ModulesManager::file('/inc/utils.inc');
ModulesManager::file('/inc/model/orm/RelRolesStates_ORM.class.php');
ModuleSManager::file(MODULE_XIMLOADER_PATH.'/Module_ximLOADER.class.php');

class Module_ximTOUR extends Module {
    
    	function Module_ximTOUR() {
		// Call Module constructor.
            parent::Module('ximTOUR', dirname(__FILE__));

	}

        //Function which installs the module
	function install() {
		echo "\nModule ximTOUR requires Picasso project. If it doesn't exist it will be installed.\n";
		$moduleLoader = new Module_ximLOADER();
		$moduleLoader->install(2);                    
               	$this->loadConstructorSQL("ximTOUR.constructor.sql");
                parent::install();
		
	}
        
        function uninstall(){
		   		$this->removeStateFile();
        }
}

?>
