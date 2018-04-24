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

namespace Ximdex\Modules;

use Symfony\Component\Console\Application;
use Ximdex\Cli\Shell,
    Ximdex\Logger,
    Ximdex\Tasks\Worker;

class Module  {

    public $name;
    public $path;
    public $actions;
    public $sql_constructor;
    public $sql_constructor_file;
    public $sql_destructor;
    public $sql_destructor_file;
    public $messages;

    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const SUCCESS = 'SUCCESS';

    /**
     * @public
     */
    public function __construct($name, $path) {

        if (empty($name) || empty($path)) {
            die("* ERROR: name and path in Module constructor must be provided.\n");
        }

        $this->messages = new \Ximdex\Utils\Messages();

        //$this->name = get_class($this);
        $this->name = $name;
        $this->path = $path;

        $this->sql_constructor = array();
        $this->sql_destructor = array();

        //  $this->messages->add(sprintf(_("sys {%s} : Module instanciated (%s) (%s)"),
        //   __CLASS__, $this->name, $this->path), MSG_TYPE_NOTICE);
       // Logger::info(sprintf("sys {%s} : Module instanciated (%s) (%s)", __CLASS__, $this->name, $this->path));
    }

    /**
     * Installed modules has a state file in XIMDEX_ROOT_PATH/data
     * Uninstalled modules hasn't
     */
    protected function getStateFile() {
        return XIMDEX_ROOT_PATH . "/data/.{$this->getModuleName()}";
    }

    protected function checkStateFile() {
        return file_exists($this->getStateFile());
    }

    protected function addStateFile() {
        if (!$this->checkStateFile()) {
            file_put_contents($this->getStateFile(), "", FILE_APPEND);
        }
    }

    protected function removeStateFile() {
        if ($this->checkStateFile()) {
            unlink($this->getStateFile());
        }
    }

    /**
     * return if module is core ( true ) or not ( false )
     */
    function isCoreModule() {
        return in_array($this->getModuleName(), Manager::getCoreModules());
    }

    /**
     * @public
     */
    function getModulePath() {
        return $this->path;
    }

    /**
     * @public
     */
    function getModuleName() {
        return $this->name;
    }

    /**
     * @public
     */
    function getModuleClassName() {
        return Manager::get_module_prefix() . $this->name;
        //return get_class($this);
    }

    /**
     * @private
     * @param $sql_file : Filename (without path information) which contain SQL.
     * @return NULL or SQL Data Array.
     */
    function loadSQL($sql_file) {

        $sql_path = $this->getModulePath() . "/sql/";
        $sql_file = $sql_path . $sql_file;

        if (file_exists($sql_file)) {
            $sql_data = file($sql_file);
            if (is_array($sql_data)) {
                foreach ($sql_data as $idx => $sql) {
                    $sql_data[] = rtrim($sql);
                }
            } else {
                $this->messages->add(sprintf(_("** ERROR: %s is empty"), $sql_file), MSG_TYPE_ERROR);
                return null;
            }
        } else {
            $this->messages->add(sprintf(_("** ERROR: %s doesn't exist"), $sql_file), MSG_TYPE_ERROR);
            return null;
        }
        return $sql_data;
    }

    /**
     * @protected
     */
    function loadConstructorSQL($sql_file) {

        $this->sql_constructor_file = $sql_file;
        $data = $this->loadSQL($sql_file);

        if (!empty($data)) {
            $this->sql_constructor = $data;
            return true;
        } else {
            Logger::error("Error loading module constructor $sql_file");
            return false;
        }
    }

    /**
     * @protected
     */
    function loadDestructorSQL($sql_file) {

        $this->sql_destructor_file = $sql_file;
        $data = $this->loadSQL($sql_file);

        if (!is_null($data)) {
            $this->sql_destructor = $data;
            return true;
        } else {
            return false;
        }
    }

    function sqlFileExists($sql_file) {
        $sql_path = $this->getModulePath() . '/sql/';
        $sql_file = $sql_path . $sql_file;
        return file_exists($sql_file);
    }

    /**
     * @private
     * Inject via mysql command...
     */
    function injectSQLFile($sql_file) {
        $sql_path = $this->getModulePath() . '/sql/';
        $sql_file = $sql_path . $sql_file;
        $result = false;

        if (file_exists($sql_file)) {
            $db = new \Ximdex\Runtime\Db();
            $sql = file_get_contents($sql_file);
            $result = $db->ExecuteScript($sql);
            if ($result === false)
            {
            	$this->messages->add(sprintf(_("Error executing SQL script file: %s"), $sql_file), MSG_TYPE_WARNING);
            	Logger::error('Error executing SQL script file: ' . $sql_file);
            }
        } else {
            $this->messages->add(sprintf(_("%s not exists"), $sql_file), MSG_TYPE_WARNING);
            $result = false;
        }
        return $result;
    }

    /**
     *  Install new module into ximDEX.
      *
      */
    function install() {

        $ret = true;
        if (!$this->preInstall()) {
            echo "Se ha abortado la instalación por no cumplirse los prerequisitos\n";
            $ret = false;
        } else {
            // SQL Insertion
            if (!empty($this->sql_constructor)) {
                $this->injectSQLFile($this->sql_constructor_file);
                Logger::info("-- SQL constructor loaded");
            } else {
                $this->messages->add(_("* ERROR: SQL constructor not loaded"), MSG_TYPE_ERROR);
                $ret = false;
            }
            // Actions Registration
            // Actions Activation
            if (!$this->postInstall()) {
                echo "Ha fallado el proceso de post instalación, puede que el módulo no funcione correctamente\n";
                $ret = false;
            } else {
                $this->addStateFile();
            }
        }
        // Muestra los mensajes
        $this->messages->displayRaw();
        return $ret;
    }

    /**
     *  Instructions previous to the installation
     */
    function preInstall() {
        return true;
    }

    function checkDependences($arrDependences) {
        if (!is_array($arrDependences)) {
            return NULL;
        }
        foreach ($arrDependences as $dependence) {
            $ret = Shell::exec("which " . $dependence, true);
            if (empty($ret[0])) {
                return $dependence;
            }
        }
        return null;
    }

    /**
     *  Instructions subsequent to the installation
     * @public
     *
     * @return
     */
    function postInstall() {
        return true;
    }

    /**
     *  Uninstall module from ximDEX.
     * @public
     */
    function uninstall() {

        // SQL Remove
        if (!empty($this->sql_destructor) && !$this->isCoreModule()) {
            $this->injectSQLFile($this->sql_destructor_file);
            $this->removeStateFile();
            $this->messages->add(_("-- SQL destructor loaded"), MSG_TYPE_NOTICE);
        } else {
            $this->messages->add(_("* ERROR: SQL destructor not loaded"), MSG_TYPE_ERROR);
        }
        // Actions deRegistration
        // Actions deActivation
        //show messages
        $this->messages->displayRaw();
    }

    // States -

    /**
     *  Enable module.
     * @public
     */
    function enable() {
        
    }

    /**
     *  Disable module.
     * @public
     */
    function disable() {
        
    }

    /**
     * @public
     */
    function state() {
        if ($this->checkStateFile()) {
            return Manager::get_module_state_installed();
        } else {
            return Manager::get_module_state_uninstalled();
        }
    }

    function log($priority, $string) {

        /*
        if ($this instanceof Modules) {
            Logger::warning("Using $this->log in a class that is not an instance of Module.");
            return false;
        }
        */

        $module_name = $this->name;

        switch ($priority) {
            case self::SUCCESS:
                Logger::info(" - [$module_name] (SUCCESS): $string");
                break;
            case self::ERROR:
            default:
                echo(" * [$module_name] (ERROR): $string\n");
                Logger::error($string);
        }
    }

    /**
     * @protected
     */
    function checkState() {
        return $this->state();
    }

    /**
     * return array with install params
     *
     */
    function getInstallParams() {
        return array();
    }

    /**
     * @param Worker $worker
     */
    public function addTasks(Worker &$worker) {

    }

    /**
     * @param Application $application
     */
    public function addCommands( Application &$application ) {

    }






}
