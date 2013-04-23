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
 *  @version $Revision: 8529 $
 */



ModulesManager::file('/inc/serializer/Serializer.class.php');

class Action_moduleslist extends ActionAbstract {

	public function index() {

	}
	
	protected function readModules() {

		$modules = array();
		$userId = XSession::get('userID');
		if ($userId != '301') {
			// Must be administrator
			return $modules;
		}		
		
		$paths = ModulesManager::getModules();
				
		foreach ($paths as $module) {
			$moduleName=$module["name"];
			if(strpos($moduleName,'xim')!==false){
				$isEnabled = $this->isEnabled($moduleName);
				$coreModule = in_array($moduleName, ModulesManager::getCoreModules() );
				$modules[] = array(
					'core_module' => $coreModule, 
					'name' => $moduleName,
					'enabled' => $isEnabled,
					'class' => ($isEnabled ? 'browser-modules-view-enabled' : 'browser-modules-view-disabled')
				);
			}
		}
		
		$values = array('modules' => $modules);
		$this->render($values, "modulelist", 'only_template.tpl');
	}
	
	/*protected function isInstalled($moduleName) {
		return ModulesManager::isEnabled($moduleName);
	}*/
	
	protected function isEnabled($moduleName) { return ModulesManager::isEnabled($moduleName);	}

	private function moduleNotFound() {
		$this->messages->add( _("Module not found"), MSG_TYPE_ERROR);
		return $this->render(array('messages' => $this->messages->messages), NULL, 'messages.tpl');		
	}


	public function opentab() {
		$this->addJs('/actions/moduleslist/resources/js/validate.js');
		$this->addCss('/actions/moduleslist/resources/css/moduleslist.css');
		$lang = strtolower(XSession::get("locale"));
		$base = XIMDEX_ROOT_PATH."/actions/moduleslist/template/Smarty";


		$module_name = $this->request->getParam('modsel');
		$module_exists = ModulesManager::moduleExists($module_name);
		if(!$module_exists) { return $this->moduleNotFound(); }
		$module_actived = ModulesManager::isEnabled($module_name);
		$module_state = ModulesManager::checkModule($module_name);
		$module_installed = (MODULE_STATE_INSTALLED == $module_state);
		$core_module = in_array($module_name, ModulesManager::getCoreModules() );

		$values = array(
			"module_name"      => $module_name, 
			"module_exists"    => $module_exists, 
			"module_actived"   => $module_actived,
			"module_installed" => $module_installed, 
			"core_module"	   => $core_module, 
			"lang" 			   => $lang
		);



		$file = "{$module_name}.tpl";



		if ( file_exists("{$base}/{$lang}/{$file}" ) )  {
			 $this->render($values, "{$lang}/{$file}", 'default-3.0.tpl');
		}else if ( file_exists("{$base}/en_us/{$file}" ) ) {
			 $this->render($values, "en_us/{$file}", 'default-3.0.tpl');
		}else {
			return $this->moduleNotFound();
		}
	}



	public function changeState() {
		$module_name = $this->request->getParam('modsel');
		$module_exists = ModulesManager::moduleExists($module_name);
		$module_active  = (int) $this->request->getParam('module_active');
		$module_install  = (int) $this->request->getParam('module_install');
		if(!$module_exists) { return $this->moduleNotFound(); }
		$state_now = ModulesManager::isEnabled($module_name);
		$module_state = ModulesManager::checkModule($module_name);
		$install_now = (MODULE_STATE_INSTALLED == $module_state);
		$core_module = in_array($module_name, ModulesManager::getCoreModules() );

		if($state_now != $module_active && !$core_module) {
			if($module_active) {
				//Before active, we check if install it
				if($module_install != $install_now && $module_install) {
					$this->installModule($module_name);
				}
				
				$this->enableModule($module_name);
			}else {
				$this->disableModule($module_name);

				//After disabled, we check if uninstall it
				if($module_install != $install_now && !$module_install) {
					$this->uninstallModule($module_name);			
				}
			}
		}else {
			if($module_install != $install_now && !$core_module) {
				if( $module_install) {
					$this->installModule($module_name);
				}else {
					$this->uninstallModule($module_name);	
				}
			}else {
				$this->messages->add( _("Module not changed"), MSG_TYPE_ERROR);
			}
		}


		echo json_encode(array('messages' => $this->messages->messages));
		die();
	}


	function installModule($module_name) {

		ModulesManager::$msg = null;
		ModulesManager::installModule($module_name);
		if(ModulesManager::$msg != null) {
			$this->messages->add(ModulesManager::$msg, MSG_TYPE_NOTICE);
			ModulesManager::$msg = null;
		}else {
			$this->messages->add( _("Module installed"), MSG_TYPE_NOTICE);
		}
	}

	function enableModule($module_name) {

		ModulesManager::$msg = null;
		ModulesManager::enableModule($module_name);
		if(ModulesManager::$msg != null) {
			$this->messages->add(ModulesManager::$msg, MSG_TYPE_NOTICE);
			ModulesManager::$msg = null;
		}else {
			$this->messages->add( _("Module actived"), MSG_TYPE_NOTICE);
		}
	}

	function uninstallModule($module_name)  {

		ModulesManager::uninstallModule($module_name);
		if(ModulesManager::$msg != null) {
			$this->messages->add(ModulesManager::$msg, MSG_TYPE_NOTICE);
			ModulesManager::$msg = null;
		}else {
		$this->messages->add( _("Module uninstalled"), MSG_TYPE_NOTICE);
		}
	}

	function disableModule($module_name) {
		ModulesManager::$msg = null;
		$this->messages->add( _("Module disabled"), MSG_TYPE_NOTICE);
		ModulesManager::disableModule($module_name);
		if(ModulesManager::$msg != null) {
			$this->messages->add(ModulesManager::$msg, MSG_TYPE_NOTICE);
			ModulesManager::$msg = null;
		}else {
		$this->messages->add( _("Module disabled"), MSG_TYPE_NOTICE);
		}
	}
}

?>
