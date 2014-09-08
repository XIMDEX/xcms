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




/**
 *	scafold.php
 *	Su responsabilidad es generar las vistas create, list y update para los nodos
 *
 */
 
if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__). '/../../'));
}
 
require_once(XIMDEX_ROOT_PATH . '/inc/cli/CliParser.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/cli/CliReader.class.php');

define ('VIEWS_FOLDER', XIMDEX_ROOT_PATH . '/inc/entities/');

class GenerateEntityCli extends CliParser  {
	var $_metadata = array(
		array (	'name' => '--ormFile',
				'mandatory' => true,
				'message' => 'Tabla a la que se hace referencia',
				'type' => TYPE_STRING),
		array (	'name' => '--className',
				'mandatory' => true,
				'message' => 'Clase que contiene el archivo referenciado',
				'type' => TYPE_STRING)
		);
}

$parameterCollector = new GenerateEntityCli($argc, $argv);
$ormFile = $parameterCollector->getParameter('--ormFile');
$className = $parameterCollector->getParameter('--className');

if (!is_file($ormFile)) {
	XMD_Log::display('Se ha solicitado generar las vistas de un archivo inexistente ' . $ormFile);
	die();
}

require_once $ormFile;
$obj = new $className();
if (is_null($obj->_metaData)) {
	die('fail');
} else {
	var_dump($obj->_metaData);
}

XMD_Log::display("File: " . realpath($ormFile));
XMD_Log::display('Class: ' . $className);
$longFieldTypes = array('V', 'I', 'C', 'M', 'R');
foreach ($obj->_metaData as $key => $fieldDescriptors) {
	XMD_Log::display("\t Field: " . $key);
	$fieldType = CliReader::alert(array('V', 'I', 'C', 'M', 'R', 'v', 'i', 'c', 'm', 'r'), 
		'Seleccione tipo de campo Visible (V), Invisible (I), CreationDate (C), ModificationDate (M), Relation (R):');
	$obj->_metaData[$key]['TYPE'] = $longFieldTypes(strtoupper($fieldType));

	if ($fieldType == 'R') {
		$fieldRelation = CliReader::alert(array('1', '2', '3', '4'), 
			'Seleccione tipo de campo Has_one (1), Has_many (2), Has_and_belongs_to_many (3), Belongs_to (4):');
		switch ($fieldRelation) {
			case 1:
				$table = CliReader::getString('Introduzca la tabla a la que se hace referencia');
				$field = CliReader::getString(sprintf('Introduzca el campo que referencia a %s en la tabla %s', $className, $table));
				$obj->_metaData[$key]['RELATIONS'][] = array('TYPE' => 'HAS_ONE', 'TABLE' => $table, 'FIELD' => $field); 
				break;
			case 2:
				$table = CliReader::getString('Introduzca la tabla a la que se hace referencia');
				$field = CliReader::getString(sprintf('Introduzca el campo que referencia a %s en la tabla %s', $className, $table));
				$obj->_metaData[$key]['RELATIONS'][] = array('TYPE' => 'HAS_MANY', 'TABLE' => $table, 'FIELD' => $field); 
				break;		
			case 3:
				$tableDest = CliReader::getString('Introduzca la tabla que describe la otra parte N');
				$relationTable = CliReader::getString('Introduzca la tabla de relación intermedia');
				$idSource = CliReader::getString(sprintf('Introduzca el campo que referencia a %s en la tabla de relación %s', $className, $relationTable));
				$idDest = CliReader::getString(sprintf('Introduzca el campo que referencia a %s en la tabla de relación %s', $tableDest, $relationTable));
				$obj->_metaData[$key]['RELATIONS'][] = 
					array(	
						'TYPE' => 'HAS_MANY_AND_BELONGS_TO', 
						'RELATION_TABLE' => $relationTable, 
						'TABLE' => $tableDest, 
						'FIELD' => $idSource,
						'RELATION_FIELD' => $idDest); 
				break;
			case 4:
				$table = CliReader::getString('Introduzca la tabla a la que se hace referencia');
				$obj->_metaData[$key]['RELATIONS'][] = array('TYPE' => 'BELONGS_TO', 'TABLE' => $table); 
				break;		
		}
	}
}
?>