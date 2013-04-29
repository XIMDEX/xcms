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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */




if (!defined('XIMDEX_ROOT_PATH')) {
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

require_once(XIMDEX_ROOT_PATH . '/inc/helper/Messages.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/helper/Cache.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/db/db.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/App.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/patterns/Overloadable.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/behaviors/BehaviorCollection.class.php');

define('LOG_LEVEL_NONE', 0);
define('LOG_LEVEL_ALL', 1);
define('LOG_LEVEL_QUERY', 2);
define('LOG_LEVEL_EXECUTE', 3);
define('ALL', '*');
define('MONO', false);
define('MULTI', true);

if (!defined('DEBUG_LEVEL')) {
	define('DEBUG_LEVEL', LOG_LEVEL_NONE); //Only for debugging purposes
}

class GenericData extends Overloadable {

	/**
	 *
	 * @var unknown_type
	 */
	const MAX_TINYINT = '255';
	/**
	 *
	 * @var unknown_type
	 */
	const MAX_SMALLINT = '65535';
	/**
	 *
	 * @var unknown_type
	 */
	const MAX_MEDIUMINT = '16777215';
	/**
	 *
	 * @var unknown_type
	 */
	const MAX_INT = '4294967295';
	/**
	 *
	 * @var unknown_type
	 */
	const MAX_BIGINT = '18446744073709551615';
	/**
	 *
	 * @var unknown_type
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
	var $_idField;
	/**
	 * Nombre de la tabla si no se llama igual que la clase
	 *
	 * @var string
	 */
	var $_table;
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
	var $_metaData = null;

	var $_cache = 0;
	var $_useMemCache = 0;

	var $_fieldsToTraduce = NULL;

	var $returnQuery = false;

	var $modelInError = false;

	/* @var $actAs array Lista de behaviours que este modelo implementa*/
	var $actsAs = null;
	/* @var $behaviors array Lista de behavious instanciados*/
	private $behaviors = null;

	/* @var $messages Messages */
	var $messages = null;


	/**
	 * Constructor
	 * @param int $id
	 * @return unknown_type
	 */
	function GenericData($id = 0) {

		$this->behaviors = new BehaviorCollection($this);

		if (is_null($this->_fieldsToTraduce)) {
			$this->_fieldsToTraduce = array();
		}
		$id = (int) $id;
		$dbObj = new DB();
		$this->messages = new Messages();
		if ($id > 0) {
			if ((bool) $this->_useMemCache) {
				$cache = new Cache();
				$className = get_class($this);
				$result = $cache->get($className . $id);
				if ($result) {
					$unserialized = $this->_unserialize($result);
					if ($unserialized) {
						return;
					}
				}
			}
			$query = sprintf("SELECT * FROM {$this->_table} WHERE {$this->_idField} = %d LIMIT 1",
			$id);
			if ((DEBUG_LEVEL == LOG_LEVEL_ALL) || (DEBUG_LEVEL == LOG_LEVEL_QUERY)) {
				$this->_logQuery($query);
			}
			$dbObj->Query($query, $this->_cache);
			if (!$dbObj->EOF) {
				reset($this->_metaData);
				while (list($key,) = each($this->_metaData)) {
					if (array_key_exists($key, $dbObj->row)) {
						$this->{$key} = $dbObj->GetValue($key);
					} else {
						$backtrace = debug_backtrace();
						error_log(sprintf('[CONSTRUCTOR]Inconsistencia entre el modelo y la base de datos [inc/helper/GenericData.class.php] script: %s file: %s line: %s table: %s field: %s',
						$_SERVER['SCRIPT_FILENAME'],
						$backtrace[0]['file'],
						$backtrace[0]['line'],
						$this->_table,
						$key));
						$this->modelInError = true;
					}
				}
			}
			if ((bool) $this->_useMemCache) {
				$values = $this->_serialize();
				if ($values) {
					$cache->set($className . $this->{$this->_idField}, $values);
				}
			}
		}
	}

	/**
	 *
	 * @param $filter
	 * @return unknown_type
	 */
	private function _applyFilter($filter) {
		$result = true;
		$result = $result && $this->behaviors->$filter($this);
		$this->_mergeMessagesFromBehaviors();
		return $result && $this->$filter();

	}

	/**
	 *
	 * @return unknown_type
	 */
	private function _mergeMessagesFromBehaviors() {
		if (!empty($this->behaviors)) {
			$this->messages->mergeMessages($this->behaviors->messages);
		}
	}

	/**
	 *
	 * @return unknown_type
	 */
	function add() {
		if (!$this->_applyFilter('beforeAdd')) {
			return false;
		}
		reset($this->_metaData);
		$arrayFields = array();
		$arrayValues = array();
		while (list($field, $descriptors) = each($this->_metaData)) {
			if (isset($descriptors['auto_numeric']) && ($descriptors['auto_numeric'] == 'true')) continue;
			$arrayFields[] = sprintf('`%s`', $field);
			$arrayValues[] = $this->_convertToSql($this->$field, $descriptors);
		}
		$fields = implode(', ', $arrayFields);
		$values = implode(', ', $arrayValues);
		$query = "INSERT INTO {$this->_table}"
		. "($fields) VALUES ($values)";
		if ((DEBUG_LEVEL == LOG_LEVEL_ALL) || (DEBUG_LEVEL == LOG_LEVEL_EXECUTE)) {
			$this->_logQuery($query);
		}
		if ($this->returnQuery) {
			return $query;
		}
		$insertedId = null;
		if ($this->_checkDataIntegrity()) {
			$dbObj = new DB();
			$dbObj->Execute($query);
			if ($dbObj->numErr > 0) {
				$this->messages->add($dbObj->desErr, MSG_TYPE_ERROR);
				foreach ($this->messages->messages as $message) {
					XMD_Log::error(sprintf("%s: [%s]", $message['message'], $query));
				}
				return false;
			} else {

				$className = get_class($this);

				$isAutoField = isset($this->_metaData[$this->_idField])
					&& array_key_exists('auto_increment', $this->_metaData[$this->_idField])
					&& $this->_metaData[$this->_idField]['auto_increment'] == true;

				if ($isAutoField) {
					$this->{$this->_idField} = $dbObj->newID;
					$insertedId = $this->{$this->_idField};
				} else {
					$result = $this->find($this->_idField,
							sprintf('%s=%%s', $this->_idField),
							array($this->{$this->_idField}),
							MONO);
					if (count($result) == 1) {
						XMD_Log::warning('La tabla no tiene un campo autoincremental, devolviendo campo id');
						$insertedId = $result[0];
					}
				}

				if ((bool) $this->_useMemCache) {
					$cache = new Cache();
					$className = get_class($this);
					$result = $this->_serialize();
					if ($result) {
						$cache->set($className . $this->{$this->_idField}, $result);
					}
				}

			}
		} else {
			XMD_Log::error('Integrity errors found while executing a SQL query');
			foreach ($this->messages->messages as $message) {
				XMD_Log::error($message['message']);
			}
		}
		$this->_applyFilter('afterAdd');
		return $insertedId ? $insertedId : false;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function beforeAdd(){return true;}
	function afterAdd(){return true;}
	function beforeUpdate(){return true;}
	function afterUpdate(){return true;}
	function beforeDelete(){return true;}
	function afterDelete(){return true;}
	function beforeFind(){return true;}
	function afterFind(){return true;}

	/**
	 *
	 * @return unknown_type
	 */
	function update() {
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
		$sets = implode(', ', $arraySets);
		$query = sprintf('UPDATE %s SET %s WHERE %s = %d',$this->_table,$sets,$this->_idField,
		$this->{$this->_idField});
		if ((DEBUG_LEVEL == LOG_LEVEL_ALL) || (DEBUG_LEVEL == LOG_LEVEL_EXECUTE)) {
			$this->_logQuery($query);
		}
		if ($this->returnQuery) {
			return $query;
		}
		$updatedRows = null;
		if ($this->_checkDataIntegrity()) {
			$dbObj = new DB();
			$dbObj->Execute($query);
			if (($dbObj->numRows > 0) && (bool) $this->_useMemCache) {
				$cache = new Cache();
				$className = get_class($this);
				$result = $this->_serialize();
				if ($result) {
					$cache->replace($className . $this->{$this->_idField}, $result);
				}
			}
			$updatedRows = $dbObj->numRows;
		}
		$this->_applyFilter('afterUpdate');
		return $updatedRows ? $updatedRows : NULL;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function delete() {
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
		$dbObj = new DB();
		$dbObj->Execute($query);
		if (($dbObj->numRows > 0) && (bool) $this->_useMemCache) {
			$cache = new Cache();
			$className = get_class($this);
			if ($result) {
				$cache->delete($className . $this->{$this->_idField});
			}
		}

		$this->_applyFilter('afterDelete');
		return $dbObj->numRows;
	}

	/**
	 * Deletes a set of elements
	 *
	 * @access public
	 * @param array conditions field_name => $field_value
	 * @param boolean escape
	 */
	function deleteAll($condition = '', $params = NULL, $escape = true) {
		$condition = $this->_getCondition($condition, $params, $escape);
		$query = sprintf("DELETE FROM %s WHERE %s",
		$this->_table, $condition);
		return $this->execute($query);
	}

	/**
	 *
	 * @param $attribute
	 * @param $value
	 * @return unknown_type
	 */
	function set($attribute, $value){
		if (empty($attribute)) {
			return false;
		}
		if (!array_key_exists($attribute, $this->_metaData)) {
			$backtrace = debug_backtrace();
			error_log(sprintf('[SET]Intentando setear una propiedad que no existe'
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
	 *
	 * @param $attribute
	 * @return unknown_type
	 */
	function get($attribute) {
		if (empty($attribute)) return false;
		if (!array_key_exists($attribute, $this->_metaData)) {
			$backtrace = debug_backtrace();
			error_log(sprintf('[GET]Intentando hacer un get de una propiedad que no existe'
			. ' [inc/helper/GenericData.class.php] script: %s file: %s line: %s table: %s key: %s',
			$_SERVER['SCRIPT_FILENAME'],
			$backtrace[0]['file'],
			$backtrace[0]['line'],
			$this->_table,
			$attribute));
			$this->modelInError = true;
			return false;
		}

		if (!empty( $this->_fieldsToTraduce) && in_array($attribute, $this->_fieldsToTraduce)) {
			return _($this->$attribute);
		}
		return $this->$attribute;
	}

	/**
	 * Validación/conversión por tipo de campo
	 *
	 * @param unknown_type $fieldValue
	 * @param array $fieldTypeMatches
	 * @return unknown
	 */
	function _convertToSql($fieldValue, $fieldTypeMatches) {
		return DB::sqlEscapeString($fieldValue);
	}

	/**
	 *
	 * @return unknown_type
	 */
	function _checkDataIntegrity() {
		$dataTypeFloat = array('float');
		$dataTypeDouble = array('double');
		$dataTypeText = array('text', 'tinytext', 'mediumtext', 'mediumblob', 'varchar', 'char', 'longtext', 'blob', 'longblob');
		$dataTypeDate = array('date', 'datetime', 'timestamp', 'time', 'year');
		$dataTypeInt = array('tinyint', 'smallint', 'mediumint', 'int', 'bigint');
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
				if ($matchesCount > 0) break;
			}

			$fieldType = !empty($matches[1]) ? $matches[1] : '';

			if (empty($fieldType)) {
				$this->messages->add(sprintf(_("Error de parseo en el campo %s"), $key), MSG_TYPE_ERROR);
			}
			$value = $this->{$key};

			if ($descriptor['not_null'] == 'false' && is_null($value)) {
				continue;
			}

			if (($descriptor['not_null'] == 'true') && !isset($descriptor['auto_increment'])) {
				if (empty($value) && !in_array($fieldType, $dataTypeInt)) {
					$this->messages->add(sprintf(_("Debe rellenar el campo %s"), $key), MSG_TYPE_ERROR);
				}
			}
			switch ($fieldType) {
				case in_array($fieldType, $dataTypeFloat):
				case in_array($fieldType, $dataTypeDouble):
					if (!is_numeric($value)) $this->messages->add(sprintf(_("El campo %s tiene un formato inválido"), $key), MSG_TYPE_ERROR);
					break;
				case in_array($fieldType, $dataTypeText):
					/*				case 'text':
					 case 'tinytext':
					 case 'mediumtext':
					 case 'mediumblob':
					 case 'varchar':
					 case 'char':
					 case 'longtext':
					 case 'blob':
					 case 'longblob':*/
					//if (!is_string($value)) $this->messages->add("El campo $key tiene un formato inválido", MSG_TYPE_ERROR);
					break;
					//				case in_array($fieldType, $dataTypeDate):
				case 'datetime':
					if (!preg_match(sprintf('/%s/', self::REGEXP_DATETIME), $value)) {
						$this->messages->add(sprintf(_('El campo %s no tiene el formato correcto'), $key)
						, MSG_TYPE_ERROR);
					}
					break;
				case 'date':
					if (!preg_match(sprintf('/%s/', self::REGEXP_DATE), $value)) {
						$this->messages->add(sprintf(_('El campo %s no tiene el formato correcto'), $key)
						, MSG_TYPE_ERROR);
					}
					break;
				case 'timestamp':
					if (!preg_match(sprintf('/%s/', self::REGEXP_TIMESTAMP), $value)) {
						$this->messages->add(sprintf(_('El campo %s no tiene el formato correcto'), $key)
						, MSG_TYPE_ERROR);
					}
					break;
				case 'time':
					if (!preg_match(sprintf('/%s/', self::REGEXP_TIME), $value)) {
						$this->messages->add(sprintf(_('El campo %s no tiene el formato correcto'), $key)
						, MSG_TYPE_ERROR);
					}
					break;
				case 'year':
					if (!preg_match(sprintf('/%s/', self::REGEXP_YEAR), $value)) {
						$this->messages->add(sprintf(_('El campo %s no tiene el formato correcto'), $key)
						, MSG_TYPE_ERROR);
					}
					break;
				case in_array($fieldType, $dataTypeInt):
					if (!is_int((int)$value)) $this->messages->add(sprintf(_("El campo %s tiene un formato inválido"), $key), MSG_TYPE_ERROR);
					switch ($fieldType) {
						case 'tinyint':
							if ((int)$value > self::MAX_TINYINT) {
								$this->messages->add(sprintf(_('El campo %s ha excedido el tamaño máximo de %d con el valor %s'),
								$key, self::MAX_TINYINT, $value), MSG_TYPE_ERROR);
							}
							break;
						case 'smallint':
							if ((int)$value > self::MAX_SMALLINT) {
								$this->messages->add(sprintf(_('El campo %s ha excedido el tamaño máximo de %d con el valor %s'),
								$key, self::MAX_SMALLINT, $value), MSG_TYPE_ERROR);
							}
							break;
						case 'mediumint':
							if ((int)$value > self::MAX_MEDIUMINT) {
								$this->messages->add(sprintf(_('El campo %s ha excedido el tamaño máximo de %d con el valor %s'),
								$key, self::MAX_MEDIUMINT, $value), MSG_TYPE_ERROR);
							}
							break;
						case 'int':
							if ((int)$value > self::MAX_INT) {
								$this->messages->add(sprintf(_('El campo %s ha excedido el tamaño máximo de %d con el valor %s'),
								$key, self::MAX_INT, $value), MSG_TYPE_ERROR);
							}
							break;
						case 'bigint':
							if ((int)$value > self::MAX_BIGINT) {
								$this->messages->add(sprintf(_('El campo %s ha excedido el tamaño máximo de %d con el valor %s'),
								$key, self::MAX_BIGINT, $value), MSG_TYPE_ERROR);
							}
							break;
					}
						case in_array($fieldType, $dataTypeElse):
							/*				case 'binary':
							 case 'decimal':
							 case 'enum':
							 case 'set':*/

							break;
			}
		}
		return (!($this->messages->count(MSG_TYPE_ERROR) > 0));
	}

	/**
	 *
	 * @param $query
	 * @param $returnType
	 * @param $fields
	 * @return unknown_type
	 */
	function query($query, $returnType = MULTI, $fields = '' ) {
		$this->_applyFilter('beforeFind');
		$dbObj = new DB();

		if ((DEBUG_LEVEL == LOG_LEVEL_ALL) || (DEBUG_LEVEL == LOG_LEVEL_QUERY)) {
			$this->_logQuery($query);
		}

		$dbObj->query($query);
		if (!($dbObj->numRows > 0)) {
			return NULL;
		}

		$result = array();
		while (!$dbObj->EOF) {
			if ($returnType == MULTI) {
				$subResult = array();
				foreach ($dbObj->row as $key => $value) {
					$subResult[$key] = $this->_getValueForFind($key, $value);
				}
				$result[] = $subResult;
			} elseif ($returnType == MONO) {
				$result[] = $this->_getValueForFind($fields, $dbObj->row[0]);
			}
			$dbObj->next();
		}
		$this->_applyFilter('afterFind');
		return $result;
	}

	/**
	 *
	 * @param $query
	 * @return unknown_type
	 */
	function execute($query) {
		if (preg_match('/update/i', $query) > 0) {
			$this->_applyFilter('beforeUpdate');
		} else if(preg_match('/delete/i', $query) > 0) {
			$this->_applyFilter('beforeDelete');
		} else {
			XMD_Log::warning('No pre-filter applied for query: ' . $query);
		}

		$dbObj = new DB();

		if ((DEBUG_LEVEL == LOG_LEVEL_ALL) || (DEBUG_LEVEL == LOG_LEVEL_EXECUTE)) {
			$this->_logQuery($query);
		}

		$result = $dbObj->execute($query);

		if (preg_match('/update/i', $query) > 0) {
			$this->_applyFilter('afterUpdate');
		} else if(preg_match('/delete/i', $query) > 0) {
			$this->_applyFilter('afterDelete');
		} else {
			XMD_Log::warning('No post-filter applied for query: ' . $query);
		}
		return $result;
	}

	/**
	 *
	 * @param $condition
	 * @param $params
	 * @param $escape
	 * @return unknown_type
	 */
	function _getCondition($condition, $params, $escape) {
		$dbObj = new DB();

		if (is_null($params)) {
			$params = array();
		}

		if (is_array($params)){
			foreach ($params as $key => $value) {
				if ($escape) {
					$params[$key] = $dbObj->sqlEscapeString($value);
				} else {
					$params[$key] = $value;
				}
			}
		}

		if (!empty($condition) && !is_null($params) ) {
			$value = sprintf('$condition = sprintf("%s", "%s");', $condition, implode('", "', $params));
			eval($value);
		}
		return $condition;
	}

	/**
	 * Ejecuta una consulta simple contra la base de datos y devuelve un array con los valores obtenidos
	 *
	 * @param string/array $fields
	 * @param string $condition sprint_f style
	 * @param array $params
	 * @return unknown
	 */
	function find($fields = ALL, $condition = '', $params = NULL, $returnType = MULTI, $escape = true) {

		$condition = $this->_getCondition($condition, $params, $escape);
		$query = sprintf(
			'SELECT %s FROM %s WHERE %s',
			$fields,
			$this->_table,
			empty($condition) ? '1' : $condition
		);
		return $this->query($query, $returnType, $fields);
	}

	/**
	 *
	 * @param $condition
	 * @param $params
	 * @return unknown_type
	 */
	function count($condition = '', $params = NULL) {
		return $this->find('count(1)', $condition, $params, MONO);
	}

	/**
	 *
	 * @param $key
	 * @param $value
	 * @return unknown_type
	 */
	function _getValueForFind($key, $value) {
		if (!array_key_exists($key, $this->_metaData)) {
			return $value;
		}

		$tmpValue = $this->$key;
		$this->$key = $value;
		$returnValue = $this->get($key);
		$this->$key = $tmpValue;
		return $returnValue;
	}

	/**
	 *
	 * @param $varsToLoad
	 * @return unknown_type
	 */
	function loadFromArray($varsToLoad) {
		if (!is_array($varsToLoad)) return false;
		foreach($varsToLoad as $key => $value) {
			$this->{$key} = $value;
		}
		return true;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function _serialize() {
		reset($this->_metaData);
		while(list($key, ) = each($this->_metaData)) {
			$values[$key] = $this->{$key};
		}
		return $values ? $values : false;
	}

	/**
	 *
	 * @param $values
	 * @return unknown_type
	 */
	function _unserialize($values) {
		reset($this->_metaData);
		while (list($key, ) = each($this->_metaData)) {
			if (isset($values[$key])) {
				$this->$key = $values[$key];
			}
		}
		return !empty($this->{$this->_idField}) ? true : false;
	}

	/**
	 *
	 * @param $method
	 * @param $params
	 * @return unknown_type
	 */
	function call__($method, $params) {
		/**
		 * Overloadable añade un nivel de array a los parámetros
		 */
		return $this->behaviors->$method($this, isset($params[0]) ? $params[0] : NULL);
	}

	/**
	 *
	 * @param $query
	 * @return unknown_type
	 */
	function _logQuery($query) {
		error_log($query);
	}
}
?>
