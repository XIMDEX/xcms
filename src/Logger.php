<?php

namespace Ximdex;

use Colors\Color;
// use Monolog\Handler\StreamHandler;
use Ximdex\Runtime\App;
use Exception;
use Monolog\Handler\RotatingFileHandler;

Class Logger
{
    const MAX_FILES_TO_KEEP = 30;
    
    private static $instances = array();
    private static $active = '';
    private $logger = null;
    private static $color;

    public function __construct($logger)
    {
        $this->logger = $logger;
        if (!self::$color) {
            self::$color = new Color();
            self::$color->setForceStyle(true);
        }
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
        // $log->pushHandler(new StreamHandler(XIMDEX_ROOT_PATH . '/logs/' . $file . '.log', \Monolog\Logger::DEBUG, true, 0666));
        $log->pushHandler(new RotatingFileHandler(XIMDEX_ROOT_PATH . '/logs/' . $file . '.log', self::MAX_FILES_TO_KEEP
            , \Monolog\Logger::DEBUG, true, 0666));
        if ($default) {
            self::addLog($log);
        } else {
            self::addLog($log, $file);
        }
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
        try {
            $res = self::get()->logger->addError(self::$color->__invoke($string)->red()->bold(), $object);
            if (CLI_MODE) {
                sleep(5);
            }
            return $res;
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    public static function warning($string)
    {
        return self::get()->logger->addWarning(self::$color->__invoke($string)->yellow());
    }

    public static function debug($string)
    {
        if (App::debug()) {
            try {
                return self::get()->logger->addDebug(self::$color->__invoke($string)->white());
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        }
    }

    public static function fatal($string)
    {
        try {
            $res = self::get()->logger->addCritical(self::$color->__invoke($string)->red()->bold());
            if (CLI_MODE) {
                sleep(5);
            }
            return $res;
        }
        catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    public static function info($string, bool $success = false, string $color = '')
    {
        try {
            if ($success) {
                $string = self::$color->__invoke($string)->green()->bold();
            }else if (!empty($color)) {
                $string = self::$color->__invoke($string)->$color()->bold();
            }
            return self::get()->logger->addInfo($string);
        } catch (Exception $e) {
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
        if (self::$active) {
            return self::$active;
        }
        return 'default';
    }
}