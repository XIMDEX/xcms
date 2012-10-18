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
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');

define ('VIEWS_FOLDER', XIMDEX_ROOT_PATH . '/xmd/template/Smarty/views/');

class ScafoldCli extends CliParser  {
	var $_metadata = array(
		array (	'name' => '--entityFile',
				'mandatory' => true,
				'message' => 'Tabla a la que se hace referencia',
				'type' => TYPE_STRING),
		array (	'name' => '--className',
				'mandatory' => true,
				'message' => 'Clase que contiene el archivo referenciado',
				'type' => TYPE_STRING)
		);
}

$parameterCollector = new ScafoldCli($argc, $argv);
$ormFile = $parameterCollector->getParameter('--entityFile');
$className = $parameterCollector->getParameter('--className');

if (!is_file($ormFile)) {
	XMD_Log::display('Se ha solicitado generar las vistas de un archivo inexistente ' . $ormFile);
	die();
}

require_once $ormFile;
$obj = new $className();
if (is_null($obj->_metaData)) {
	die('fail');
}

$parcial = '';

XMD_Log::display("File: " . realpath($ormFile));
XMD_Log::display('Class: ' . $className);

$longFieldTypes = array('K', 'V', 'I', 'C', 'M', 'R');
$classNameForForm = str_replace('_ENTITY', '', $className);

foreach ($obj->_metaDataEntity as $key => $fieldDescriptors) {
	switch ($fieldDescriptors['TYPE']) {
		case 'K':
			$parcial .= sprintf('{input_hidden name="%s_%s" value="`$%s.%s`"}', 
				$classNameForForm, $key, $classNameForForm, $key) . "\n";
			echo "\n";
			break;
		case 'V':
			if (array_key_exists('RELATION', $fieldDescriptors)) {
				switch ($fieldDescriptors['RELATION']) {
					case 'HAS_ONE':
						$parcial .= sprintf('{select name="%s_%s" human_readable_name="%s" values="`$%s_%s`"}',
							$classNameForForm, $key, $fieldDescriptors['SINGULAR_HUMAN_NAME'],
							$classNameForForm, $key) . "\n";
						break;
/*						
					case 'HAS_MANY': // Puede contener un through ??
						$parcial .= sprintf('{ name="%s_%s" human_readable_name="%s" values="`$%s_%s`"}',
							$classNameForForm, $key, $fieldDescriptors['SINGULAR_HUMAN_NAME'],
							$classNameForForm, $key);
						break;
*/
					case 'HAS_AND_BELONGS_TO_MANY':
						$parcial .= sprintf('{checkboxlist name="%s_%s" human_readable_name="%s" values="`$%s_%s`"}',
							$classNameForForm, $key, $fieldDescriptors['SINGULAR_HUMAN_NAME'],
							$classNameForForm, $key) . "\n";
						break;
				}
				break;
			}
			$parcial .= sprintf('{input_text name="%s_%s" human_readable_name="%s" value="`$%s.%s`"}',
				$classNameForForm, $key, $fieldDescriptors['SINGULAR_HUMAN_NAME'], 
				$classNameForForm, $key, $classNameForForm, $key, $classNameForForm, $key) . "\n";
			break;
	}
}

if (!is_dir(VIEWS_FOLDER . $classNameForForm)) {
	FsUtils::mkdir(VIEWS_FOLDER . $classNameForForm);
}
FsUtils::file_put_contents(VIEWS_FOLDER . $classNameForForm . '/' . '_form.tpl', $parcial);

?>
