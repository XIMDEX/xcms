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

if(!defined('XIMDEX_ROOT_PATH')) { 
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__).'/../../'));
}
include_once(XIMDEX_ROOT_PATH.'/inc/utils.inc');


XSession::check();
		
////
//// Inicio del flujo de la acción.
//// 
function GetMappedUrl($nodeID, $imageName)
	{
	$node = new Node($nodeID);
	$config = new Config();
	
	// Cargamos el proyecto

	$projectID = $node->GetProject();
	$node->SetID($projectID);
	
	// Luego la carpeta de imagenes de las plantillas

	$imagesID = $node->GetChildByName($config->GetValue("VisualTemplateDir"));
	$node->SetID($imagesID);

	$imageID = $node->GetChildByName($imageName);

	$df = new DataFactory($imageID);         
	$lastVersion = $df->GetLastVersionId();

	$version = new Version($lastVersion);
	$hash = $version->get('File');

	return $imageID > 0 ? Config::GetValue('UrlRoot') . "/data/files/$hash" : '';
	}

if (isset($_GET["nodeid"]))
	{
	$nodeID = $_GET["nodeid"];
	$fileNode = new Node($nodeID);
	$fileName = $fileNode->GetNodeName();
	$fileContent = $fileNode->class->GetContent();
	$fileContent = explode("##########",$fileContent);
	if(!isset($_GET["container"]))
		{
		$fileContent = $fileContent[0];
		$fileContent = preg_replace("/%%%(.+?)%%%/e", "GetMappedUrl($nodeID,\"$1\")", $fileContent);
		header("Content-type: application/xml");
		echo $fileContent;
		}
	else
		{
		header("Content-type: application/xml");
		echo $fileContent[1];
		}
	}
else	
	{
   	gPrintHeader();
    gPrintBodyBegin();
   	gPrintMsg("ERROR en parametros");
	gPrintBodyEnd();
	}
?>
