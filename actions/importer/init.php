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



	define ('OPERATION_ADD', 'add');
	define ('OPERATION_UPDATE', 'update');
	define ('OPERATION_DELETE', 'delete');
	
	$validOperations = array(OPERATION_ADD, OPERATION_UPDATE, OPERATION_DELETE);

   ModulesManager::file('/inc/helper/Messages.class.php');
   ModulesManager::file('/inc/helper/Utils.class.php');
   ModulesManager::file('/inc/fsutils/FsUtils.class.php');
   ModulesManager::file('/inc/fsutils/ZipArchiver.class.php');
   ModulesManager::file('/inc/io/BaseIO.class.php');
   ModulesManager::file('/actions/importer/actionIO.php');
   ModulesManager::file('/inc/auth/Authenticator.class.php');
	
	$whiteList = array('paquete', 'passApp', 'op');
	
	reset($_GET);
	while (list(, $key) = each($whiteList)) {
		if (isset($_GET[$key])) {
			$$key = $_GET[$key];
		}
	}
	$paquete = sprintf('%s/data/tmp/%s', XIMDEX_ROOT_PATH, $paquete);
	$messages = new Messages();
	
	if (!isset($op) || !in_array($op, $validOperations)) {
		$messages->add(_('Operation way has not been selected'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, '');
	}
	
	do {
		$destFolder = sprintf('/tmp/%s', Utils::generateRandomChars(12, true, true, true));
	} while(is_dir($destFolder));
	
	if (!mkdir($destFolder)) {
		$messages->add(_('Folder to unzip data could not be created'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, $destFolder);
	}
	
	if (!unzipFile($paquete, $destFolder)) {
		$messages->add(_('Package with file which you want to import has not been specified or it does not exist'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, $destFolder);
	}
	
	//Checking file is complete
	$xmlFile = $destFolder . '/ximio.xml';
	if (!is_file($xmlFile)) {
		$messages->add(_('Package to import has not been found into zip file'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, $destFolder);
	}
	
	$identificationFile = $destFolder . '/identificacion.xml';
	if (!is_file($identificationFile)) {
		$messages->add(_('Identification of package has not been found into zip file'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, $destFolder);
	}
	
	// Checking credentials
	$errors = array();
	$identificationDocument = domxml_open_file($identificationFile);
	if (!$identificationDocument) {
		$messages->add(_('Error while parsing crendential file'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, $destFolder);
	}
	
	$identifications = $identificationDocument->get_elements_by_tagname('identificacion');
	if (count($identifications) !== 1) {
		$messages->add(_('Incorrect format has been detected on credential file'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, $destFolder);
	}
	
	$identification = $identifications[0];
	$user = $identification->get_attribute('login');
	
    $authenticator =& new Authenticator();
	if (!$authenticator->login($user, $passApp)) {
		$messages->add(_('Inserted User / password are not correct'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, $destFolder);
	}
	
	// Reading xml
	$domDocument = domxml_open_file($xmlFile);
	if (!$domDocument) {
		$messages->add(_('Xml of package has not a correct format'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, $destFolder);
	}
	
	// Making up package
	$nodeTypeName = 'binaryfile';
	$elements = $domDocument->get_elements_by_tagname($nodeTypeName);
	
	if (count($elements) !== 1) {
		$messages->add(_('El paquete contiene más de un nodo'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, $destFolder);
	}
	$element = $elements[0];
	if ($element->has_attribute('name')) {
		$name = $element->get_attribute('name');
	}
	
	if ($element->has_attribute('parentid')) {
		$idParent = $element->get_attribute('parentid');
	}
	
	if ($element->has_attribute('idnode')) {
		$idNode = $element->get_attribute('idnode');
	}
	
	if (($op != OPERATION_ADD) && !isset($idNode)) {
		$messages->add(_('Node which you want to operate has not been specified'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, $destFolder);
	}

	$childrens = $element->child_nodes();
	if (!empty($childrens) && is_array($childrens)) {
		reset($childrens);
		while (list($key, $children) = each($childrens)) {
			if (strtolower(get_class($children)) != 'domelement') {
				unset($childrens[$key]);
			}
		}
	}
	
	if ((count($childrens) !== 1) && ($op == OPERATION_ADD)) {
		$messages->add(_('Package has no references about path of file to import'), MSG_TYPE_ERROR);
		cleanup($messages, $paquete, $destFolder);
	}
	
	
	if (!empty($childrens) && is_array($childrens)) {
		reset($childrens);
		list(, $children) = each($childrens); // better
		
		$src = sprintf('%s/%s', $destFolder, $children->get_attribute('src'));
	}
	
	$baseIO = new BaseIO();
	
	switch ($op) {
		case OPERATION_ADD:
			$data = array (
					"NODETYPENAME" => "BINARYFILE",
					"NAME" => $name,
					"PARENTID" => $idParent,
					"STATE" => "Publicación",
					"CHILDRENS" => 
					array (
						array (
							"NODETYPENAME" => "PATH",
							"SRC" => $src
							)
						)
					);
			$result = $baseIO->build($data);
			break;
		case OPERATION_UPDATE:
			//publish when update is done?
			$data = array (
					'ID' => $idNode,
					"NODETYPENAME" => "BINARYFILE",
					"NAME" => $name,
					"STATE" => "Publicación",
					"CHILDRENS" => 
					array (
						array (
							"NODETYPENAME" => "PATH",
							"SRC" => $src
							)
						)
					);
			$result = $baseIO->update($data);
			break;
		case OPERATION_DELETE:
			$data = array (
					'ID' => $idNode,
					"NODETYPENAME" => "BINARYFILE"
					);
			$result = $baseIO->delete($data);
			break;
			
	}

	//Launching baseIO
	
	
	if ($result > 0) {
		$messages->add(_('Operation has been successfully performed'), MSG_TYPE_NOTICE);
	} else {
		$messages->add(_('Node could not be imported'), MSG_TYPE_ERROR);
	}
	
	cleanup($messages, $paquete, $destFolder, $result);
	//Return result
	
?>
