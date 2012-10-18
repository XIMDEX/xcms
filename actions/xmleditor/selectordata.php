<?php 

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *  
 * @desc Interfaz XML. Definicion del contenido de un nodo para los selectores del editor.   *
 *                                                                            *
 ******************************************************************************/

ModulesManager::file('/inc/utils.inc');

XSession::check();

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
                                                     // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");	// HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");										// HTTP/1.0


header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="ISO-8859-1"?>';

$contentType = $_GET['contenttype'];
if(!($contentType == 'all'		||	$contentType == 'images' ||
	 $contentType == 'links'	||	$contentType == 'common' ||
	 $contentType == 'import'	||	$contentType == 'common_import' ))
	{
	$contentType = 'all';
	}
	
$selectedNodeID = $_GET['nodeid'];
$nodeType = new NodeType();
if(!$selectedNodeID)
	{
	$config = new Config();
	$selectedNodeID = $config->GetValue("ProjectsNode");
	}

/// Segun lo que se nos pida, enlaces, imagenes o todo, hacemos dos listas
/// la de los tipos de nodo a mostrar y la de los tipos de nodo seleccionables

if($contentType == 'images')
	{
//	echo "images";
	$typeList = array('Projects', 'Project', 'Server', 'Section', 'NewsSection', 'ImagesFolder', 'ImagesRootFolder', 'ImageFile', 'TemplateImages');
	for($i=0; $i<sizeof($typeList); $i++)
		{
		$nodeType->SetByName($typeList[$i]);
		$typeList[$i] = $nodeType->GetID();
		}

	$selectableList = array('ImageFile');
	for($i=0; $i<sizeof($selectableList); $i++)
		{
		$nodeType->SetByName($selectableList[$i]);
		$selectableList[$i] = $nodeType->GetID();
		}
	}

if($contentType == 'links')
	{
//	echo "links";
	$typeList = array('Projects', 'Project', 'Server', 'Section', 'NewsSection', 'LinkManager', 'LinkFolder', 'XmlRootFolder', 'XmlFolder', 'XmlDocument', 'XmlContainer', 'CommonRootFolder', 'CommonFolder', 'Link', 'TextFile', 'BinaryFile', 'ImageFile');
	for($i=0; $i<sizeof($typeList); $i++)
		{
		$nodeType->SetByName($typeList[$i]);
		$typeList[$i] = $nodeType->GetID();
		}
	
	$selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile');
	for($i=0; $i<sizeof($selectableList); $i++)
		{
		$nodeType->SetByName($selectableList[$i]);
		$selectableList[$i] = $nodeType->GetID();
		}
	}

if($contentType == 'common')
	{
//	echo "links";
	$typeList = array('Projects', 'Project', 'Server', 'Section', 'NewsSection', 'CommonRootFolder', 'CommonFolder', 'TextFile', 'BinaryFile', 'ImageFile');
	for($i=0; $i<sizeof($typeList); $i++)
		{
		$nodeType->SetByName($typeList[$i]);
		$typeList[$i] = $nodeType->GetID();
		}
	
	$selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile');
	for($i=0; $i<sizeof($selectableList); $i++)
		{
		$nodeType->SetByName($selectableList[$i]);
		$selectableList[$i] = $nodeType->GetID();
		}
	}
	
if($contentType == 'import')
	{
//	echo "links";
	$typeList = array('Projects', 'Project', 'Server', 'Section', 'NewsSection', 'ImportRootFolder', 'ImportFolder', 'TextFile', 'BinaryFile', 'ImageFile');
	for($i=0; $i<sizeof($typeList); $i++)
		{
		$nodeType->SetByName($typeList[$i]);
		$typeList[$i] = $nodeType->GetID();
		}
	
	$selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile');
	for($i=0; $i<sizeof($selectableList); $i++)
		{
		$nodeType->SetByName($selectableList[$i]);
		$selectableList[$i] = $nodeType->GetID();
		}
	}
	
if($contentType == 'common_import')
	{
//	echo "links";
	$typeList = array('Projects', 'Project', 'Server', 'Section', 'NewsSection', 'ImportRootFolder', 'ImportFolder', 'CommonRootFolder', 'CommonFolder', 'TextFile', 'BinaryFile', 'ImageFile');
	for($i=0; $i<sizeof($typeList); $i++)
		{
		$nodeType->SetByName($typeList[$i]);
		$typeList[$i] = $nodeType->GetID();
		}
	
	$selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile');
	for($i=0; $i<sizeof($selectableList); $i++)
		{
		$nodeType->SetByName($selectableList[$i]);
		$selectableList[$i] = $nodeType->GetID();
		}
	}
	
if($contentType == 'all')
	{
//	echo "all";
	$typeList = $nodeType->GetAllNodeTypes();
	
	$selectableList = array('XmlDocument', 'Server', 'Link', 'ImageFile', 'TextFile', 'BinaryFile');
	for($i=0; $i<sizeof($selectableList); $i++)
		{
		$nodeType->SetByName($selectableList[$i]);
		$selectableList[$i] = $nodeType->GetID();
		}
	}
//echo sizeof($selectableList)." ".sizeof($typeList);
echo '<tree>';
$selectedNode = new Node($selectedNodeID);
if(!$selectedNode->numErr)
	{
	$children = $selectedNode->GetChildren();
	for($i = 0; $i < sizeof($children); $i++)
		{
		$selectedNode->SetID($children[$i]);
		$curNodeType = $selectedNode->GetNodeType();
		
		//// Si esta en la lista de nodos a mostrar
		if (in_array($curNodeType,$typeList)) 
			{
			$nodeIcon = 'images/icons/'.$selectedNode->nodeType->GetIcon();
			/// Si ademas es seleccionable 
			if (in_array($curNodeType,$selectableList)) 
				echo '<tree text="'.$selectedNode->GetNodeName().'" src="treeselectordata.php?nodeid='.$selectedNode->GetID().'&amp;contenttype='.$contentType.'" action="javascript: parent.setInfo(\''.$selectedNode->GetPath().'\',\''.$selectedNode->GetID().'\')" icon="'.$nodeIcon.'" openIcon="'.$nodeIcon.'"/>';
			else
				echo '<tree text="'.$selectedNode->GetNodeName().'" src="treeselectordata.php?nodeid='.$selectedNode->GetID().'&amp;contenttype='.$contentType.'" icon="'.$nodeIcon.'" openIcon="'.$nodeIcon.'"/>';
			}
		}
	}
echo '</tree>';
	
?>
