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
	define("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__))."/../../");
}

require_once(XIMDEX_ROOT_PATH."/inc/cli/CliParser.class.php");

class DumpTable extends CliParser  {
	var $_metadata = array(
		array (	"name" => "--table",
				"mandatory" => true,
				"message" => "Tabla que se va a exportar",
				"type" => TYPE_STRING),
		array (	"name" => "--full",
				"mandatory" => false,
				"message" => "Producir una salida completa",
				"type" => TYPE_STRING),
		array (	"name" => "--id",
				"mandatory" => false,
				"message" => "Identificador que se quiere exportar",
				"type" => TYPE_STRING)
	);
}

$parameterCollector = new DumpTable($argc, $argv);
$tableName = $parameterCollector->getParameter("--table");
$full = $parameterCollector->getParameter("--full");
$idField = $parameterCollector->getParameter("--id");


$factory = new \Ximdex\Utils\Factory(XIMDEX_ROOT_PATH . "/inc/model/orm/", $tableName);
$object = $factory->instantiate("_ORM");
if (!is_object($object)) {
	 die("Error, la clase de orm especificada no existe\n");
}

if (!empty($idField)) {
	if (preg_match('/,/', $idField)) {
		$where = $object->_idField . ' in (%s)';
	} else {
		$where = $object->_idField . ' = %s';
	}
	$value = array($idField);
} else {
	$where = '';
	$value = NULL;
}

$result = $object->find($object->_idField, $where, $value, MONO, false);

if (empty($result)) {
	die("Resultado vac�o encontrado\n");
}

	if ($full) {
	echo <<< HEREDOC

<?php
if (!defined('XIMDEX_ROOT_PATH')) {
        define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../../');
}

require_once(XIMDEX_ROOT_PATH . '/script/diffChecker/lmd.class.php');

HEREDOC;
	}
reset($result);
while (list(, $id) = each($result)) {
	$datos["table"] = $tableName;
	$object = $factory->instantiate("_ORM", $id);
	foreach ($object->_metaData as $key => $value) {
		$datos[$key] = $object->get($key);
	}
	$stringdata = array();
	foreach ($datos as $key => $value) {
		$stringdata[] =	"'$key' => '$value'";
	}
	$stringValue = implode(",\n", $stringdata);

	echo <<< HEREDOC
\$data = array(
$stringValue
);

\$lmd = new lmd();
\$lmd->add(\$data);

HEREDOC;
}
if ($full) {
echo "?>\n";
}

?>