<?php
/**
 *  ximNOTA definition class.
 *
 *  @author Ximdex Development Team <dev@ximdex.com>
 *  @version $Revision:$
 *
 **/

// Point to ximdex root and include necessary class.
if (!defined('XIMDEX_ROOT_PATH')) 
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));
require_once(XIMDEX_ROOT_PATH . "/inc/modules/Module.class.php");

class Module_ximNOTA extends Module {

	function Module_ximNOTA() {
		// Call Module constructor.
		parent::Module('ximNOTA', dirname(__FILE__));
		// Initialization stuff.
	}

	function install() {
		$this->loadConstructorSQL("ximNOTA.constructor.sql");
		$install_ret = parent::install();		
	}

	function uninstall() {
		$this->loadDestructorSQL("ximNOTA.destructor.sql");
		parent::uninstall();
	}

}

?>
