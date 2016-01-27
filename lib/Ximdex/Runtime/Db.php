<?php

namespace  Ximdex\Runtime ;

use Mockery\CountValidator\Exception;
use PDO ;

class Db
{
    /**
     * @var null|\PDO
     */
    private $db = null;

    private $sql = '';
    private $dbEncoding = '' ;
    private $workingEncoding ;
    private $rows = array();
    public $EOF = true ;
    public $row = array();
    public $numRows = 0 ;
    public $numFields = 0 ;
    private $index = 0;
    public $numErr = null ;
    public $desErr = null ;
    public $newID = null ;

    /**
     * @var \PDOStatement
     */
    private $stm = null ;

    /**
     * @param string $conf
     * @return Db
     * @throws \Exception
     */
    static public function getInstance($conf = null)
    {
        return new  Db($conf);
    }

    /**
     * Db constructor.
     * @param string|null $conf
     */
    public function __construct($conf = null)
    {
        $this->db = App::Db($conf);

    }

    public function Query($sql, $cache = false)
    {
        $this->_getEncodings();
        $sql = \Ximdex\XML\Base::recodeSrc($sql, $this->dbEncoding);

        $this->sql = $sql;

        error_log( $this->sql ) ;
        $this->rows = array();

        try {
            $this->stm = $this->db->query($this->sql, PDO::FETCH_ASSOC);

            if ( empty( $this->stm )) {
                throw new \Exception('Bad Query: ' .  $this->sql );
            }

            foreach( $this->stm as $row ) {

                $this->rows[] = $row ;

            }

        } catch  ( \Exception $e  ) {

                $this->numErr = $this->db->errorCode() ;

        }

        if ( count( $this->rows ) == 0 ) {

            $this->EOF = true ;

        } else {
            $this->index = 0 ;
            $this->EOF = false ;
            $this->numRows = count( $this->rows ) ;
            $this->row = $this->rows[0] ;
            $this->numFields = count( $this->row ) ;
        }


        error_log( "TOTAL: " . count( $this->rows )  ) ;

    }



    /**
     * Function which performs a BD query
     * @param $sql
     * @return unknown_type
     */
    function Execute($sql)
    {
            //Encode to dbConfig value in table config
            $this->_getEncodings();
            $sql = \Ximdex\XML\Base::recodeSrc($sql, $this->dbEncoding);

            $this->sql = $sql;

            $this->rows = array();
            $this->EOF = true ;
            $this->newID = null ;

           if ( $this->db->exec($this->sql ) ) {
               $this->newID = $this->db->lastInsertId() ;
               return true ;
           } else {
               $this->numErr = $this->db->errorCode() ;
               $this->desErr = $this->db->errorInfo() ;
        }



        return false;
    }

    function Next()
    {
        if ( !$this->EOF ) {
            $this->index++ ;
            if ( $this->index >= count( $this->rows ) ) {
                $this->EOF = true ;
            } else {
                $this->row = $this->rows[$this->index];
            }
        }

        return $this->EOF;

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
            try {
                $stm = $this->db->query($this->sql, PDO::FETCH_ASSOC);

                foreach( $stm as $row ) {
                    $configKey = $row['ConfigKey'];
                    $configValue = $row['ConfigValue'];
                    if ($configKey == 'dbEncoding') {
                        $this->dbEncoding = $configValue;
                    } else if ($configKey == 'workingEncoding') {
                        $this->workingEncoding = $configValue;
                    }
                }


            } catch (\PDOException  $e) {
                $this->numErr = $e;
                $this->desErr = $e;

            }

        }

    }


    /**
     * Functions which obtains the current row value for a determined field
     * @param $col
     * @return unknown_type
     */
    function GetValue($col)
    {


        if ( isset(  $col, $this->row[ $col ] )) {

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


        return "'" . @mysql_real_escape_string($value) . "'";
    }

}