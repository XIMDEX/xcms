<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 6/11/14
 * Time: 18:11
 */

namespace Ximdex;

use Monolog\Handler\StreamHandler;
use Ximdex\Runtime\App;
use Exception;


Class Logger
{
    private static $instances = array();
    private static $active = '';
    private $logger = null;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Make a new instance of a file log handler with read and write permission for user and group (and for others in porder to test works)
     * @param string $id
     * @param string $file
     * @param bool $default
     */
    public static function generate(string $id, string $file, bool $default = false)
    {
        $log = new \Monolog\Logger($id);
        $log->pushHandler(new StreamHandler(XIMDEX_ROOT_PATH . '/logs/' . $file . '.log', \Monolog\Logger::DEBUG, true, 0666));
        if ($default)
            self::addLog($log);
        else
            self::addLog($log, $file);
    }

    /**
     * @param $logger
     * @param string $loggerInstance
     */
    public static function addLog($logger, $loggerInstance = 'default')
    {
        self::$instances[$loggerInstance] = new Logger($logger);
        if (count(self::$instances) == 1) {
            self::$active = $loggerInstance;
        }
    }

    public static function get()
    {
        $loggerInstance = self::$active;
        if (!isset(self::$instances[$loggerInstance]) || !self::$instances[$loggerInstance] instanceof self) {
            throw new Exception('Logger need to be initilized');
        }
        return self::$instances[$loggerInstance];
    }

    /**
     * @param string $loggerInstance
     * @return Logger
     * @throws
     */
    public static function setActiveLog($loggerInstance = 'default')
    {
        if (!isset(self::$instances[$loggerInstance])) {
            throw new Exception('Logger Instance not found');
        }
        self::$active = $loggerInstance;
        return self::$instances[$loggerInstance];
    }

    public static function error($string, $object = array())
    {
        error_log($string);
        try{
            return self::get()->logger->addError($string, $object);
        }catch (\Exception $e){
            error_log($e->getMessage());
        }
    }

    public static function warning($string)
    {
        error_log($string);
        return self::get()->logger->addWarning($string);
    }

    public static function debug($string)
    {
        error_log($string);
        if (App::debug())
        {
            try{
                return self::get()->logger->addDebug($string);
            }catch (Exception $e){
                error_log($e->getMessage());
            }
        }
    }

    public static function fatal($string)
    {
        error_log($string);
        try{
            return self::get()->logger->addCritical($string);
        }catch (Exception $e){
            error_log($e->getMessage());
        }
    }

    public static function info($string)
    {
        error_log($string);
        try{
            return self::get()->logger->addInfo($string);
        }catch (Exception $e){
            error_log($e->getMessage());
        }
    }

    public static function logTrace($string)
    {
        $trace = debug_backtrace(false);
        $t1 = $trace[1];
        $t2 = $trace[2];

        $trace = array(
            'file' => $t1['file'],
            'line' => $t1['line'],
            'function' => $t2['class'] . $t2['type'] . $t2['function']
        );
        $result = $string . PHP_EOL . sprintf("on %s:%s [%s]\n", $trace['file'], $trace['line'], $trace['function']);
        return self::get()->logger->addInfo( $result );
    }
    
    public static function get_active_instance()
    {
        if (self::$active)
            return self::$active;
        return 'default';
    }
}