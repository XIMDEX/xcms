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



// Point to ximdex root and include necessary class.
ModulesManager::file('/inc/modules/Module.class.php');
ModulesManager::file('/inc/model/node.inc');
ModulesManager::file('/inc/cli/CliReader.class.php');
ModulesManager::file('/inc/model/orm/Channels_ORM.class.php');
ModulesManager::file('/inc/fsutils/FsUtils.class.php');
ModulesManager::file('/inc/persistence/Config.class.php');
//Xowl is not actived in this point
require_once(XIMDEX_ROOT_PATH.ModulesManager::path('Xowl').'/actions/enricher/model/Enricher.class.php');

class Module_Xowl extends Module {
	
	//Class constructor
	function Module_Xowl() {
		// Call Module constructor.
		parent::Module("Xowl", dirname (__FILE__));
	}

	function install() {
        	// get constructor SQL
		$this->loadConstructorSQL("Xowl.constructor.sql");
		$install_ret = parent::install();
		return true;
	}

	function enable() {
		$sp = "You must type the Xowl key in order to activate this module.\n\n(If you don't know what it's all about, please contact us at soporte@ximdex.com.)";

                $key = CliReader::getString(sprintf("\nXowl module activation info: %s\n\n--> Xowl Key: ", $sp));
		printf("\nStoring your personal key ...\n");
		
		$sql="UPDATE Config SET ConfigValue='".$key."' WHERE ConfigKey='EnricherKey'";
		$db=new DB();
		$db->Execute($sql);
		printf("Key stored successfully!. Testing service conection ...\n\n");


		$ra = new Enricher();
		$text='';
		$ret = $ra->suggest($text,$key,'xml');			

		if(empty($ret)){
			printf("Deleting key...\n");
			$sql_del="UPDATE Config SET ConfigValue='' WHERE ConfigKey='EnricherKey'";
			$db->Execute($sql_del);			
			printf("The service could not be connected. Your key is not correct. Please contact us.\n\n");
		}
		else{printf("Conection OK. You can now enrich your documents with our Remote Annotator!.\n\n");}		
	}

	function uninstall() {
        	// get destructor SQL
		$this->loadDestructorSQL("Xowl.destructor.sql");
		parent::uninstall();
	}
}
?>
