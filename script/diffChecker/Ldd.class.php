<?php
/**
 *  ldd: clase de gestión de modificación de estructura
 *  ...
 *
 *  @author Jose Manuel Gomez <josemanuel.gomez@ximetrix.com>
 *  @copyright (c) XimetriX Network Thoughts, {@link http://www.ximetrix.com}
 *  @version $Revision$
 *  @package model
 *
 *  $Id$
 */

if (!defined("XIMDEX_ROOT_PATH")) {
	define ("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__)."/../../../"));
}

require_once(XIMDEX_ROOT_PATH."/inc/db/db.inc");
require_once(XIMDEX_ROOT_PATH."/inc/helper/GenericData.class.php");
require_once(XIMDEX_ROOT_PATH."/inc/patterns/Factory.class.php");
require_once(XIMDEX_ROOT_PATH."/inc/fsutils/FsUtils.class.php");
require_once(XIMDEX_ROOT_PATH."/extensions/adodb/adodb.inc.php");
require_once(XIMDEX_ROOT_PATH."/extensions/adodb/adodb-active-record.inc.php");
require_once(XIMDEX_ROOT_PATH."/inc/patterns/Factory.class.php");
require_once(XIMDEX_ROOT_PATH."/conf/log.conf");
require_once(XIMDEX_ROOT_PATH."/script/diffChecker/UpdateDb_log.class.php");
require_once(XIMDEX_ROOT_PATH."/script/diffChecker/lddConstants.php");

define("GET_FIELD_TYPE_INFO", "/([a-z]+)\s*\(\s*([0-9]*)\s*(\s*,*\s*([0-9]*))?\s*\)/");

class ldd {
	var $tableName = NULL;
	var $object = NULL;
	var $_activeRecord = NULL;
	var $_dbConnection = NULL;
	var $resultMode = NULL;
	var $_noEscapeValues = array("CURRENT_TIMESTAMP");
	var $mode = NULL;
	var $script = "";

	function ldd($fileName, $resultMode) {
		global $DB_TYPE_USAGE;
		if (!isset($DB_TYPE_USAGE) || ($DB_TYPE_USAGE != ADODB)) {
			UpdateDb_log::fatal("Es necesario tener activado ADODB en el tipo de acceso a datos para poder ejecutar el actualizador");
			die("Es necesario tener activado ADODB en el tipo de acceso a datos para poder ejecutar el actualizador");
		}

		UpdateDb_log::debug("Chequeando archivo $fileName");
		if (!is_file($fileName)) {
			UpdateDb_log::error("No se ha podido encontrar el orm correspondiente al archivo " . $fileName);
			return false;
		}

		preg_match("/(.*)\/([^\/]*)_ORM.class.php$/", $fileName, $matches);
		if (count($matches) != 3) {
			return false;
		}
		$path = $matches[1];
		$className = $matches[2];

		$this->tableName = $className;

		$this->mode = $resultMode;

		$factory = new Factory($path, $this->tableName);
		$this->object = $factory->instantiate("_ORM");

		return true;
	}

	function updateFromMetaData() {
		if (!is_object($this->object)) {
			UpdateDb_log::error("El objeto ldd no se ha cargado correctamente, compruebe los errores anteriores");
			return false;
		}

		$data = $this->object->_metaData;
		$uniqueKeys = isset($this->object->_uniqueConstraints) ? $this->object->_uniqueConstraints : array();

		$dbObject = new DB();
		$this->_dbConnection = $dbObject->_getInstance();

		$this->_activeRecord = new ADODB_Active_Record($this->tableName, false, $this->_dbConnection);

		$tableInfo = $this->_activeRecord->TableInfo();
		if (empty($tableInfo)) {
			// Hay que crear la tabla
			$ret = $this->_createTable();
		} else {
			// Hay que actualizar la tabla
			$ret = $this->_updateTable($tableInfo, $data);
		}

		return $ret;
	}

	function _createTable() {
		$queryPart = array();
		reset($this->object->_metaData);
		while (list($fieldName, $fieldInfo) = each($this->object->_metaData)) {
			$result = preg_match(GET_FIELD_TYPE_INFO, $fieldInfo["type"], $matches);
			if (empty($result)) {
				preg_match("/([a-z]+)/", $fieldInfo["type"], $matches);
			}

			if (empty($matches)) {
				UpdateDb_log::error(sprintf("Error al parsear el tipo del campo %s de la tabla %s, saltamos la creación de este campo",
					$fieldName, $this->tableName));
				return ;
			}

			$nodeType = $matches[1];
			if (!strnatcasecmp($nodeType, "enum")) {
				preg_match("/[a-z]+\s*\((.*)\)/", $fieldInfo["type"], $matches);
				$enumValues = $matches[1];
			}

			$nodeMaxLength = isset($matches[2]) ? $matches[2] : NULL;
			$nodeScale = isset($matches[4]) ? $matches[4] : NULL;

			$nodeNullity = $fieldInfo["not_null"] == "true" ? true : false;

			$nodeAutoIncrement = isset($fieldInfo["auto_increment"]) ? $fieldInfo["auto_increment"] : false;

			if (isset($fieldInfo["primary_key"]) && $fieldInfo["primary_key"]) {
				$primaryKey = sprintf("PRIMARY KEY (`%s`)", $fieldName);
			}

			$nodeHasDefault = !is_null($this->object->{$fieldName});
			if ($nodeHasDefault) {
				$nodeDefaultValue = $this->object->{$fieldName};
			}

			if (isset($enumValues)) {
				$queryType = sprintf("%s(%s)", $nodeType, $enumValues);
				unset($enumValues);
			} else {
				if (($nodeMaxLength > 0) && ($nodeScale > 0)) {
					$queryType = sprintf("%s(%s,%s)", $nodeType, $nodeMaxLength, $nodeScale);
				} else if($nodeMaxLength > 0) {
					$queryType = sprintf("%s(%s)", $nodeType, $nodeMaxLength);
				} else {
					$queryType = $nodeType;
				}
			}

			$queryNULL = $nodeNullity ? "NOT NULL" : "NULL";
			$queryAutoincrement = $nodeAutoIncrement ? "AUTO_INCREMENT" : "";

			$queryDefault = "";

			if (isset($nodeDefaultValue)) {
				if (in_array($nodeDefaultValue, $this->_noEscapeValues)) {
					$spfValue = "DEFAULT %s";
				} else {
					$spfValue = "DEFAULT '%s'";
				}
				$queryDefault = sprintf($spfValue, $nodeDefaultValue);
			}

			$queryPart[] = sprintf("`%s` %s %s %s",
				$fieldName, $queryType, $queryNULL,
				$queryAutoincrement, $queryDefault);

		}
		$queryString = implode(",\n", $queryPart);

		$indexes = array();
		if (isset($this->object->_indexes)) {
			reset($this->object->_indexes);
			while (list(, $index) = each($this->object->_indexes)) {
				if (strcmp($index, $this->object->_idField)) {
					$indexes[] = sprintf("KEY (`%s`)", $index);
				}
			}
		}
		$index = implode(", ", $indexes);

		$uniqueKeys = array();
		if (isset($this->object->_uniqueConstraints)) {
			reset($this->object->_uniqueConstraints);
			while (list(, $uniqueKey) = each($this->object->_uniqueConstraints)) {
				foreach ($uniqueKey as $key => $value) {
					$uniqueKey[$key] = sprintf("`%s`", $value);
				}
				$uniqueKeys[] = sprintf("UNIQUE KEY (%s)", implode(", ", $uniqueKey));
			}
		}

		$uniqueKey = implode(", ", $uniqueKeys);

		if (empty($primaryKey)) {
			UpdateDb_log::error("La tabla {$this->tableName} no contiene primaryKey, regenere el orm y pruebe de nuevo");
			return;
		}

		$query = sprintf("CREATE TABLE `%s`(%s %s %s %s)",
			$this->tableName,
			$queryString,
			!empty($index) ? sprintf(", %s", $index) : "",
			!empty($uniqueKey) ? sprintf(", %s", $uniqueKey) : "",
			!empty($primaryKey) ? sprintf(", %s", $primaryKey) : ""
			);
		return $this->_executeQuery($query);

	}

	function _updateTable($tableInfo, $data) {
		$ret = true;

		// Paso 1: Eliminamos las pk y la autonumeric si procede
		reset($tableInfo->flds);
		while (list(, $fieldInfo) = each($tableInfo->flds)) {
			$newFieldInfo = isset($data[$fieldInfo->name]) ? $data[$fieldInfo->name] : array();
			$nodePrimaryKey = isset($newFieldInfo["primary_key"]) ? $newFieldInfo["primary_key"] : false;
			if (($fieldInfo->primary_key != $nodePrimaryKey) && !$nodePrimaryKey) {
				$this->_updateField($fieldInfo->name, $fieldInfo, $newFieldInfo);
				$query = sprintf("ALTER TABLE `%s` DROP PRIMARY KEY", $this->tableName);
				$ret = $ret && $this->_executeQuery($query);
			}
		}

		// Paso 2: Añadimos las nuevas PK y actualizamos el campo si procede
		reset($tableInfo->flds);
		while (list(, $fieldInfo) = each($tableInfo->flds)) {
			if (!array_key_exists($fieldInfo->name, $data)) {
				$ret = $ret && $this->_dropField($fieldInfo->name);
				continue;
			}

			$newFieldInfo = $data[$fieldInfo->name];
			if ($this->_updateField($fieldInfo->name, $fieldInfo, $newFieldInfo)) {
				unset($data[$fieldInfo->name]);
			}
		}

		// Paso 3: Inserción del resto de los documentos
		reset($data);
		while (list($fieldName, $fieldInfo) = each($data)) {
			$ret = $ret && $this->_updateField($fieldName, NULL, $fieldInfo);
		}

		// Paso 4: Actualización de indices
		$indexes = $this->_activeRecord->GetPrimaryKeys($this->_dbConnection, $this->tableName);
		if (isset($this->object->_indexes) && is_array($this->object->_indexes)) {
			reset($this->object->_indexes);
			while (list(, $index) = each($this->object->_indexes)) {
				if (!in_array($index, $indexes) && (strcmp($index, $this->object->_idField))) {
					$query = sprintf("ALTER TABLE `%s` ADD INDEX (`%s`)", $this->tableName, $index);
					$ret = $ret && $$this->_executeQuery($query);
				} else {
					unset($indexes[$index]);
				}
			}
		}
		while (list(, $index) = each($indexes)) {
			if ($index != $this->object->_idField) {
				$query = sprintf("ALTER TABLE `%s` DROP INDEX (`%s`)", $this->tableName, $index);
				$ret = $ret && $this->_executeQuery($query);
			}
		}

		return $ret;
	}

	function _updateField($fieldName, $fieldInfo, $newFieldInfo) {
		$ret = true;
		$result = preg_match(GET_FIELD_TYPE_INFO, $newFieldInfo["type"], $matches);
		if (empty($result)) {
			preg_match("/([a-z]+)/", $newFieldInfo["type"], $matches);
		}

		if (empty($matches)) {
			UpdateDb_log::error(sprintf("Error al parsear el tipo del campo %s de la tabla %s, saltamos la actualización de este campo",
				$fieldName, $this->tableName));
			return ;
		}

		$nodeType = $matches[1];
		if (!strcmp($nodeType, "enum")) {
			preg_match("/[a-z]+\s*\((.*)\)/", $newFieldInfo["type"], $matches) ;
			$enumValues = $matches[1];
		}
		$nodeMaxLength = isset($matches[2]) ? $matches[2] : NULL;
		$nodeScale = isset($matches[4]) ? $matches[4] : NULL;

		$nodeNullity = $newFieldInfo["not_null"] == "true" ? true : false;

		$nodeAutoIncrement = isset($newFieldInfo["auto_increment"]) ? $newFieldInfo["auto_increment"] : false;

		$nodePrimaryKey = isset($newFieldInfo["primary_key"]) ? $newFieldInfo["primary_key"] : false;
		if (!is_null($fieldInfo) && ($fieldInfo->primary_key != $nodePrimaryKey)) {
			if ($nodePrimaryKey) {
				$query = sprintf("ALTER TABLE `%s` ADD PRIMARY KEY (`%s`)", $this->tableName, $fieldInfo->name);
				$ret = $this->_executeQuery($query);
			}
		}

		$nodeHasDefault = !is_null($this->object->{$fieldName});
		if ($nodeHasDefault) {
			$nodeDefaultValue = $this->object->{$fieldName};
		}

		if (!is_null($fieldInfo)) {
			$anyFieldChanged = !is_null($fieldInfo) && (($fieldInfo->type != $nodeType) ||
				($fieldInfo->max_length != $nodeMaxLength) ||
				($fieldInfo->scale != $nodeScale) ||
				($fieldInfo->not_null != $nodeNullity) ||
				($fieldInfo->auto_increment != $nodeAutoIncrement));

			$defaultValueChanged = false;
			if (isset($fieldInfo->has_default) && ($nodeHasDefault != $fieldInfo->has_default)) {
				$defaultValueChanged = true;
			}

			if (($nodeHasDefault && $fieldInfo->has_default) &&
				$nodeDefaultValue != $fieldInfo->default_value) {
				$defaultValueChanged = true;
			}
		}

		if (is_null($fieldInfo) || $anyFieldChanged || $defaultValueChanged) {
			if (isset($enumValues)) {
				$queryType = sprintf("%s(%s)", $nodeType, $enumValues);
				unset($enumValues);
			} else {
				if (($nodeMaxLength > 0) && ($nodeScale > 0)) {
					$queryType = sprintf("%s(%s,%s)", $nodeType, $nodeMaxLength, $nodeScale);
				} else if($nodeMaxLength > 0) {
					$queryType = sprintf("%s(%s)", $nodeType, $nodeMaxLength);
				} else {
					$queryType = $nodeType;
				}
			}
			$queryNULL = $nodeNullity ? "NOT NULL" : "NULL";
			$queryAutoincrement = $nodeAutoIncrement ? "AUTO_INCREMENT" : "";

			$queryDefault = "";

			if (isset($nodeDefaultValue)) {
				if (in_array($nodeDefaultValue, $this->_noEscapeValues)) {
					$spfValue = "DEFAULT %s";
				} else {
					$spfValue = "DEFAULT '%s'";
				}
				$queryDefault = sprintf($spfValue, $nodeDefaultValue);
			}

			if (is_null($fieldInfo)) {
				$query = sprintf("ALTER TABLE `%s` ADD `%s` %s %s %s %s",
					$this->tableName, $fieldName, $queryType,
					$queryNULL, $queryAutoincrement, $queryDefault);
			} else {
				$query = sprintf("ALTER TABLE `%s` CHANGE `%s` `%s` %s %s %s %s",
					$this->tableName, $fieldInfo->name, $fieldInfo->name, $queryType,
					$queryNULL, $queryAutoincrement, $queryDefault);
			}

			$ret = $ret && $this->_executeQuery($query);
			return $ret;
		} else {
			if (!is_null($fieldInfo)) {
				return true;
			}
		}

		return false;
	}

	function _dropField($fieldName) {
		$query = sprintf("ALTER TABLE %s DROP `%s`", $this->tableName, $fieldName);// drop field
		$ret = $this->_executeQuery($query);
		return $ret;
	}

	function _executeQuery($query) {
		UpdateDb_log::debug($query);
		if ($this->mode == SCREEN) {
			echo $query .";\n";
			return true;
		} else if ($this->mode == DB) {
			$db = new DB();
			$result = $db->execute($query);
			if (!$result) {
				UpdateDb_log::error($db->desErr);
			} else {
				$this->script .= $query;
			}
		} else if ($this->mode == REPORT) {
			$this->script .= $query;
			return true;
		}

		return $result;
	}

	// Devuelve el script generado por la clase
	function getScript() {
		return $this->script;
	}
}
?>
