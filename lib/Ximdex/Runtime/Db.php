<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 11/05/16
 * Time: 15:10
 */
namespace Ximdex\Runtime;

use Ximdex\Runtime\App;
use PDO;
use XMD_Log;

class Db
{
    /**
     * @var null|\PDO
     */
    private $db = null;
    private static $defaultConf = null;

    private $sql = '';
    private $dbEncoding = '';
    private $workingEncoding;
    private $rows = array();
    public $EOF = true;
    public $row = array();
    public $numRows = 0;
    public $numFields = 0;
    private $index = 0;
    public $numErr = null;
    public $desErr = null;
    public $newID = null;

    /**
     * @var \PDOStatement
     */
    private $stm = null;

    /**
     * @param string $conf
     * @return Db
     * @throws \Exception
     */
    static public function getInstance($conf = null)
    {
        //return new  Db($conf);
        return new   Db();
    }

    /**
     * Db constructor.
     * @param string|null $conf
     */
    public function __construct($conf = null)
    {
        if (is_null($conf)) {
             if(is_null(self::$defaultConf)){
                 self::$defaultConf = App::getInstance()->getValue('default.db', 'db');
             }
             $conf = self::$defaultConf;
         }
        $this->db = App::Db($conf);
    }

    /**
     * Reconnect the Database
     */
    public function reconectDataBase(){
         $dbConfig = App::getInstance()->getValue('db', 'db');
         if ( !empty( $dbConfig ) ) {
             $dbConn = new \PDO("{$dbConfig['type']}:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['db']}", $dbConfig['user'], $dbConfig['password']);
             $dbConn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
             $this->db = $dbConn;
             $idconfig = uniqid();
             App::addDbConnection($dbConn, $idconfig);
             self::$defaultConf = $idconfig;
         }
     }

    public function Query($sql, $cache = false)
    {

        // todo remove cache parameter
        unset($cache);


        $this->_getEncodings();
        $sql = \Ximdex\XML\Base::recodeSrc($sql, $this->dbEncoding);

        $this->sql = $sql;

        $this->rows = array();

        try {
            $this->stm = $this->db->query($this->sql, PDO::FETCH_ASSOC);

            if ($this->stm === false) {
                throw new \Exception('Bad Query: ' . $this->sql);
            }

            foreach ($this->stm as $row) {

                $this->rows[] = $row;

            }

        } catch (\Exception $e) {
            if ($this->db->errorCode() == PDO::ERR_NONE) {
                $this->numErr = null;
            } else {
                $this->numErr = $this->db->errorCode();
            }
        }

        if (count($this->rows) == 0) {

            $this->EOF = true;

        } else {
            $this->index = 0;
            $this->EOF = false;
            $this->numRows = count($this->rows);
            $this->row = $this->rows[0];
            $this->numFields = count($this->row);
        }


    }


    /**
     * @param $sql
     * @return bool
     */
    function Execute($sql)
    {
        //Encode to dbConfig value in table config
        $this->_getEncodings();
        $sql = \Ximdex\XML\Base::recodeSrc($sql, $this->dbEncoding);
        $this->sql = $sql;

        $this->rows = array();
        $this->EOF = true;
        $this->newID = null;

        // Change to prepare to obtain num rows
        $statement = $this->db->prepare($this->sql);
        if ($statement->execute()) {
            $this->newID = $this->db->lastInsertId();
            $this->numRows = $statement->rowCount();
            return true;
        } else {
            $this->numRows = $statement->rowCount();
            if ($this->db->errorCode() == PDO::ERR_NONE) {
                $this->numErr = null;
            } else {
                $this->numErr = $this->db->errorCode();
            }
            $this->desErr = $this->db->errorInfo();
        }


        return false;
    }

    /**
     * Execute a sql script
     *
     * @param $sql
     * @return bool
     */
    function ExecuteScript($sql)
    {
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $result = true;

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            while ($statement->nextRowset()) {/* https://bugs.php.net/bug.php?id=61613 */};
        } catch (\PDOException $e) {
            $result = false;
        }

        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        return $result;
    }

    /**
     * @return bool
     */
    function Next()
    {
        if (!$this->EOF) {
            $this->index++;
            if ($this->index >= count($this->rows)) {
                $this->EOF = true;
            } else {
                $this->row = $this->rows[$this->index];
            }
        }

        return $this->EOF;

    }

    function Go($number)
    {
        $this->index = $number;
        if ($this->index >= count($this->rows)) {
            $this->EOF = true;
        } else {
            $this->row = $this->rows[$this->index];
        }
    }
    /**
     *
     * Read dbEncoding and dbEncoding from database, Config
     * Is not possible to do in other place, because if you put in getValue, for example, or in the constructor,
     * is will create an infinite circle
     *
     */
    /**
     *
     */
    private function _getEncodings()
    {

        $sql = "select ConfigKey,ConfigValue from Config where ConfigKey='workingEncoding' or ConfigKey='dbEncoding'";

        if (($this->dbEncoding == '') && ($this->workingEncoding == '')) {
            $this->sql = $sql;
            try {
                $stm = $this->db->query($this->sql, PDO::FETCH_ASSOC);
                if ($stm === false)
                {
                	//TODO ajlucena: errors detected with this table (NY000: Table 'Config' was not locked with LOCK TABLES)
                	throw new \PDOException();
                }
                foreach ($stm as $row) {
                    $configKey = $row['ConfigKey'];
                    $configValue = $row['ConfigValue'];
                    if ($configKey == 'dbEncoding') {
                        $this->dbEncoding = $configValue;
                    } else if ($configKey == 'workingEncoding') {
                        $this->workingEncoding = $configValue;
                    }
                }


            } catch (\PDOException  $e) {
                if ($this->db->errorCode() == PDO::ERR_NONE) {
                    $this->numErr = null;
                } else {
                    $this->numErr = $this->db->errorCode();
                }
                $this->desErr = $e;
            }

        }

    }


    /**
     * Functions which obtains the current row value for a determined field
     */
    /**
     * @param $col
     * @return null|String
     */
    function GetValue($col)
    {


        if (isset($col, $this->row[$col])) {

            $this->_getEncodings();
            $value = \Ximdex\XML\Base::recodeSrc($this->row[$col], $this->workingEncoding);
            return $value;
        }

        return NULL;
    }

    /**
     * @TODO REMOVE USAGE
     * @param $value
     * @return string
     */
    public static function sqlEscapeString($value)
    {


        if (is_null($value)) {
            return 'NULL';
        }

        if (!(strlen($value) > 0)) {
            XMD_Log::info("WARNING: A SQL statement is converting an empty string to NULL");
            return 'NULL';
        }
        return self::getInstance()->db->quote($value);
    }

}