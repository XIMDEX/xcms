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

namespace Ximdex\Data;

use Ximdex\Logger;
use Ximdex\Behaviours\Collection;
use Ximdex\Runtime\Db;
use Ximdex\Utils\Messages;

/**
 * TODO Remove this defines
 */
if (!defined('MULTI')) {
    define('LOG_LEVEL_NONE', 0);
    define('LOG_LEVEL_ALL', 1);
    define('LOG_LEVEL_QUERY', 2);
    define('LOG_LEVEL_EXECUTE', 3);
    define('ALL', '*');
    define('MONO', false);
    define('MULTI', true);
}
if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', LOG_LEVEL_NONE); //Only for debugging purposes
}

class GenericData
{
    const MAX_TINYINT   = '255';
    const MAX_SMALLINT  = '65535';
    const MAX_MEDIUMINT = '16777215';
    const MAX_INT       = '4294967295';
    const MAX_BIGINT    = '18446744073709551615';
    
    /**
     *
     * @var string
     */
    const REGEXP_DATETIME = '\d{4}[-|\/]\d{1,2}[-|\/]\d{1,2}\s\d{1,2}:\d{1,2}:\d{1,2}';
    
    /**
     *
     * @var String
     */
    const REGEXP_DATE = '\d{4}[-|\/]\d{1,2}[-|\/]\d{1,2}';
    
    /**
     *
     * @var String
     */
    const REGEXP_TIMESTAMP = '\d{4}[-|\/]\d{1,2}[-|\/]\d{1,2}\s\d{1,2}:\d{1,2}:\d{1,2}';
    
    /**
     *
     * @var String
     */
    const REGEXP_TIME = '\d{1,2}:\d{1,2}:\d{1,2}';
    
    /**
     *
     * @var String
     */
    const REGEXP_YEAR = '\d{4}';

    /**
     * Nombre del campo identificador de la tabla si no se llama id
     *
     * @var string
     */
    public $_idField;
    
    /**
     * Nombre de la tabla si no se llama igual que la clase
     *
     * @var string
     */
    public $_table;
    
    /**
     * Array asociativo que contiene la estructura de la tabla
     * type = (int, varchar, text, tinytext, ...)
     * null = (true, false)
     * key = (PRI, UNI)
     * default = valor por defecto
     * extra = auto_increment
     *
     * @var array
     */
    public $_metaData = null;
    public $_cache = 0;
    public $_fieldsToTraduce = null;
    public $returnQuery = false;
    public $modelInError = false;

    /* @var $actAs array Lista de behaviours que este modelo implementa */
    public $actsAs = null;
    
    /* @var $messages Messages */
    public $messages = null;
    
    /* @var $behaviors array Lista de behavious instanciados */
    public $behaviors = null;

    /**
     * @param int $id
     */
    public function __construct($id = 0)
    {
        $this->behaviors = new Collection($this);
        if (is_null($this->_fieldsToTraduce)) {
            $this->_fieldsToTraduce = array();
        }
        $id = (int) $id;
        $dbObj = new \Ximdex\Runtime\Db();
        $this->messages = new Messages();
        $cache     = null;
        $className = null;
        if ($id > 0) {
            $query = sprintf("SELECT * FROM {$this->_table} WHERE {$this->_idField} = %d LIMIT 1", $id);
            if ((DEBUG_LEVEL == LOG_LEVEL_ALL) || (DEBUG_LEVEL == LOG_LEVEL_QUERY)) {
                $this->_logQuery($query);
            }
            $dbObj->Query($query, $this->_cache);
            if (!$dbObj->EOF) {
                reset($this->_metaData);
                foreach (array_keys($this->_metaData) as $key) {
                    if (array_key_exists($key, $dbObj->row)) {
                        $this->{$key} = $dbObj->GetValue($key);
                    } else {
                        $backtrace = debug_backtrace();
                        Logger::warning(sprintf('[CONSTRUCTOR] Inconsistency between the model and the database [inc/helper/GenericData.class.php] script: %s file: %s line: %s table: %s field: %s'
                            , $_SERVER['SCRIPT_FILENAME'], $backtrace[0]['file'], $backtrace[0]['line'], $this->_table, $key));
                        $this->modelInError = true;
                    }
                }
            }
        }
    }

    /**
     * @param $values
     * @return bool
     */
    public function _unserialize($values)
    {
        reset($this->_metaData);
        while (list($key) = each($this->_metaData)) {
            if (isset($values[$key])) {
                $this->$key = $values[$key];
            }
        }
        return !empty($this->{$this->_idField}) ? true : false;
    }

    /**
     * @param $query
     */
    public function _logQuery($query)
    {
        Logger::debug($query);
    }

    /**
     * @return bool
     */
    public function _serialize()
    {
        $values = array();
        reset($this->_metaData);
        while (list($key) = each($this->_metaData)) {
            $values[$key] = $this->{$key};
        }
        return $values ? $values : false;
    }
    
    /**
     * @return bool|null|string
     */
    public function add()
    {
        if (!$this->_applyFilter('beforeAdd')) {
            return false;
        }
        reset($this->_metaData);
        $arrayFields = array();
        $arrayValues = array();
        while (list($field, $descriptors) = each($this->_metaData)) {
            if (isset($descriptors['auto_increment']) && ('true' == $descriptors['auto_increment'])) {
                continue;
            }
            $arrayFields[] = sprintf('`%s`', $field);
            $arrayValues[] = $this->_convertToSql($this->$field, $descriptors);
        }
        $fields = implode(', ', $arrayFields);
        $values = implode(', ', $arrayValues);
        $query = "INSERT INTO {$this->_table} ($fields) VALUES ($values)";
        if ((DEBUG_LEVEL == LOG_LEVEL_ALL) || (DEBUG_LEVEL == LOG_LEVEL_EXECUTE)) {
            $this->_logQuery($query);
        }
        if ($this->returnQuery) {
            return $query;
        }
        $insertedId = null;
        if ($this->_checkDataIntegrity()) {
            $className = null;
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->Execute($query);
            if ($dbObj->numErr > 0) {
                $this->messages->add($dbObj->desErr[2], MSG_TYPE_ERROR);
                return false;
            } else {
                $isAutoField = isset($this->_metaData[$this->_idField])
                    && array_key_exists('auto_increment', $this->_metaData[$this->_idField])
                    && 'true' == $this->_metaData[$this->_idField]['auto_increment'];
                if ($isAutoField) {
                    $this->{$this->_idField} = $dbObj->newID;
                    $insertedId              = $this->{$this->_idField};
                } else {
                    $result = $this->find($this->_idField,
                        sprintf('%s=%%s', $this->_idField),
                        array($this->{$this->_idField}),
                        MONO);
                    if (count($result) == 1) {
                        Logger::warning('The table has an auto-increment field, returning id field');
                        $insertedId = $result[0];
                    }
                }
            }
        } else {
            Logger::error('Integrity errors found while executing a SQL query (' . $query . ')');
            foreach ($this->messages->messages as $message) {
                Logger::error($message['message']);
            }
        }
        $this->_applyFilter('afterAdd');
        return $insertedId ? $insertedId : false;
    }

    /**
     * @param $filter
     * @return bool
     */
    private function _applyFilter($filter)
    {
        $result = true;
        $result = $result && $this->behaviors->$filter($this);
        $this->_mergeMessagesFromBehaviors();
        return $result && $this->$filter();

    }

    private function _mergeMessagesFromBehaviors()
    {
        if (!empty($this->behaviors)) {
            $this->messages->mergeMessages($this->behaviors->messages);
        }
    }

    /**
     * Validación/conversión por tipo de campo
     * @param $fieldValue
     * @param $fieldTypeMatches
     * @return string
     */
    public function _convertToSql($fieldValue, $fieldTypeMatches)
    {
        // TODO remove unused params
        unset($fieldTypeMatches);
        return Db::sqlEscapeString($fieldValue);
    }

    public function _checkDataIntegrity()
    {
        $dataTypeFloat  = array('float');
        $dataTypeDouble = array('double');
        $dataTypeText   = array('text', 'tinytext', 'mediumtext', 'mediumblob', 'varchar', 'char', 'longtext', 'blob', 'longblob');
        // $dataTypeDate = array('date', 'datetime', 'timestamp', 'time', 'year');
        $dataTypeInt  = array('tinyint', 'smallint', 'mediumint', 'int', 'bigint');
        $dataTypeElse = array('binary', 'decimal', 'enum', 'set');
        $_fieldTypes = array('(text|date|float|double|datetime|timestamp|time|tinytext|blob|mediumblob|mediumtext|longblob|longtext)',
            '(varchar|tinyint|smallint|mediumint|int|bigint|year|char|tinyint|binary)\(([0-9]+)\)',
            '(decimal)\(([0-9]+),([0-9]+)\)',
            '(enum|set)\((.*)\)');
        reset($this->_metaData);
        while (list($key, $descriptor) = each($this->_metaData)) {
            reset($_fieldTypes);
            $matches = array();
            while (list(, $pattern) = each($_fieldTypes)) {
                $matchesCount = preg_match("/$pattern/", $descriptor['type'], $matches);
                if ($matchesCount > 0) {
                    break;
                }

            }
            $fieldType = !empty($matches[1]) ? $matches[1] : '';
            if (empty($fieldType)) {
                $this->messages->add(sprintf(_("Error parsing the field %s"), $key), MSG_TYPE_ERROR);
            }
            $value = $this->{$key};
            if ('false' == $descriptor['not_null'] && is_null($value)) {
                continue;
            }
            if (('true' == $descriptor['not_null']) && !isset($descriptor['auto_increment'])) {
                if (empty($value) && !in_array($fieldType, $dataTypeInt)) {
                    $this->messages->add(sprintf(_("You must set the field %s"), $key), MSG_TYPE_ERROR);
                }
            }
            switch ($fieldType) {
                case in_array($fieldType, $dataTypeFloat):
                case in_array($fieldType, $dataTypeDouble):
                    if (!is_numeric($value)) {
                        $this->messages->add(sprintf(_("The field %s has an invalid format"), $key), MSG_TYPE_ERROR);
                    }
                    break;
                case in_array($fieldType, $dataTypeText):
                    break;
                case 'datetime':
                    if (!preg_match(sprintf('/%s/', self::REGEXP_DATETIME), $value)) {
                        $this->messages->add(sprintf(_('The field %s has not the correct format'), $key)
                            , MSG_TYPE_ERROR);
                    }
                    break;
                case 'date':
                    if (!preg_match(sprintf('/%s/', self::REGEXP_DATE), $value)) {
                        $this->messages->add(sprintf(_('The field %s has not the correct format'), $key)
                            , MSG_TYPE_ERROR);
                    }
                    break;
                case 'timestamp':
                    if (!preg_match(sprintf('/%s/', self::REGEXP_TIMESTAMP), $value)) {
                        $this->messages->add(sprintf(_('The field %s has not the correct format'), $key)
                            , MSG_TYPE_ERROR);
                    }
                    break;
                case 'time':
                    if (!preg_match(sprintf('/%s/', self::REGEXP_TIME), $value)) {
                        $this->messages->add(sprintf(_('The field %s has not the correct format'), $key)
                            , MSG_TYPE_ERROR);
                    }
                    break;
                case 'year':
                    if (!preg_match(sprintf('/%s/', self::REGEXP_YEAR), $value)) {
                        $this->messages->add(sprintf(_('The field %s has not the correct format'), $key)
                            , MSG_TYPE_ERROR);
                    }
                    break;
                case in_array($fieldType, $dataTypeInt):
                    if (!is_int((int) $value)) {
                        $this->messages->add(sprintf(_("The field %s has an invalid format"), $key), MSG_TYPE_ERROR);
                    }
                    switch ($fieldType) {
                        case 'tinyint':
                            if ((int) $value > self::MAX_TINYINT) {
                                $this->messages->add(sprintf(_('The field %s has exceded the maximum size of %d with the value %s'),
                                    $key, self::MAX_TINYINT, $value), MSG_TYPE_ERROR);
                            }
                            break;
                        case 'smallint':
                            if ((int) $value > self::MAX_SMALLINT) {
                                $this->messages->add(sprintf(_('The field %s has exceded the maximum size of %d with the value %s'),
                                    $key, self::MAX_SMALLINT, $value), MSG_TYPE_ERROR);
                            }
                            break;
                        case 'mediumint':
                            if ((int) $value > self::MAX_MEDIUMINT) {
                                $this->messages->add(sprintf(_('The field %s has exceded the maximum size of %d with the value %s'),
                                    $key, self::MAX_MEDIUMINT, $value), MSG_TYPE_ERROR);
                            }
                            break;
                        case 'int':
                            if ((int) $value > self::MAX_INT) {
                                $this->messages->add(sprintf(_('The field %s has exceded the maximum size of %d with the value %s'),
                                    $key, self::MAX_INT, $value), MSG_TYPE_ERROR);
                            }
                            break;
                        case 'bigint':
                            if ((int) $value > self::MAX_BIGINT) {
                                $this->messages->add(sprintf(_('The field %s has exceded the maximum size of %d with the value %s'),
                                    $key, self::MAX_BIGINT, $value), MSG_TYPE_ERROR);
                            }
                            break;
                    }
                    break;
                case in_array($fieldType, $dataTypeElse):
                    break;
            }
        }
        return (!($this->messages->count(MSG_TYPE_ERROR) > 0));
    }

    /**
     * Ejecuta una consulta simple contra la base de datos y devuelve un array con los valores obtenidos
     * 
     * @param string $fields
     * @param string $condition
     * @param null $params
     * @param bool $returnType
     * @param bool $escape
     * @param string $index
     * @param string $order
     * @param string $groupBy
     * @return array|bool
     */
    public function find($fields = ALL, $condition = '', $params = null, $returnType = MULTI, $escape = true, string $index = null
        , string $order = null, string $groupBy = null)
    {
        $condition = $this->_getCondition($condition, $params, $escape);
        $query     = sprintf(
            'SELECT %s FROM %s WHERE %s',
            $fields,
            $this->_table,
            empty($condition) ? '1' : $condition
        );
        if ($groupBy) {
            $query .= ' GROUP BY ' . $groupBy;
        }
        if ($order) {
            $query .= ' ORDER BY ' . $order;
        }
        return $this->query($query, $returnType, $fields, $index);
    }

    /**
     * @param $condition
     * @param $params
     * @param $escape
     * @return mixed
     */
    public function _getCondition($condition, $params, $escape)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        if (is_null($params)) {
            $params = array();
        }
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if ($escape) {
                    $params[$key] = $dbObj->sqlEscapeString($value);
                } else {
                    $params[$key] = $value;
                }
            }
        }
        if (!empty($condition) && !is_null($params)) {
            $value = sprintf('$condition = sprintf("%s", "%s");', $condition, implode('", "', $params));
            eval($value);
        }
        return $condition;
    }
    
    /**
     * @param string $query
     * @param string $returnType
     * @param string $fields
     * @param string $indexField
     * @return boolean|array
     */
    public function query($query, $returnType = MULTI, $fields = '', string $indexField = null)
    {
        $this->_applyFilter('beforeFind');
        $dbObj = new \Ximdex\Runtime\Db();
        if ((DEBUG_LEVEL == LOG_LEVEL_ALL) || (DEBUG_LEVEL == LOG_LEVEL_QUERY)) {
            $this->_logQuery($query);
        }
        if ($dbObj->query($query) === false) {
            return false;
        }
        $result = array();
        while (!$dbObj->EOF) {
            if (MULTI == $returnType) {
                $subResult = array();
                $index = 0;
                foreach ($dbObj->row as $key => $value) {
                    $subResult[$index] = $this->_getValueForFind($key, $value);
                    $subResult[$key] = $this->_getValueForFind($key, $value);
                    $index++;
                }
                $result[] = $subResult;
            } elseif (MONO == $returnType) {
                $subResult = null;
                foreach ($dbObj->row as $key => $value) {
                    $subResult = $this->_getValueForFind($key, $value);
                }
                if ($indexField) {
                    if (!isset($dbObj->row[$indexField])) {
                        Logger::error('Field ' . $indexField . ' does not exist in query: ' . $query);
                        return false;
                    }
                    $result[$dbObj->row[$indexField]] = $subResult;
                }else{
                    $result[] = $subResult;
                }
            }
            $dbObj->next();
        }
        $this->_applyFilter('afterFind');
        return $result;
    }

    /**
     * @param $key
     * @param $value
     * @return bool|string
     */
    public function _getValueForFind($key, $value)
    {
        if (!array_key_exists($key, $this->_metaData)) {
            return $value;
        }
        $tmpValue    = $this->$key;
        $this->$key  = $value;
        $returnValue = $this->get($key);
        $this->$key  = $tmpValue;
        return $returnValue;
    }

    /**
     * @param $attribute
     * @return bool|string
     */
    public function get($attribute)
    {
        if (empty($attribute)) {
            return false;
        }
        if (!array_key_exists($attribute, $this->_metaData)) {
            $backtrace = debug_backtrace();
            Logger::error(sprintf('[GET] Trying to get a property that does not exist'
                . ' [inc/helper/GenericData.class.php] script: %s file: %s line: %s table: %s key: %s',
                $_SERVER['SCRIPT_FILENAME'],
                $backtrace[0]['file'],
                $backtrace[0]['line'],
                $this->_table,
                $attribute));
            $this->modelInError = true;
            return false;
        }
        if (!empty($this->_fieldsToTraduce) && in_array($attribute, $this->_fieldsToTraduce)) {
            return _($this->$attribute);
        }
        return $this->$attribute;
    }

    /**
     * @return bool
     */
    public function beforeAdd()
    {
        return true;
    }

    public function afterAdd()
    {
        return true;
    }

    public function beforeUpdate()
    {
        return true;
    }

    public function afterUpdate()
    {
        return true;
    }
    
    public function beforeDelete()
    {
        return true;
    }

    public function afterDelete()
    {
        return true;
    }

    public function beforeFind()
    {
        return true;
    }

    public function afterFind()
    {
        return true;
    }

    /**
     * @return bool|int|null|string
     */
    public function update()
    {
        if (!$this->_applyFilter('beforeUpdate')) {
            return false;
        }
        reset($this->_metaData);
        $arraySets = array();
        while (list($field, $descriptors) = each($this->_metaData)) {
            $arraySets[] = sprintf("`%s` = %s",
                $field,
                $this->_convertToSql($this->$field, $descriptors));
        }
        $sets  = implode(', ', $arraySets);
        $query = sprintf('UPDATE %s SET %s WHERE %s = %d', $this->_table, $sets, $this->_idField,
            $this->{$this->_idField});
        if ((DEBUG_LEVEL == LOG_LEVEL_ALL) || (DEBUG_LEVEL == LOG_LEVEL_EXECUTE)) {
            $this->_logQuery($query);
        }
        if ($this->returnQuery) {
            return $query;
        }
        $updatedRows = null;
        if ($this->_checkDataIntegrity()) {
            $dbObj = new \Ximdex\Runtime\Db();
            $res = $dbObj->Execute($query);
            if ($res === false) {
                return false;
            }
            $updatedRows = $dbObj->numRows;
        }
        $this->_applyFilter('afterUpdate');
        return $updatedRows ? $updatedRows : null;
    }

    /**
     * @return bool|int|string
     */
    public function delete()
    {
        if (!$this->_applyFilter('beforeDelete')) {
            return false;
        }
        $query = sprintf("DELETE FROM {$this->_table} WHERE {$this->_idField} = %d",
            $this->{$this->_idField});
        if ((DEBUG_LEVEL == LOG_LEVEL_ALL) || (DEBUG_LEVEL == LOG_LEVEL_EXECUTE)) {
            $this->_logQuery($query);
        }
        if ($this->returnQuery) {
            return $query;
        }
        $dbObj  = new \Ximdex\Runtime\Db();
        $result = $dbObj->Execute($query);

        $this->_applyFilter('afterDelete');
        return $dbObj->numRows;
    }

    /**
     * Deletes a set of elements
     * 
     * @param string $condition
     * @param null $params
     * @param bool $escape
     * @return bool
     */
    public function deleteAll($condition = '', $params = null, $escape = true)
    {
        $condition = $this->_getCondition($condition, $params, $escape);
        $query = sprintf("DELETE FROM %s WHERE %s", $this->_table, $condition);
        return $this->execute($query);
    }

    /**
     * @param $query
     * @return bool
     */
    public function execute($query)
    {
        if (preg_match('/update/i', $query) > 0) {
            $this->_applyFilter('beforeUpdate');
        } else if (preg_match('/delete/i', $query) > 0) {
            $this->_applyFilter('beforeDelete');
        } else {
            Logger::warning('No pre-filter applied for query: ' . $query);
        }
        $dbObj = new \Ximdex\Runtime\Db();
        if ((DEBUG_LEVEL == LOG_LEVEL_ALL) || (DEBUG_LEVEL == LOG_LEVEL_EXECUTE)) {
            $this->_logQuery($query);
        }
        $result = $dbObj->execute($query);
        if (preg_match('/update/i', $query) > 0) {
            $this->_applyFilter('afterUpdate');
        } else if (preg_match('/delete/i', $query) > 0) {
            $this->_applyFilter('afterDelete');
        } else {
            Logger::warning('No post-filter applied for query: ' . $query);
        }
        return $result;
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function set($attribute, $value)
    {
        if (empty($attribute)) {
            return false;
        }
        if (!array_key_exists($attribute, $this->_metaData)) {
            $backtrace = debug_backtrace();
            Logger::error(sprintf('[SET] Trying to set a property that does not exist'
                . ' [inc/helper/GenericData.class.php] script: %s file: %s line: %s table: %s key: %s value: %s',
                $_SERVER['SCRIPT_FILENAME'],
                $backtrace[0]['file'],
                $backtrace[0]['line'],
                $this->_table,
                $attribute,
                $value));
            $this->modelInError = true;
            return false;
        }
        $this->$attribute = $value;
        return true;
    }

    /**
     * <p>Get all rows for the current table.</p>
     * <p>This is a facade method and it just call find method without parameters.</p>
     * 
     * @return array
     */
    public function findAll()
    {
        return $this->find();
    }

    /**
     * @param string $condition
     * @param null $params
     * @return bool|int
     */
    public function count($condition = '', $params = null)
    {
        $result = $this->find('count(1)', $condition, $params, MONO);
        return intval($result[0]) ?? false;
    }

    /**
     * @param $varsToLoad
     * @return bool
     */
    public function loadFromArray($varsToLoad)
    {
        if (!is_array($varsToLoad)) {
            return false;
        }
        foreach ($varsToLoad as $key => $value) {
            $this->{$key} = $value;
        }
        return true;
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     */
    public function call__($method, $params)
    {
        /**
         * Overloadable añade un nivel de array a los parámetros
         */
        return $this->behaviors->$method($this, isset($params[0]) ? $params[0] : null);
    }
    
    /**
     * Check if exist in database an element with the same metadata property already
     * Useful if you want to know if an entity is created yet to avoid duplicates
     * May be use in add calls under specified conditions
     * IMPORTANT: The returned value will be FALSE in error, element ID if exists and NULL is not
     * 
     * @param string $idName
     * @return boolean|mixed|NULL
     */
    public function exists($idName = 'id')
    {
        reset($this->_metaData);
        $arrayFields = array();
        $arrayValues = array();
        while (list($field, $descriptors) = each($this->_metaData)) {
            if (isset($descriptors['auto_increment']) && ('true' == $descriptors['auto_increment'])) {
                continue;
            }
            $arrayFields[] = sprintf('`%s`', $field);
            $arrayValues[] = $this->_convertToSql($this->$field, $descriptors);
        }
        $query = "SELECT $idName FROM {$this->_table} WHERE 1 = 1";
        $i = 0;
        foreach ($arrayFields as $field) {
            if ($field == $idName) {
                continue;
            }
            if ($arrayValues[$i] === null) {
                $query .= ' and ' . $field . ' is null';
            }
            else {
                $query .= ' and ' . $field . ' = ' . $arrayValues[$i];
            }
            $i++;
        }
        if (!$this->_checkDataIntegrity()) {
            Logger::error('Integrity errors found while executing a SQL query (' . $query . ')');
            foreach ($this->messages->messages as $message) {
                Logger::error($message['message']);
            }
            return false;
        }
        $res = $this->query($query);
        if ($res === false) {
            return false;
        }
        if (isset($res[0][$idName])) {
            return $res[0][$idName];
        }
        return null;
    }

    public function __call($method, $params = array())
    {
        if (!method_exists($this, 'call__')) {
            trigger_error(sprintf( __('Magic method handler call__ not defined in %s', true), get_class($this)), E_USER_ERROR);
        } else {
            return $this->call__($method, $params);
        }
    }
    
    public function loader(string $id) : void
    {
        $this->__construct($id);
    }
}