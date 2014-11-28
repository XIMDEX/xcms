<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 6/11/14
 * Time: 18:11
 */

namespace Ximdex;


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
            throw \Exception('Logger need to be initilized');
            return;
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
            throw \Exception('Logger Instance not found');
            return;
        }
        self::$active = $loggerInstance;
        return self::$instances[$loggerInstance];
    }

    public static function error($string, $object = array())
    {
        return self::get()->logger->addError($string, $object);
    }

    public static function warning($string)
    {
        return self::get()->logger->addWarning($string);
    }

    public static function debug($string)
    {
        return self::get()->logger->addDebug($string);
    }

    public static function fatal($string)
    {
        return self::get()->logger->addFatal($string);
    }

    public static function info($string)
    {
        return self::get()->logger->addInfo($string);
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
}