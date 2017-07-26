<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 11/05/16
 * Time: 15:10
 */
namespace Ximdex\Runtime;

use Ximdex\Logger as XMD_Log;

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
    
    private $TIME_TO_RECONNECT = 30;	//sleeping time to reconnect to the database in seconds

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
        	if (is_null(self::$defaultConf)) {
				self::$defaultConf = App::getInstance()->getValue('default.db', 'db');
			}
			$conf = self::$defaultConf;
        }
        $this->db = App::Db($conf);
    }

   	/**
   	 * Reconnect the Database
   	 * @return boolean
   	 */
    public function reconectDataBase(){
         $dbConfig = App::getInstance()->getValue('db', 'db');
         if ( !empty( $dbConfig ) )
         {
         	try
         	{
            	$dbConn = new \PDO("{$dbConfig['type']}:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['db']}", $dbConfig['user'], $dbConfig['password']);
         	}
         	catch (\PDOException $e)
         	{
         		XMD_Log::error('Can\'t reconnect to database at ' . $dbConfig['host'] . ':' . $dbConfig['port']);
         		return false;
         	}
            $dbConn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->db = $dbConn;
            $idconfig = uniqid();
            App::addDbConnection($dbConn, $idconfig);
            self::$defaultConf = $idconfig;
            XMD_Log::info('Reconnection to database at ' . $dbConfig['host'] . ':' . $dbConfig['port'] . ' has been stablished correctly');
            return true;
         }
     }

    public function Query($sql, $cache = false)
    {

        // todo remove cache parameter
        unset($cache);


        if (!$this->_getEncodings())
        {
            XMD_Log::error($this->desErr);
            return false;
        }
        $sql = \Ximdex\XML\Base::recodeSrc($sql, $this->dbEncoding);

        $this->sql = $sql;

        $this->rows = array();

        $this->stm = $this->db->query($this->sql, \PDO::FETCH_ASSOC);
        
        if ($this->stm === false) {
            if ($this->db->errorCode() == \PDO::ERR_NONE) {
                $this->numErr = null;
            } else {
                $this->numErr = $this->db->errorCode();
            }
            $error = $this->db->errorInfo();
            XMD_Log::error($error[2] . '. (SQL: ' . $this->sql . ')');
            $this->database_error();
            return false;
        }

        foreach ($this->stm as $row) {

            $this->rows[] = $row;

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

		return true;
    }


    /**
     * @param $sql
     * @return bool
     */
    function Execute($sql)
    {
        //Encode to dbConfig value in table config
        if (!$this->_getEncodings())
        {
            XMD_Log::error($this->desErr);
            return false;
        }
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
        }
        if ($this->db->errorCode() == \PDO::ERR_NONE) {
            $this->numErr = null;
        } else {
            $this->numErr = $this->db->errorCode();
        }
        $error = $this->db->errorInfo();
        $this->desErr = $error[2];
        XMD_Log::error($error[2] . '. (SQL: ' . $this->sql . ')');
        $this->database_error();
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
            XMD_Log::error($e->errorInfo[2]);
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
    private function _getEncodings()
    {

        $sql = "select ConfigKey,ConfigValue from Config where ConfigKey='workingEncoding' or ConfigKey='dbEncoding'";

        if (($this->dbEncoding == '') && ($this->workingEncoding == '')) {
            $this->sql = $sql;
            $stm = $this->db->query($this->sql, \PDO::FETCH_ASSOC);
            if ($stm === false)
            {
                $error = $this->error();
            	XMD_Log::error('Can\'t get encondings types (' . $error[2] . ')');
            	$this->database_error();
                return false;
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
        }
        return true;
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

            if (!$this->_getEncodings())
            {
                XMD_Log::error($this->desErr);
                return false;
            }
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
            //XMD_Log::warning("A SQL statement is converting an empty string to NULL");
            return 'NULL';
        }
        return self::getInstance()->db->quote($value);
    }

    public function error()
    {
    	return $this->db->errorInfo();
    }
    
    /**
     * Operations with some specified database errors
     * @param Db $db
     */
    public function database_error(Db $db = null)
    {
    	if (!$db)
    	{
    		$db = $this->db;
    	}
    	$error = $db->errorInfo();
    	if ($error[0] == 'HY000' and $error[1] == 2006)
    	{
    		//MySQL server has gone away error; we will sleep for a few seconds and try again a new connection later
    		do
    		{
    			//we will do a loop until the connection has been stablished
    			XMD_Log::error('Connection to database has been lost. Trying to reconnect in ' . $this->TIME_TO_RECONNECT . ' seconds');
    			$res = $this->reconectDataBase();
    			sleep($this->TIME_TO_RECONNECT);
    		}
    		while (!$res);
    		XMD_Log::info('Reconnecting to database has been executed successfully');
    		return true;
    	}
    	return false;
    }
}