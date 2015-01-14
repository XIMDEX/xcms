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
use Ximdex\Modules\Module;

//ModulesManager::file('/lib/Ximdex/Modules/Module.php');
//ModulesManager::file('/inc/io/BaseIO.class.php');
//ModulesManager::file('/inc/model/RelRolesActions.class.php');


class Module_ximPUBLISHtools extends Module {

    const PUB_REPORT_ACTION_ID = '7300';
    const COL_STATES_REPORT_ACTION_ID = '7302';

    function Module_ximPUBLISHtools() {
        // Call Module constructor.
        parent::__construct('ximPUBLISHtools', dirname(__FILE__));
        // Initialization stuff.
    }

    function install() {

        if (!ModulesManager::isEnabled('ximNEWS')) {
            $this->log(Module::WARNING, 'ximNEWS module is not enabled. Several actions will not be visible until ximNEWS is enabled');
        }

        if (!ModulesManager::isEnabled('ximSYNC')) {
            $this->log(Module::WARNING, 'ximSYNC module is not enabled. Several actions will not be visible until ximSYNC is enabled');
        }

        $this->loadConstructorSQL("ximPUBLISHtools.constructor.sql");

        parent::install();
    }

    function uninstall() {
        // Uninstalling withouth disabling not allowed for this module.
        $modMngr = new ModulesManager('wix');
        if (ModulesManager::isEnabled($this->getModuleName()))
            ModulesManager::disableModule($this->getModuleName());

        $this->loadDestructorSQL("ximPUBLISHtools.destructor.sql");
        parent::uninstall();
    }

    function enable() {

        // It's necessary to enable actions on this method, not on 'install' method.
        // Due to recent lmd class removal, unique way to do this is via DB class

        $db = new DB();
        $sql = array();

        // Pub. Report

        $sql['Creating ximPUBLISH report action'] = "INSERT INTO Actions 
		(IdAction,IdNodeType,Name,Command,Icon,Description,Sort,Module,Multiple) 
		VALUES ('" . self::PUB_REPORT_ACTION_ID . "','5014','Informe de publicación','managebatchs','publicate_section.png',
		'Muestra estado de la cola de trabajos de publicación',100,'ximPUBLISHtools',0)";

        $sql['Enabling ximPUBLISH report action'] = "INSERT INTO RelRolesActions 
		(IdRel,IdRol,IdAction,IdState,IdContext) 
		VALUES (NULL,201," . self::PUB_REPORT_ACTION_ID . ",NULL,1)";

        // Colectors States Report
        $sql['Creating Colectors States Report action'] = "INSERT INTO Actions 
		(IdAction,IdNodeType,Name,Command,Icon,Description,Sort,Module,Multiple) 
		VALUES ('" . self::COL_STATES_REPORT_ACTION_ID . "','5301','Listado de Colectores','viewcolectorstates','generate_colector.png',
		'Muestra un listado con los colectores de una seccion y sus estados',100,'ximPUBLISHtools',0)";

        $sql['Enabling Colectors States Report action'] = "INSERT INTO RelRolesActions 
		(IdRel,IdRol,IdAction,IdState,IdContext) 
		VALUES (NULL,201," . self::COL_STATES_REPORT_ACTION_ID . ",NULL,1)";

        foreach ($sql as $desc => $query) {
            if (!$ret = $db->Execute($query)) {
                echo "Error {$desc} - {$query}\n";
                XMD_Log::error($desc);
                self::disable();
                die();
            }

            echo "{$desc} successfully\n";
        }

        parent::enable();
    }

    function disable() {
        // It's necessary to disable actions on this method, not on 'uninstall' method.
        // Due to recent lmd class removal, unique way to do this is via DB class

        $db = new DB();
        $sql = array();
        $relRolesActions = new RelRolesActions();

        // Pub. Report
        $sql['Deleting ximPUBLISH report action'] = "DELETE FROM Actions WHERE IdAction = '" . self::PUB_REPORT_ACTION_ID . "'";
        $result = $relRolesActions->find('IdRel', 'IdAction = %s', array(self::PUB_REPORT_ACTION_ID), MONO);
        $sql['Disabling ximPUBLISH report action'] = "DELETE FROM RelRolesActions WHERE IdRel = '" . $result[0] . "'";

        // Colector States Report
        $sql['Deleting Colectors States Report action'] = "DELETE FROM Actions WHERE IdAction = '" . self::COL_STATES_REPORT_ACTION_ID . "'";
        $result = $relRolesActions->find('IdRel', 'IdAction = %s', array(self::COL_STATES_REPORT_ACTION_ID), MONO);
        $sql['Disabling Colectors States Report action'] = "DELETE FROM RelRolesActions WHERE IdRel = '" . $result[0] . "'";

        foreach ($sql as $desc => $query) {
            $ret = $db->Execute($query);
            echo "{$desc} successfully\n";
        }

        parent::disable();
    }

}

?>
