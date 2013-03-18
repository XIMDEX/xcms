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
 * @desc Interfaz XML. Definicion del contenido de un nodo para los selectores.  *
 *                                                                            *
 ******************************************************************************/

if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../../"));

include_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');

ModulesManager::file('/inc/utils.inc');

/*
	 TODO Toda la parte de arriba hay que refactorizarla, hay dos posibles refactorizaciones
	 primero:(optimiza en velocidad) sustituir los nombres por idNodeTypes (ahorrará muchas consultas de selección)
	 segundo:(optimiza en mantenibilidad) estimar los arrays en funcion de la tabla nodeallowedcontents
*/
?>
<?php


//TODO ESTA LINEA HAY QUE DECOMENTARLA
XSession::check();

$userID = XSession::get('userID');

//error_reporting(1); WTF!! Establecía el error reporting en este script a E_ERROR

$contentType = isset($_GET['contenttype']) ? $_GET['contenttype'] : NULL;
$selectedNodeID = isset($_GET['nodeid']) ? $_GET['nodeid'] : NULL;
$targetNodeID = isset($_GET['targetid']) ? $_GET['targetid'] : NULL;
$filterType = isset($_GET['filtertype']) ? $_GET['filtertype'] : NULL;
$idNodeType = isset($_GET['nodetype']) ? $_GET['nodetype'] : NULL;

$desde = isset($_GET['desde']) ? $_GET['desde'] : NULL;
$hasta = isset($_GET['hasta']) ? $_GET['hasta'] : NULL;



if(!$contentType && !$idNodeType) {
	$contentType = 'all';
}

if ((!$filterType)&&($contentType=='dynamic')) {
	$contentType = 'all';
}

if($contentType) {
		/// Tenemos varios sets de tipos de nodo prefabricados, segun que arbol queramos mostrar
		/// Los configuramos diciendo qué tipos de nodo solo se muestran y qué tipos de nodo, además, pueden ser seleccionados.
		$prefabricados = array('all', 'images', 'xmldocs','links', 'common', 'import', 'common_import', 'ximlet', 'ximletContainer', 'dynamic', 'pvds', 'ximnewsnewlanguage');
		if(!in_array($contentType, $prefabricados) ) {
			$contentType = 'all';
		}

		/// Si no se nos solicita un nodo en concreto, buscamos la raiz de los proyectos
		$nodeType = new NodeType();
		if(!$selectedNodeID)
			{
			$config = new Config();
			$selectedNodeID = $config->GetValue("ProjectsNode");
			}

		/// Segun lo que se nos pida, enlaces, imagenes o todo, hacemos dos listas
		/// la de los tipos de nodo a mostrar y la de los tipos de nodo seleccionables

		if($contentType == 'dynamic')
			{

			$filterTypeAdd="";

			$targetNode=new Node($targetNodeID);

			$targetNodeType=new NodeType($targetNode->GetNodeType());

			//Esto es para incluir las subcarpetas, porque son de distinto tipo a los padres
			if ($filterType=='CommonRootFolder') {
				$filterTypeAdd='CommonFolder';
			}
			elseif ($filterType=='CommonFolder') {
				$filterTypeAdd='CommonRootFolder';
			}
			elseif ($filterType=='CssRootFolder') {
				$filterTypeAdd='CssFolder';
			}
			elseif ($filterType=='CssFolder') {
				$filterTypeAdd='CssRootFolder';
			}
			elseif ($filterType=='LinkManager') {
				$filterTypeAdd='LinkFolder';
			}
			elseif ($filterType=='LinkFolder') {
				$filterTypeAdd='LinkManager';
			}
			elseif ($filterType=='ImportFolder') {
				$filterTypeAdd='ImportRootFolder';
			}
			elseif ($filterType=='ImportRootFolder') {
				$filterTypeAdd='ImportFolder';
			}
			elseif ($filterType=='ImagesFolder') {
				$filterTypeAdd='ImagesRootFolder';
			}
			elseif ($filterType=='ImagesRootFolder') {
				$filterTypeAdd='ImagesFolder';
			}
			elseif ($filterType=='ximPORTA') {
				$filterTypeAdd='LinkManager';
			}
			elseif (($filterType=='Server')&&($targetNodeType->GetName()=='Section')) {
				$filterTypeAdd='Section';
			}
	 		elseif ($filterType=='Section') {
	 			$filterTypeAdd='Server';
	 		}


			$typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection',$filterType);

			if ($filterTypeAdd!="") {

				$typeList[]=$filterTypeAdd;
				if ($filterType=='ximPORTA') {
					$typeList[]='LinkFolder';
				}

			}

			$typeList = nameToId($typeList);

			$selectableList = array($filterType);
			if ($filterTypeAdd!="") {

				$selectableList[]=$filterTypeAdd;
				if ($filterType=='ximPORTA') {
					$selectableList[]='LinkFolder';
				}


			}
			$selectableList = nameToId($selectableList);
		}else {
			getTypeAndSelectableList($contentType, $typeList, $selectableList);
		}

	} else {
		/*
		 SE HA INTRODUCIDO UN NODETYPE
		 Estimamos el array typeList y el array selectableList a partir de la tabla nodeAllowedcontents
		*/
		$nodeType = new NodeType($idNodeType);
		if ($nodeType->get('IdNodeType') > 0) {
			$firstNode = array($nodeType->get('IdNodeType'));
			// El primer nivel va a ser la variable $selectableList, el resto va a ser $typeList
			$idTypeList = $idSelectableList = $tmpArray = getContainers($firstNode);
			do {
				if (empty($tmpArray)) {
					continue;
				}
				$tmpArray = getContainers($tmpArray);
				reset($tmpArray);
				while (list($key, $idNodeTypeKey) = each($tmpArray)) {
					if (in_array($idNodeTypeKey, $idTypeList)) {
						unset($tmpArray[$key]);
					}
				}
				$idTypeList = array_unique(array_merge($idTypeList, $tmpArray));
			} while (!empty($tmpArray));
			$typeList = ($idTypeList);
			$selectableList = ($idSelectableList);
		} else {
			$typeList = NULL;
			$selectableList = NULL;
		}
	}

	$targetNode = new Node($targetNodeID);
	$pathList = $targetNode->TraverseToRoot();
	//array_pop($pathList);
	// Quizás habría que pasar esta función a la clase nodetype
	function getContainers($idNodeTypeArray) {
		//Condición de ejecución de la función
		if(!is_array($idNodeTypeArray)) {
			return array();
		}
		reset($idNodeTypeArray);
		$returnObject = array();
		while (list (, $idNodeType) = each($idNodeTypeArray)) {
			$dbObj = new DB();
			$query = sprintf("SELECT IdNodeType FROM NodeAllowedContents WHERE NodeType = %d", $idNodeType);
			$dbObj->Query($query);

			while(!$dbObj->EOF) {
				$returnObject[] = $dbObj->GetValue('IdNodeType');
				$dbObj->Next();
			}
			unset($dbObj);
		}
		return $returnObject;
	}
/// Comenzamos la escritura del interfaz xml
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                                                     // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");	// HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");										// HTTP/1.0

header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="ISO-8859-1"?>';


echo '<tree>';
PrintContent($selectedNodeID, $contentType, $pathList, $targetNodeID, $filterType,$selectableList, $typeList,$userID, $idNodeType);
echo '</tree>';

function PrintContent($nodeID, $contentType=null, $pathList=null, $targetNodeID=null, $filterType=null,$selectableList=null, $typeList=null, $userID, $idNodeType)
	{

	//*******************************************************
	// Comienzo de parte nueva añadida por Jose Luis el 11.08.2004 para solo dejar ver los nodos asociados a tu grupo
	//*******************************************************

	/* Verifica variables globales
	 * emartos 03/05/2007
	*/
//	global $desde;
//	global $hasta;
//	global $nelementos;
	$desde = isset ($_GET['desde']) ? $_GET['desde'] : null;
	$hasta = isset ($_GET['hasta']) ? $_GET['hasta'] : null;
	$nelementos = isset ($_GET['nelementos']) ? $_GET['nelementos'] : null;

	$user = new User();
	$user->SetID($userID);
	$group = new Group();
	$node = new Node();

	$nodeList = getNodelist($userID, $nodeID);


	$selectedNode = new Node($nodeID);
	if(!$selectedNode->numErr) {
		$children = $selectedNode->GetChildren();
		$childNode = null; // Declaración de la variable

		if ($children) {
			$i=0;
			foreach($children as $childID) {
				//Esta condición también ha sido añadida el 11.08.2004
				if ($user->HasPermission("view all nodes") or (is_array($nodeList) && in_array($childID,$nodeList)) or $user->IsOnNode($childID, true)) {
					$selectedNode->SetID($childID);
					$listaDatos=$selectedNode->DatosNodo();

					$curNodeType = $listaDatos['NodeType'];
					if ((!$typeList || in_array($curNodeType,$typeList))) {
						$childNode[]	= $childID ;
						$nodeName[]		= $listaDatos['NodeName'];
						$systemType[]	= 1000-$selectedNode->nodeType->get('System');
						$nodeIcon[]		= $selectedNode->nodeType->GetIcon();
			//			$nodePath[]		= $selectedNode->GetPath();
						$nodeTypeList[]	= $curNodeType;
						$nodeState[]	= $listaDatos['State'];
		//				$nodeChildCount[]= sizeof($selectedNode->GetChildren());
						$nodeChannels[]		= $selectedNode->GetChannels();
					}
				}
			}
	}



			if (is_array($childNode))
				{
						//				array_multisort($systemType, $nodeName, $childNode, $nodePath, $nodeIcon, $nodeChildCount, $nodeTypeList);
						array_multisort($systemType, $nodeName, $childNode, $nodeIcon, $nodeTypeList, $nodeState);

						if (($desde!=null)&&($hasta!=null))	{
							$childNode=array_slice($childNode,$desde,$hasta-$desde+1);
							$nodeName=array_slice($nodeName,$desde,$hasta-$desde+1);
							$systemType=array_slice($systemType,$desde,$hasta-$desde+1);
							$nodeIcon=array_slice($nodeIcon,$desde,$hasta-$desde+1);
							$nodeState=array_slice($nodeState,$desde,$hasta-$desde+1);
							$nodeTypeList=array_slice($nodeTypeList,$desde,$hasta-$desde+1);

						}

						$l=sizeof($childNode);
						$numArchivos=0;



				if (($l > $nelementos)&&($nelementos!=0))
					{



					$partes = floor($l/$nelementos);


					if ($l%$nelementos != 0) $partes = $partes + 1;


					if ($desde==null) {
						$desde_aux=$numArchivos;
					}
					else {
						$desde_aux=$desde;
					}

					for ($k = 1; $k <= $partes; $k++) {



							$texto = "";

							$nodoDesde = $childNode[$numArchivos];
							$textoDesde=$nodeName[$numArchivos];



							$expr = $numArchivos + $nelementos - 1;


							if ($l > $expr){
									$nodoHasta = $childNode[$expr];
									$textoHasta = $nodeName[$expr];
									$hasta_aux=$desde_aux+$nelementos-1;
									}
							else{
									$nodoHasta = $childNode[$l-1];
									$textoHasta = $nodeName[$l-1];
									$hasta_aux=$l-1;
							}


							$nodeCarpetaPagDesde=new Node($nodoDesde);
							$nodeCarpetaPagHasta=new Node($nodoHasta);

							$i=$numArchivos;


							$encontrado=false;

							while (($i<$hasta_aux)&&(!$encontrado)) {

								if($pathList && in_array($childNode[$i], $pathList)) {
									$open="yes";
									$encontrado=true;
								}
								else {
									$open="no";
								}

								$i++;
							}

							if($childNode[$i] == $targetNodeID)
								$selected="yes";
							else
								$selected="no";

							if (!$selectableList  || in_array($nodeTypeList[$i], $selectableList))
								{
								$channels = $nodeChannels[$i];
								$channelIdList = '';
								$channelNameList = '';
								foreach($channels as $channelID)
									{
									$chan = new Channel($channelID);

									if($channelIdList)
										{
										$channelNameList .= ',';
										$channelIdList .= ',';
										}
									$channelNameList .= "'".$chan->GetName().".".$chan->GetExtension()."'";
									$channelIdList .= "'".$channelID."'";
									}

								echo '<tree text="'.$textoDesde.' -> '.$textoHasta.'" padre="'.$selectedNode->GetParent().'" nodoDesde="'.$nodoDesde.'" nodoHasta="'.$nodoHasta.'" src="treeselectordata.php?nodeid='.$selectedNode->GetParent().'&amp;contenttype='.$contentType.'&amp;targetid='.$targetNodeID.'&amp;filtertype='.$filterType.'&amp;desde='.$desde_aux.'&amp;hasta='.$hasta_aux.'" contenidotipo="'.$contentType.'&amp;nodetype='.$idNodeType.'"  icon="../../xmd/images/icons/folder_a-z.png" openIcon="../../xmd/images/icons/folder_a-z.png" nodeid="'.$childNode[$i].'" state="'.$nodeState[$i].'" children="2" open="'.$open.'" selected="'.$selected.'"  tipofiltro="'.$filterType.'" targetid="'.$targetNodeID.'"/>';
								}
							else {
								echo '<tree text="'.$textoDesde.' -> '.$textoHasta.'" padre="'.$selectedNode->GetParent().'" nodoDesde="'.$nodoDesde.'" nodoHasta="'.$nodoHasta.'" src="treeselectordata.php?nodeid='.$selectedNode->GetParent().'&amp;contenttype='.$contentType.'&amp;targetid='.$targetNodeID.'&amp;filtertype='.$filterType.'&amp;desde='.$desde_aux.'&amp;hasta='.$hasta_aux.'" contenidotipo="'.$contentType.'&amp;nodetype='.$idNodeType.'" icon="../../xmd/images/icons/folder_a-z.png" openIcon="images/icons/folder_a-z.png" nodeid="'.$childNode[$i].'" state="'.$nodeState[$i].'" children="2" open="'.$open.'" selected="'.$selected.'"  tipofiltro="'.$filterType.'" targetid="'.$targetNodeID.'"/>';
//mivar = '							  <tree text="'+textoDesde +' -> '+textoHasta+ '" selected="' + sel + '" open="'+ abierto +'" nodoDesde="' + nodoDesde + '" nodoHasta="' + nodoHasta + '" src="treeselectordata.php?contenttype=' + contenido + '&#38;targetid=' + target + '&#38;nodeid=' + NodoPadre + '&#38;desde=' + nodoDesde + '&#38;hasta=' + nodoHasta + '&#38;filtertype=' + tipofiltro + '" action="javascript: SetSelectedNode(0);" nodeid="0" icon="images/icons/folder_a-z.png" openIcon="images/icons/folder_a-z.png" state="" children="5" />';
								}


										$numArchivos = $numArchivos + $nelementos;
										$desde_aux=$desde_aux+$nelementos;

						}

					}

				else {

				//***********************************************************************************************************
					foreach($childNode as $childID) {
						$selectedNode->SetID($childID);
						$nodePath[]		= $selectedNode->GetPath();
						$nodeIsFolder[]		= $selectedNode->nodeType->isFolder() ? '1' : '0';
						$nodeChildCount[]= sizeof($selectedNode->GetChildren());
						$nodeIcon[]		= $selectedNode->nodeType->GetIcon();
					}


					if (($desde!=null) && ($hasta!=null)) {


							for($i=0; $i < $hasta-$desde+1; $i++)
								{

								if($pathList && in_array($childNode[$i], $pathList))
									$open="yes";
								else
									$open="no";

								if($childNode[$i] == $targetNodeID)
									$selected="yes";
								else
									$selected="no";

								if (!$selectableList  || in_array($nodeTypeList[$i], $selectableList))
									{


									$channels = $nodeChannels[$i];
									$channelIdList = '';
									$channelNameList = '';
									foreach($channels as $channelID)
										{
										$chan = new Channel($channelID);

										if($channelIdList)
											{
											$channelNameList .= ',';
											$channelIdList .= ',';
											}
										$channelNameList .= "'".$chan->GetName().".".$chan->GetExtension()."'";
										$channelIdList .= "'".$channelID."'";
										}

									echo '<tree text="'.$nodeName[$i].'" padre="'.$selectedNode->GetParent().'" src="treeselectordata.php?nodeid='.$childNode[$i].'&amp;contenttype='.$contentType.'&amp;targetid='.$targetNodeID.'&amp;filtertype='.$filterType.'&amp;nodetype='.$idNodeType.'" contenidotipo="'.$contentType.'" isdir="'.$nodeIsFolder[$i].'" path="'.$nodePath[$i].'" action="javascript: parent.setInfo(\''.$nodePath[$i].'\',\''.$childNode[$i].'\', new Array('.$channelIdList.'), new Array('.$channelNameList.'))" icon="'.$nodeIcon[$i].'" openIcon="'.$nodeIcon[$i].'" nodeid="'.$childNode[$i].'" state="'.$nodeState[$i].'" children="'.$nodeChildCount[$i].'" open="'.$open.'" selected="'.$selected.'"  tipofiltro="'.$filterType.'" targetid="'.$targetNodeID.'"/>';
									}
								else {



									echo '<tree text="'.$nodeName[$i].'" padre="'.$selectedNode->GetParent().'" src="treeselectordata.php?nodeid='.$childNode[$i].'&amp;contenttype='.$contentType.'&amp;targetid='.$targetNodeID.'&amp;filtertype='.$filterType.'&amp;nodetype='.$idNodeType.'" contenidotipo="'.$contentType.'" isdir="'.$nodeIsFolder[$i].'" path="'.$nodePath[$i].'" icon="'.$nodeIcon[$i].'" openIcon="'.$nodeIcon[$i].'" nodeid="'.$childNode[$i].'" state="'.$nodeState[$i].'" children="'.$nodeChildCount[$i].'" open="'.$open.'" selected="'.$selected.'"  tipofiltro="'.$filterType.'" targetid="'.$targetNodeID.'"/>';
								}
								//if($open=="yes")
								//	PrintContent($childNode[$i], $contentType, $pathList, $selectableList, $typeList);
								//echo '</tree>';


								}


					}
						else {
							for($i = 0; $i < sizeof($childNode); $i++)
								{
								if($pathList && in_array($childNode[$i], $pathList))
									$open="yes";
								else
									$open="no";

								if($childNode[$i] == $targetNodeID)
									$selected="yes";
								else
									$selected="no";

								if (!$selectableList  || in_array($nodeTypeList[$i], $selectableList))
									{
									$channels = $nodeChannels[$i];
									$channelIdList = '';
									$channelNameList = '';
									foreach($channels as $channelID)
										{
										$chan = new Channel($channelID);

										if($channelIdList)
											{
											$channelNameList .= ',';
											$channelIdList .= ',';
											}
										$channelNameList .= "'".$chan->GetName().".".$chan->GetExtension()."'";
										$channelIdList .= "'".$channelID."'";
										}

									echo '<tree text="'.$nodeName[$i].'" padre="'.$selectedNode->GetParent().'" src="treeselectordata.php?nodeid='.$childNode[$i].'&amp;contenttype='.$contentType.'&amp;targetid='.$targetNodeID.'&amp;filtertype='.$filterType.'&amp;nodetype='.$idNodeType.'" contenidotipo="'.$contentType.'" isdir="'.$nodeIsFolder[$i].'" path="'.$nodePath[$i].'" action="javascript: parent.setInfo(\''.$nodePath[$i].'\',\''.$childNode[$i].'\', new Array('.$channelIdList.'), new Array('.$channelNameList.'))" icon="'.$nodeIcon[$i].'" openIcon="'.$nodeIcon[$i].'" nodeid="'.$childNode[$i].'" state="'.$nodeState[$i].'" children="'.$nodeChildCount[$i].'" open="'.$open.'" selected="'.$selected.'"  tipofiltro="'.$filterType.'" targetid="'.$targetNodeID.'"/>';
									}
								else{
									echo '<tree text="'.$nodeName[$i].'" padre="'.$selectedNode->GetParent().'" src="treeselectordata.php?nodeid='.$childNode[$i].'&amp;contenttype='.$contentType.'&amp;targetid='.$targetNodeID.'&amp;filtertype='.$filterType.'&amp;nodetype='.$idNodeType.'" contenidotipo="'.$contentType.'" isdir="'.$nodeIsFolder[$i].'" path="'.$nodePath[$i].'" icon="'.$nodeIcon[$i].'" openIcon="'.$nodeIcon[$i].'" nodeid="'.$childNode[$i].'" state="'.$nodeState[$i].'" children="'.$nodeChildCount[$i].'" open="'.$open.'" selected="'.$selected.'" tipofiltro="'.$filterType.'" targetid="'.$targetNodeID.'"/>';

}
								//if($open=="yes")
								//	PrintContent($childNode[$i], $contentType, $pathList, $selectableList, $typeList);
								//echo '</tree>';


								}

							}




					}
					}

		}

	}

/** ************************************************************************************ **/
/** ************************************* HELPERS ************************************** **/
/** ************************************************************************************ **/


//getNodeList from $userId and $nodeID selected
function getNodelist($userID, $nodeID) {
	$user = new User();
	$user->SetID($userID);
	$group = new Group();


	if (!XSession::get("nodelist") or $nodeID==1)
	{
		  $groupList= $user->GetGroupList();
	  	  $groupList = array_diff($groupList,array($group->GetGeneralGroup())); // quitamos general group

		  // Vamos a poner en listaNodos todos los nodos representables
		  $nodeList = null;
		  if ($groupList) {
		  		$nodeList = array();
		  		foreach ($groupList as $groupID) {
		   	   	$group->SetID($groupID);
		   	   	$nodeList = array_merge($nodeList, $group->GetNodeList());
		  		}
		  }

		  if (is_array($nodeList)) {
			  	$nodeList = array_unique($nodeList);
		  }

		//vamos añadiendo los padres de los nodos actuales
		$nodeList = getParentList($nodeList);

		//$session->set("nodelist", $nodeList);
		XSession::set("nodelist", $nodeList);

	}else {
		$nodeList = XSession::get("nodelist");
	}

	return $nodeList;
}

//Get all parent of nodeList
function getParentList($nodeList) {
	if($nodeList) {
		$node = new Node();
		foreach($nodeList as $nodeID) {

			$node -> SetID($nodeID);
			$padre = $node->GetParent();
			while ($padre) {

				if(!in_array($padre, $nodeList)) {
					$nodeList = array_merge($nodeList,  (array)$padre);
				}

				$node ->SetID($padre);
				$padre = $node->GetParent();
			}

		}
		return $nodeList;
	}else {
		return null;
	}
}

/**
 *
 * Sustituye a este bucle
	for($i=0; $i<sizeof($typeList); $i++) {
		$nodeType->SetByName($typeList[$i]);
		$typeList[$i] = $nodeType->GetID();
	}
 * @param array $arrayNames
 * @return array
 */

function nameToId($arrayNames){
	$returnArray = array();
	if(!is_array($arrayNames)) {
		return $returnArray;
	}
	reset($arrayNames);
	while(list(, $name) = each($arrayNames)) {
		$nodeType = new NodeType();
		$nodeType->SetByName($name);
		if ($nodeType->get('IdNodeType') > 0) {
			$returnArray[] = $nodeType->get('IdNodeType');
		}
	}
	return $returnArray;
}



/// Segun lo que se nos pida, enlaces, imagenes o todo, hacemos dos listas
/// la de los tipos de nodo a mostrar y la de los tipos de nodo seleccionables
function getTypeAndSelectableList($contentType, &$typeList, &$selectableList) {

	switch($contentType) {
		case 'image': /* ############################## IMAGE ############################################ */
			if (ModulesManager::isEnabled('ximNEWS')) {
            $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection','XimNewsImages','XimNewsImagesFolder','XimNewsImageFile', 'ImagesFolder', 'ImagesRootFolder', 'ImageFile', 'XimNewsColector', 'TemplateImages','XimNewsDateSection','XimNewsDateSection');
             $selectableList = array('ImageFile','XimNewsImageFile');
	        } else {
              $typeList = array('Projects', 'Project', 'Server', 'Section', 'ImagesFolder', 'ImagesRootFolder', 'ImageFile', 'TemplateImages');
             $selectableList = array('ImageFile');
			 }
			break;


		case 'ximnewsnewlanguage':  /* ##################### ximnewsnewlanguage ############################ */
			$typeList = array('Projects', 'Project', 'Server', 'XimNewsSection', 'XimNewsNews','XimNewsNewLanguage','XimNewsDateSection','XimNewsNew');
			$selectableList = array('XimNewsNewLanguage');
			break;

		case  'ximlet': /* ##################### ximlet ############################ */
          if (ModulesManager::isEnabled('ximNEWS')) {
              $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection','XimNewsBulletins','XimNewsColector','XimletRootFolder', 'XimletFolder', 'XmlDocument', 'XmlContainer', 'XimletContainer', 'Ximlet');
               $selectableList = array('XmlDocument', 'Ximlet');
	        } else {
              $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimletRootFolder', 'XimletFolder', 'XmlDocument', 'XmlContainer', 'XimletContainer', 'Ximlet');
              $selectableList = array('XmlDocument', 'Ximlet');
			 }
			break;


		case  'common': /* ##################### common ############################ */
			$typeList = array('Projects', 'Project', 'Server', 'Section','XimNewsSection', 'CommonRootFolder', 'CommonFolder', 'TextFile', 'BinaryFile', 'ImageFile');
			$selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile');
			break;

		case  'import': /* ##################### import ############################ */
			$typeList = array('Projects', 'Project', 'Server', 'Section','XimNewsSection','ImportRootFolder', 'ImportFolder', 'TextFile', 'BinaryFile', 'ImageFile', 'NodeHt');
			$selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile', 'Nodeht');
			break;

		case  'common_import': /* ##################### common_import ############################ */
			$typeList = array('Projects', 'Project', 'Server', 'Section','XimNewsSection', 'ImportRootFolder', 'ImportFolder', 'CommonRootFolder', 'CommonFolder', 'TextFile', 'BinaryFile', 'ImageFile');
		$selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile');
			break;


		case  'xmldocs': /* ##################### xmldocs ############################ */
			$typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection', 'XmlRootFolder', 'XmlDocument', 'XmlContainer' );
			$selectableList = array('XmlDocument');
			break;

		//Parte añadida para la acción asociar pvd a rol. Se pretende presentar las plantillas vista en el árbol.
		case  'pvds': /* ##################### pvds ############################ */
		   $typeList = array('Projects', 'Project', 'TemplateViewFolder', 'VisualTemplate');
	  	   $selectableList = array('TemplateViewFolder','VisualTemplate');
			break;

		case  'ximNEWS_common': /* ##################### ximNEWS_common ############################ */
			$typeList = array('Projects', 'Project','Server','CommonRootFolder', 'BinaryFile');
	   	$selectableList = array('BinaryFile');
			break;

		case  'ximNEWS_images': /* ##################### ximNEWS_images ############################ */
	  		 $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection', 'XimNewsImages', 'XimNewsImageFile');
	   	 $selectableList = array('XimNewsImageFile');
	   	 break;

	 	case  'ximNEWS_links': /* ##################### ximNEWS_links ############################ */
	  		 $typeList = array('Projects', 'Project', 'LinkManager', 'LinkFolder','Link');
	  		 $selectableList = array('Link');
			 break;

		case 'links': /* ############################ LINKS ################################################# */
           if (ModulesManager::isEnabled('ximNEWS')) {
                    $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection', 'XimNewsBulletins', 'XimNewsNews','XimNewsBulletinLanguage','XimNewsNewLanguage','XimNewsBulletin','XimNewsDateSection','XimNewsNew', 'XimNewsColector', 'LinkManager', 'LinkFolder', 'XmlRootFolder', 'XmlDocument', 'XmlContainer', 'CommonRootFolder', 'CommonFolder', 'Link', 'TextFile', 'BinaryFile', 'ImageFile');
                    $selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile','XimNewsBulletinLanguage','XimNewsNewLanguage');
	        }
           else {
                    $typeList = array('Projects', 'Project', 'Server', 'Section', 'LinkManager', 'LinkFolder', 'XmlRootFolder', 'XmlDocument', 'XmlContainer', 'CommonRootFolder', 'CommonFolder', 'Link', 'TextFile', 'BinaryFile', 'ImageFile');
                    $selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile');
			}
			$nodeTypeXimPorta=new NodeType();
			if ($nodeTypeXimPorta->IsNodeType('ximPORTA')) {
				$typeList[]='ximPORTA';
			}
			break;

		case 'ximletContainer':  /* ############################ ximletContainer ########################### */
          if (ModulesManager::isEnabled('ximNEWS')) {
              $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection','XimNewsBulletins','XimNewsColector','XimletRootFolder', 'XimletFolder', 'XimletContainer');
              $selectableList = array('XimletContainer');
			}else {
              $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimletRootFolder', 'XimletFolder', 'XimletContainer'	);
              $selectableList = array('XimletContainer');
			}
			break;

		case 'all': /* ############################ ALL ########################### */
			$typeList = null; //$nodeType->GetAllNodeTypes();
			$selectableList = null; //array('XmlDocument', 'Server', 'Link', 'ImageFile', 'TextFile', 'BinaryFile');
			/*		for($i=0; $i<sizeof($selectableList); $i++)
						{
						$nodeType->SetByName($selectableList[$i]);
						$selectableList[$i] = $nodeType->GetID();
						}*/
			break;
		}


		if($typeList != null)
			$typeList = nameToId($typeList);

		if($selectableList != null)
			$selectableList = nameToId($selectableList);

}
?>
