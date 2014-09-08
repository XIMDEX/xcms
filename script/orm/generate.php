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




// TODO: Eliminar HEREDOC y usar buffer ob o similar...

/**
 * XIMDEX_ROOT_PATH
 */
if (!defined('XIMDEX_ROOT_PATH'))
        define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

include_once (XIMDEX_ROOT_PATH . '/extensions/adodb/adodb.inc.php');
include_once (XIMDEX_ROOT_PATH . '/extensions/adodb/adodb-active-record.inc.php');
include_once (XIMDEX_ROOT_PATH . '/inc/db/DB_orm.class.php');
include_once (XIMDEX_ROOT_PATH . '/conf/install-params.conf.php');
include_once (XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');

if (!($argc >= 2 && is_string($argv[1]))) {
	echo <<< HEREDOC
Sintaxis incorrecta.

La sintaxis correcta es:

Para generar una sola clase:
	$ php script/orm/generate [Nombre de la tabla]

ó para generar todas las clases
	$ php script/orm/generate all

o para generar solo las nuevas
	$ php script/orm/generate new_tables

HEREDOC;
	exit();
}

//$tableName = 'Prueba';

$dbConnection = & DB::_getInstance();
$dbConnection->debug = false;

$dbConnection->Connect($DBHOST, $DBUSER, $DBPASSWD, $DBNAME, true);
if (!$dbConnection->IsConnected()) {
	echo <<< HEREDOC
Se han encontrado problemas para conectarse a la base de datos.

Compruebe los archivos de configuración de la aplicación.

HEREDOC;
	exit();
}

$tableName = $argv[1];
$tableMode = '';
if ($tableName == 'all' || $tableName == 'new_tables') {
	$tableMode = $tableName;
	$query = "SHOW TABLES";
	/* var $rs ADORecordset */
	$rs = $dbConnection->GetAll($query);
	$tables = array();
	reset ($rs);
	while(list(, $table) = each($rs)) {
		$tables[] = $table[0];
	}
} else {
	$tables = array($tableName);
}
reset($tables);
while (list(, $tableName) = each($tables)) {

	$activeRecord = new ADODB_Active_Record($tableName, false, $dbConnection);
	$indexArray = $activeRecord->GetPrimaryKeys($dbConnection, $tableName);
	if (is_array($indexArray)) {
		reset($indexArray);
		while (list($key, $indexElement) = each($indexArray)) {
			$indexArray[$key] = "'$indexElement'";
		}
		$indexes = implode(', ', $indexArray);
	} else {
		$indexes = '';
	}

	$tableInfo = $activeRecord->TableInfo();
	if (!(!is_null($tableInfo) && (count($tableInfo->keys) === 1))) {
		echo <<< HEREDOC
Error al generar la clase {$tableName}_ORM.class.php
Posibles causas:

 - La tabla $tableName no existe en la base de datos $DBNAME.
 - La tabla $tableName no tiene establecida una clave primaria única

HEREDOC;
		continue;
	}
	$fileName = XIMDEX_ROOT_PATH . "/inc/model/orm/{$tableName}_ORM.class.php";
	if (is_file($fileName)) {
		if ($tableMode == 'new_tables') {
			echo <<< HEREDOC
La tabla $tableName ya existe, la ignoramos.

HEREDOC;
			continue;
		}
		echo <<< HEREDOC
Ya existe un modelo para la tabla $tableName, el modelo existente se guardará con extensión bck(número)

HEREDOC;
		$fileContents = FsUtils::file_get_contents($fileName);
		$i = 0;
		while (is_file("$fileName.bck{$i}")) {
			$i++;
		}
		if (FsUtils::file_put_contents("$fileName.bck{$i}", $fileContents)) {
			echo "El archivo $fileName ha sido guardado en $fileName.bck{$i}\n";
		} else {
			echo "No ha sido posible guardar el archivo $fileName en $fileName.bck{$i}\n";
			continue;
		}
	}
	reset($tableInfo->flds);
	$metaDataDescriptors = array();
	$varInitializators = array();
	$primaryKey = '';

	while (list(, $fieldObject) = each($tableInfo->flds)) {

		$primaryKeyString = '';
		if ($fieldObject->primary_key === true) {
			$primaryKey = $fieldObject->name;
			$primaryKeyString = ", 'primary_key' => true";
		}
		if ($fieldObject->max_length != -1) {
			if (isset($fieldObject->enums) && is_array($fieldObject->enums)) {
				$type = sprintf('%s(%s)', $fieldObject->type, implode(', ', $fieldObject->enums));
			} else {
				if (isset($fieldObject->scale)) {
					$type = sprintf("%s(%d, %d)", $fieldObject->type, $fieldObject->max_length, $fieldObject->scale);
				} else {
					$type = sprintf("%s(%d)", $fieldObject->type, $fieldObject->max_length);
				}
			}
		} else {
			$type = sprintf("%s", $fieldObject->type);
		}
		$notNull = $fieldObject->not_null ? 'true' : 'false';

		$extraDescriptors = '';
		if ($fieldObject->auto_increment) {
			$extraDescriptors = ", 'auto_increment' => 'true'";
		}
		$metaDataDescriptors[] = sprintf ("%s'%s' => array('type' => \"%s\", 'not_null' => '%s'%s%s)",
									str_repeat("\t", 4),
									$fieldObject->name,
									$type,
									$notNull,
									$extraDescriptors,
									$primaryKeyString);
		if (isset($fieldObject->has_default) && $fieldObject->has_default) {
			if (is_numeric($fieldObject->default_value)) {
				$varInitializators[] = sprintf("\tvar \$%s = %s;", $fieldObject->name, $fieldObject->default_value);
			} else {
				$varInitializators[] = sprintf("\tvar \$%s = '%s';", $fieldObject->name, $fieldObject->default_value);
			}
		} else {
			$varInitializators[] = sprintf("\tvar \$%s;", $fieldObject->name);
		}
	}
	$metaDataDescriptor = implode(",\n", $metaDataDescriptors);
	$varInitializator = implode("\n", $varInitializators);


	$informationDbConnection = & DB::_getInstance();
	$informationDbConnection->debug = false;

	$informationDbConnection->Connect($DBHOST, $DBUSER, $DBPASSWD, 'INFORMATION_SCHEMA', true);
	if (!$informationDbConnection->IsConnected()) {
		echo <<< HEREDOC
	Se han encontrado problemas para conectarse a la base de datos INFORMATION_SCHEMA.

	Compruebe los archivos de configuración de la aplicación y utilice provisionalmente un usuario con privilegios de acceso a esta tabla.

HEREDOC;
		exit();
	}

	$query = sprintf("SELECT CONSTRAINT_NAME FROM `TABLE_CONSTRAINTS` WHERE TABLE_NAME='%s' AND CONSTRAINT_TYPE = 'UNIQUE'", $tableName);

	$rs = $informationDbConnection->Query($query);
	$uniqueConstraints = array();
	if (!$rs->EOF) {
		$row = $rs->fetchRow();
		do {
			$uniqueConstraints[] = $row['CONSTRAINT_NAME'];
		} while ($row = $rs->fetchRow());
	}

	$constraintInfo = array();
	foreach ($uniqueConstraints as $constraintName) {
		$query = sprintf("SELECT COLUMN_NAME FROM `KEY_COLUMN_USAGE` WHERE `CONSTRAINT_NAME` = '%s' AND TABLE_NAME = '%s' AND TABLE_SCHEMA = '%s'", $constraintName, $tableName, $DBNAME);
		$rs = $informationDbConnection->Query($query);
		if (!$rs->EOF) {
			$row = $rs->fetchRow();
			do {
				$constraintInfo[$constraintName][] = $row['COLUMN_NAME'];

			} while($row = $rs->fetchRow());
		}
	}

	$constraintElement = array();
	foreach ($constraintInfo as $constraintName => $constraintFields) {
		foreach ($constraintFields as $id => $field) {
			$constraintFields[$id] = "'$field'";
		}
		$constraintFields = array_unique($constraintFields);
		$fields = implode(', ', $constraintFields);
		$constraintElement[] = sprintf("\t\t\t\t'%s' => array(%s)", $constraintName, $fields);
	}
	$constraintDescriptor = implode(", ", $constraintElement);

	$template = <<< HEREDOC
<?php
/*
 *
 * WARNING: Do not edit this file by hand, automatically generated.
 *
 *
 */


/**
 * XIMDEX_ROOT_PATH
 */
if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));

include_once (XIMDEX_ROOT_PATH . '/inc/helper/GenericData.class.php');

class {$tableName}_ORM extends GenericData   {
	var \$_idField = '$primaryKey';
	var \$_table = '$tableName';
	var \$_metaData = array(
$metaDataDescriptor
				);
	var \$_uniqueConstraints = array(
$constraintDescriptor
				);
	var \$_indexes = array($indexes);
$varInitializator
}
?>

HEREDOC;

if (FsUtils::file_put_contents($fileName, $template)) {
	echo <<< HEREDOC
La clase $fileName ha sido creada con éxito

HEREDOC;
} else {
	echo <<< HEREDOC
No ha sido posible crear la clase $fileName

HEREDOC;
	}
$dbConnection = & DB::_getInstance();
$dbConnection->debug = false;

$dbConnection->Connect($DBHOST, $DBUSER, $DBPASSWD, $DBNAME, true);
if (!$dbConnection->IsConnected()) {
	echo <<< HEREDOC
Se han encontrado problemas para conectarse a la base de datos.

Compruebe los archivos de configuración de la aplicación.

HEREDOC;
	exit();
}
}
?>