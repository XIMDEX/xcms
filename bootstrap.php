<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Logger;
use Ximdex\Runtime\App;

// For legacy compatibility
if (!defined('XIMDEX_ROOT_PATH')) {
    define('XIMDEX_ROOT_PATH', __DIR__);
}
else {
    return false; //only once this file
}
if (!defined('APP_ROOT_PATH')) {
    define('APP_ROOT_PATH', XIMDEX_ROOT_PATH.'/public_xmd');
}
if (!defined('XIMDEX_VENDORS')) {
    define('XIMDEX_VENDORS', '/vendors');
}

/**
 * XIMDEX_DIRECT is true when bootstrap is called directly
 * XIMDEX_DIRECT is false when bootstrap is called from other php
 */
if (!defined('XIMDEX_DIRECT')) {   
    $included_files = get_included_files();
    define('XIMDEX_DIRECT', !isset($included_files[1]));
}

// Checking cli mode
if (!defined('CLI_MODE')) {
    global $argv, $argc;
    if('cli' != php_sapi_name() || empty($argv) || 0 == $argc) {
        $cli_mode = false;
    }
    else {
        $cli_mode = true;
    }
    define('CLI_MODE', $cli_mode);
}

include_once XIMDEX_ROOT_PATH.XIMDEX_VENDORS . '/autoload.php';

// Initialize XIMDEX_ROOT_PATH
App::setValue('XIMDEX_ROOT_PATH', dirname(dirname(__FILE__)));

// Get Config from install file
if ( file_exists( XIMDEX_ROOT_PATH . '/conf/install-params.conf.php' ) ) {
    $conf = require_once(XIMDEX_ROOT_PATH . '/conf/install-params.conf.php');
    foreach ($conf as $key => $value) {
        App::setValue($key, $value);
    }
}

// Initialize Modules Manager
// Set ximdex root path
Ximdex\Modules\Manager::file(\Ximdex\Modules\Manager::get_modules_install_params(), 'XIMDEX');

// Generate general purpose logs files
Logger::generate('XMD', 'xmd', true);
Logger::generate('ACTIONS', 'actions');
Logger::generate('PREVIEW', 'preview');

// Set default log (xmd.log)
Logger::setActiveLog();

// Read install-modules.php
$modulesConfString = "";
$installModulesPath = XIMDEX_ROOT_PATH . '/conf/install-modules.php';
if( file_exists($installModulesPath) ){
    $modulesConfString = file_get_contents($installModulesPath);
}
$matches = array();
preg_match_all('/define\(\'(.*)\',(.*)\);/iUs', $modulesConfString, $matches);
foreach ($matches[1] as $key => $value) {
    App::setValue($value, str_replace('\'', '', $matches[2][$key]));
}

// Use config values
define('DEFAULT_LOCALE', App::getValue('locale', 'ES_es'));
date_default_timezone_set(App::getValue('timezone', 'Europe/Madrid'));

// Set DB Connection
$dbConfig = App::getValue('db');
if ( !empty( $dbConfig ) ) {
	try
	{
    	$dbConn = new \PDO("{$dbConfig['type']}:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['db']};charset=utf8",
        		$dbConfig['user'], $dbConfig['password']);
	}
	catch (\PDOException $e)
	{
	    $error = 'Can\'t connect to database at ' . $dbConfig['host'] . ':' . $dbConfig['port'] . ' (' . $e->getMessage() . ')';
	    Logger::error($error);
	    if (CLI_MODE)
	    {
	        $color = new Colors\Color();
	        echo $color($error)->red()->bold() . PHP_EOL;
	    }
		die();
	}
    $dbConn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    App::addDbConnection($dbConn);

    // Get Persistent Config
    $stm = App::Db()->prepare('select * from Config');
    $stm->execute();
    foreach ($stm as $row) {
        App::setValue($row['ConfigKey'], $row['ConfigValue']);
    }
}

// special objects (pseudo-DI)
class_alias('Ximdex\Utils\Messages', 'Messages');
App::setValue( 'class::definition::DB', '/inc/db/DB.class.php' );

// Extensions setup
include_once( XIMDEX_ROOT_PATH . '/conf/extensions.conf.php');
$mManager = new \Ximdex\Modules\Manager;

/**
 * Execute function init for each enabled module
 */
foreach(\Ximdex\Modules\Manager::getEnabledModules() as $module){
    $name = $module["name"];
    $moduleInstance = $mManager->instanceModule($name);
    if( method_exists( $moduleInstance, 'init' ) ){
        $moduleInstance->init();
    }
}

// FROM MVC
if (!defined('RENDERER_ROOT_PATH')) {
    define('RENDERER_ROOT_PATH', XIMDEX_ROOT_PATH . '/inc/mvc/renderers');
}
if (!defined('SMARTY_TMP_PATH')) {
    define('SMARTY_TMP_PATH', XIMDEX_ROOT_PATH . App::getValue('TempRoot'));
}
if (XIMDEX_DIRECT && CLI_MODE && isset($argv[1])) {

    /* e.g:  $ /bootstrap.php src/Sync/scripts/scheduler/scheduler.php
     *  $command = /bootstrap.php  => argv[0]
     *  $script = src/Sync/scripts/scheduler/scheduler.php => new_command
     *
     */
    $command = array_shift($argv);
    $argc--;
    $script =  parse_url ( $argv[0],  PHP_URL_PATH );
    $is_absolute_path = ( '/' == $script[0])?: false;
    if ( $is_absolute_path ) {
        $external_script = $script;
    } else {
        $external_script = XIMDEX_ROOT_PATH.'/'.$script;
    }
    $new_command = $argv[0] = $external_script;
    if (file_exists($new_command)) {
        require_once($new_command);
    }
}
