<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;




// for legacy compatibility
if (!defined('XIMDEX_ROOT_PATH')) {
    define('XIMDEX_ROOT_PATH', dirname(dirname(__FILE__)));
}




if ( function_exists( 'xdebug_start_code_coverage')) {
    function ximdex_end_debug() {
        $vars = xdebug_get_code_coverage() ;
        $hf = fopen(dirname(__FILE__) .'/../logs/coverage.log', 'a');
        foreach( $vars as $file => $values ) {
            fwrite( $hf,  json_encode( array( $file  =>  array_keys( $values)   )) . PHP_EOL);
        }
        fclose($hf);
    }
    register_shutdown_function( 'ximdex_end_debug');
    xdebug_start_code_coverage();
}

if (!defined('CLI_MODE'))
    define('CLI_MODE', 0);



include_once dirname(dirname(__FILE__)) . '/extensions/vendors/autoload.php';


class_alias('Ximdex\Modules\Manager', 'ModulesManager');

// load FSUtils

// Initialize App
class_alias('Ximdex\Runtime\App', 'App');
App::setValue('XIMDEX_ROOT_PATH', dirname(dirname(__FILE__)));




// get Config from install file
if ( file_exists( App::getValue('XIMDEX_ROOT_PATH') . '/conf/install-params.conf.php' ) ) {
    $conf = require_once(App::getValue('XIMDEX_ROOT_PATH') . '/conf/install-params.conf.php');
    foreach ($conf as $key => $value) {
        App::setValue($key, $value);
    }
}
include_once( dirname(dirname(__FILE__))   . '/inc/fsutils/FsUtils.class.php');

// Initialize Modules Manager
// set ximdex root path
Ximdex\Modules\Manager::init( App::getValue('XIMDEX_ROOT_PATH')   );
Ximdex\Modules\Manager::file( Ximdex\Modules\Manager::get_modules_install_params() );
// setup log
class_alias('Ximdex\Logger', 'XMD_Log');

$log = new Logger('XMD');
$log->pushHandler(new StreamHandler(App::getValue('XIMDEX_ROOT_PATH') .'/logs/xmd.log', Logger::DEBUG));
Ximdex\Logger::addLog( $log );
$log = new Logger('Actions');
$log->pushHandler(new StreamHandler(App::getValue('XIMDEX_ROOT_PATH') .'/logs/actions.log', Logger::DEBUG));
Ximdex\Logger::addLog( $log , 'actions' ) ;

XMD_Log::setActiveLog();

// read install-modules.conf
$modulesConfString = file_get_contents(App::getValue('XIMDEX_ROOT_PATH') . '/conf/install-modules.conf');
$matches = array();
preg_match_all('/define\(\'(.*)\',(.*)\);/iUs', $modulesConfString, $matches);
foreach ($matches[1] as $key => $value) {
    App::setValue($value, str_replace('\'', '', $matches[2][$key]));
}

// use config values
define('DEFAULT_LOCALE', App::getValue('locale', 'ES_es'));
date_default_timezone_set(App::getValue('timezone', 'Europe/Madrid'));

// set DB Connection
$dbConfig = App::getValue('db');
if ( !empty( $dbConfig ) ) {
    $dbConn = new \PDO("{$dbConfig['type']}:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['db']}",
        $dbConfig['user'], $dbConfig['password']);
    $dbConn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    App::addDbConnection($dbConn);

// get Persistent Config
    $stm = App::Db()->prepare('select * from Config');
    $stm->execute();
    foreach ($stm as $row) {
        App::setValue($row['ConfigKey'], $row['ConfigValue']);
    }
}

// special objects (pseudo-DI)
App::setValue( 'class::definition::Messages',       '/inc/helper/Messages.class.php' );
App::setValue( 'class::definition::QueryManager',   '/inc/helper/QueryManager.class.php' );
App::setValue( 'class::definition::DB',             '/inc/db/DB.class.php' );
App::setValue( 'class::definition::XMD_log',        '/inc/log/XMD_log.class.php' );


// Extensions setup

include_once( App::getValue('XIMDEX_ROOT_PATH') . '/conf/extensions.conf.php');
