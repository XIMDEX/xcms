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


if (!defined('DB_LOADED')) {
    define("DB_LOADED", 1);
} else {
    return null;
}



$debug = NULL;

Class DB  Extends DB_legacy {

}

// Class for mySql database management
class DB_legacy
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
        //return new  Db($conf);
        return new   Db();
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

        // todo remove cache parameter
        unset($cache) ;


        $this->_getEncodings();
        $sql = \Ximdex\XML\Base::recodeSrc($sql, $this->dbEncoding);

        $this->sql = $sql;

        //error_log( $this->sql ) ;
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


        // error_log( "TOTAL: " . count( $this->rows )  ) ;

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
        error_log(  $sql  ) ;
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

    /**
     * @return bool
     */
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

    function Go( $number ) {
        $this->index = $number ;
        if ( $this->index >= count( $this->rows ) ) {
            $this->EOF = true ;
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
     */
    /**
     * @param $col
     * @return null|String
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

        // error_log( "escape: ". $value ) ;

        if (is_null($value)) {
            return 'NULL';
        }

        if (!(strlen($value) > 0)) {
            XMD_Log::info("WARNING: A SQL statement is converting an empty string to NULL");
            return 'NULL';
        }
        return self::getInstance()->db->quote( $value ) ;
    }

}