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




if (!defined("XIMDEX_ROOT_PATH")) {
	define ("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__)."/../../") );
}
if (!isset($DB_TYPE_USAGE) && defined("ADODB") ) {
	$DB_TYPE_USAGE = ADODB;
}

require_once(XIMDEX_ROOT_PATH."/inc/helper/GenericData.class.php");
require_once(XIMDEX_ROOT_PATH."/inc/helper/Messages.class.php");
require_once(XIMDEX_ROOT_PATH."/inc/patterns/Factory.class.php");
require_once(XIMDEX_ROOT_PATH."/inc/db/db.inc");
require_once(XIMDEX_ROOT_PATH."/script/diffChecker/UpdateDb_log.class.php");
require_once(XIMDEX_ROOT_PATH."/script/diffChecker/UpdateDb_historic.class.php");
require_once(XIMDEX_ROOT_PATH."/script/diffChecker/Ldd.class.php");

class lmd {
	function lmd() {
		if (!defined("LOGGED_SCRIPT_BEGIN")) {
			UpdateDb_log::info(sprintf("*** Ejecución script %s", $_SERVER["PHP_SELF"]));
			UpdateDb_historic::info(sprintf("*** Ejecución script %s", $_SERVER["PHP_SELF"]));
			define("LOGGED_SCRIPT_BEGIN", true);
		}
	}

	function add($data) {
		if (!(isset($data["table"]) && !empty($data["table"]))) {
			UpdateDb_log::error("No se ha especificado el nombre de la tabla");
		}
		$tableName = $data["table"];

		$this->checkModel($tableName);
		$factory = new Factory(XIMDEX_ROOT_PATH . "/inc/model/orm/", $tableName);
		$obj = $factory->instantiate("_ORM");

		$obj->loadFromArray($data);

		$result = $obj->find($obj->_idField, sprintf("%s = %%s", $obj->_idField), array($obj->get($obj->_idField)));
		if (count($result) > 0) {
			UpdateDb_log::info(sprintf("Ya existe una tupla en la tabla %s con el valor %s para el identificador %s, procediendo a actualizar los datos",
				$tableName, $obj->get($obj->_idField), $obj->_idField));
			return $this->update($data);
		}

		if (isset($obj->_uniqueConstraints) && is_array($obj->_uniqueConstraints)) {
			reset($obj->_uniqueConstraints);
			while (list($uniqueName, $uniqueElements) = each($obj->_uniqueConstraints)) {
				reset($uniqueElements);
				while (list(, $uniqueElement) = each($uniqueElements)) {
					$where[] = sprintf("%s = %%s", $uniqueElement);
					$value[] = $obj->get($uniqueElement);
				}
				$result = $obj->find($obj->_idField, implode(" AND ", $where), $value, MONO);
				if (count($result) > 0) {
					UpdateDb_log::error(sprintf("Se ha violado la unicidad de la clave %s, la tupla (" . print_r($data, true) . ")ya existe con el id %s",
						$uniqueName, $result[0]));
					return false;
				}
			}
		}
		if ($obj->modelInError) {
			$this->updateModel($tableName);
		}
		$result = $obj->add();
		reset($obj->messages->messages);
		while (list(, $message) = each($obj->messages->messages)) {
			UpdateDb_log::warning($message["message"]);
		}
		$obj->messages = new Messages();
		if (!$result) {
			$this->updateModel($tableName);
			$result = $obj->add();
		}
		reset($obj->messages->messages);
		while (list(, $message) = each($obj->messages->messages)) {
			UpdateDb_log::warning($message["message"]);
		}
		return $result;
	}

	function update($data) {
		if (!(isset($data["table"]) && !empty($data["table"]))) {
			UpdateDb_log::error("No se ha especificado el nombre de la tabla");
		}
		$tableName = $data["table"];

		$this->checkModel($tableName);
		$factory = new Factory(XIMDEX_ROOT_PATH . "/inc/model/orm/", $tableName);
		$obj = $factory->instantiate("_ORM");

		if (!isset($data[$obj->_idField])) {
			UpdateDb_log::error("No se ha especificado el identificador del nodo"
				 . " que se quiere actualizar");
			return false;
		}
		$obj = $factory->instantiate("_ORM", $data[$obj->_idField]);
		if (!($obj->get($obj->_idField) > 0)) {
			UpdateDb_log::error(sprintf("No se ha podido encontrar la tupla con"
				. " el identificador %s", $data[$obj->_idField]));
			return false;
		}

		if (isset($obj->_uniqueConstraints) && is_array($obj->_uniqueConstraints)) {
			reset($obj->_uniqueConstraints);
			while (list($uniqueName, $uniqueElements) = each($obj->_uniqueConstraints)) {
				$checkElement = false;
				$where = array();
				$value = array();
				reset($uniqueElements);
				while (list(, $uniqueElement) = each($uniqueElements)) {
					if (isset($data[$uniqueElement])) {
						$where[] = sprintf("%s = %%s", $uniqueElement);
						$value[] = $data[$uniqueElement];
						if ($obj->get($uniqueElement) != $data[$uniqueElement]) {
							$checkElement = true;
						}
					}
				}
				if ($checkElement) {
					$result = $obj->find($obj->_idField, implode(" AND ",
						$where), $value, MONO);
					if (count($result) > 0) {
						UpdateDb_log::error(sprintf("Se ha violado la unicidad"
							. " de la clave %s, la tupla ya existe con el id %s",
							$uniqueName, $result[0]));
						return false;
					}
				}
			}
		}
		$obj->loadFromArray($data);
		if ($obj->modelInError) {
			$this->updateMOdel($tableName);
		}
                $result = $obj->update();
                reset($obj->messages->messages);
                while (list(, $message) = each($obj->messages->messages)) {
                        UpdateDb_log::warning($message["message"]);
                }
                $obj->messages = new Messages();
                if (!$result) {
                        $this->updateModel($tableName);
                        $result = $obj->update();
                }
                reset($obj->messages->messages);
                while (list(, $message) = each($obj->messages->messages)) {
                        UpdateDb_log::warning($message["message"]);
                }
		
		return $result;
	}

	/**
	 * Si viene el identificador especificado, eliminamos, sino montamos la consulta
	 *
	 * @param unknown_type $data
	 * @return unknown
	 */
	function delete($data) {
		if (!(isset($data["table"]) && !empty($data["table"]))) {
			UpdateDb_log::error("No se ha especificado el nombre de la tabla");
		}
		$tableName = $data["table"];

		$this->checkModel($tableName);
		$factory = new Factory(XIMDEX_ROOT_PATH . "/inc/model/orm/", $tableName);
		$obj = $factory->instantiate("_ORM");

/*		if (!isset($data[$obj->_idField])) {
			UpdateDb_log::error("No se ha especificado el identificador del nodo"
				. " que se quiere eliminar");
			return false;
		}*/
		if (isset($data[$obj->_idField]) && $data[$obj->_idField] > 0) {
			$obj = $factory->instantiate("_ORM", $data[$obj->_idField]);
			if (!($obj->get($obj->_idField) > 0)) {
				UpdateDb_log::error(sprintf("No se ha podido encontrar la tupla con"
					. " el identificador %s para la tabla %s", $data[$obj->_idField], $obj->_table));
				return false;
			}

			$result = $obj->delete();
			reset($obj->messages->messages);
			while (list(, $message) = each($obj->messages->messages)) {
				UpdateDb_log::warning($message["message"]);
			}
			return $result;
		} else {
			unset($data['table']);
			$keys = array_keys($data);
			$values = array();
			foreach($data as $key => $value) {
				$values[] = $value;
			}
			$conditions = array();
			foreach($keys as $key) {
				$conditions[] = sprintf("%s = %%s", $key);
			}
			$condition = implode(' AND ', $conditions);
			$obj = $factory->instantiate("_ORM", NULL);
			$result = $obj->find($obj->_idField, $condition, $values, MONO);
			foreach($result as $keyToDelete) {
				$obj = $factory->instantiate("_ORM", $keyToDelete);
				$obj->delete();
			}
		}
		
	}

	function query($query) {
		$db = new DB();
		if (preg_match("/(SELECT)/i", $query) > 0) {
			$result = $db->query($query);
		} else {
			$result = $db->execute($query);
		}
		if (!$result) {
			UpdateDb_log::error("Error al lanzar la consulta $query");
		}
	}
	function updateModel($model) {
		UpdateDb_log::error('Modelo ' . $model . ' con errores, intentando reparar con Ldd');
		$fileName = XIMDEX_ROOT_PATH . "/inc/model/orm/" . $model . '_ORM.class.php';
		$ldd = new Ldd($fileName, DB);
		$ldd->updateFromMetaData();
	}
	function checkModel($model) {
                $dbObject = new DB();
                $this->_dbConnection = $dbObject->_getInstance();

                $this->_activeRecord = new ADODB_Active_Record($model, false, $this->_dbConnection);

                $tableInfo = $this->_activeRecord->TableInfo();
                if (empty($tableInfo)) {
			$ormFile = XIMDEX_ROOT_PATH . "/inc/model/orm/" . $model . '_ORM.class.php';
			if (is_file($ormFile)) {
				UpdateDb_log::error('Modelo ' . $model . ' no existe, se intenta crear la tabla');
				$this->updateMOdel($model);
			}
		}
	}
}
?>
