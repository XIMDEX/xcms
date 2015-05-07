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

include_once realpath(dirname(__FILE__) . "/../../../../").'/bootstrap/start.php';

if (!defined('XIMDEX_ROOT_PATH'))
    define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../../"));

ModulesManager::file('/inc/utils.php');

\Ximdex\Utils\Session::check();
$userID = \Ximdex\Utils\Session::get('userID');
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
    $prefabricados = array('all', 'images', 'xmldocs','links', 'common', 'import', 'common_import', 'ximlet', 'ximletContainer', 'dynamic', 'pvds', 'ximnewsnewlanguage');
    if(!in_array($contentType, $prefabricados) ) {
        $contentType = 'all';
    }
    $nodeType = new NodeType();
    if(!$selectedNodeID){
        $selectedNodeID =  \App::getValue("ProjectsNode");
    }

    if($contentType == 'dynamic'){
        $filterTypeAdd="";
        $targetNode=new Node($targetNodeID);
        $targetNodeType=new NodeType($targetNode->GetNodeType());

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
    $nodeType = new NodeType($idNodeType);
    if ($nodeType->get('IdNodeType') > 0) {
        $firstNode = array($nodeType->get('IdNodeType'));
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

function getContainers($idNodeTypeArray) {
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

function PrintContent($nodeID, $contentType=null, $pathList=null, $targetNodeID=null, $filterType=null,$selectableList=null, $typeList=null, $userID, $idNodeType){
    $desde = isset ($_GET['desde']) ? $_GET['desde'] : null;
    $hasta = isset ($_GET['hasta']) ? $_GET['hasta'] : null;
    $nelementos = isset ($_GET['nelementos']) ? $_GET['nelementos'] : null;

    $user = new User();
    $user->SetID($userID);

    $nodeList = getNodelist($userID, $nodeID);

    $selectedNode = new Node($nodeID);
    if(!$selectedNode->numErr) {
        $children = $selectedNode->GetChildren();
        $childNode = null;
        if ($children) {
            $i=0;
            foreach($children as $childID) {
                if ($user->HasPermission("view all nodes") or (is_array($nodeList) && in_array($childID,$nodeList)) or $user->IsOnNode($childID, true)) {
                    $selectedNode->SetID($childID);
                    $listaDatos=$selectedNode->DatosNodo();

                    $curNodeType = $listaDatos['NodeType'];
                    if ((!$typeList || in_array($curNodeType,$typeList))) {
                        $childNode[]	= $childID ;
                        $nodeName[]		= $listaDatos['NodeName'];
                        $systemType[]	= 1000-$selectedNode->nodeType->get('System');
                        $nodeIcon[]		= $selectedNode->nodeType->GetIcon();
                        $nodeTypeList[]	= $curNodeType;
                        $nodeState[]	= $listaDatos['State'];
                        $nodeChannels[]		= $selectedNode->GetChannels();
                    }
                }
            }
        }
        if (is_array($childNode)){
            array_multisort($systemType, $nodeName, $childNode, $nodeIcon, $nodeTypeList, $nodeState);

            if (($desde!=null)&&($hasta!=null))	{
                $childNode=array_slice($childNode,$desde,$hasta-$desde+1);
                $nodeName=array_slice($nodeName,$desde,$hasta-$desde+1);
                $nodeIcon=array_slice($nodeIcon,$desde,$hasta-$desde+1);
                $nodeState=array_slice($nodeState,$desde,$hasta-$desde+1);
                $nodeTypeList=array_slice($nodeTypeList,$desde,$hasta-$desde+1);
            }

            $l=sizeof($childNode);
            $numArchivos=0;
            if (($l > $nelementos)&&($nelementos!=0)){
                $partes = floor($l/$nelementos);
                if ($l%$nelementos != 0) $partes = $partes + 1;
                if ($desde==null) {
                    $desde_aux=$numArchivos;
                }
                else {
                    $desde_aux=$desde;
                }
                for ($k = 1; $k <= $partes; $k++) {
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

                    if (!$selectableList  || in_array($nodeTypeList[$i], $selectableList)){
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
                    }
                    $numArchivos = $numArchivos + $nelementos;
                    $desde_aux=$desde_aux+$nelementos;
                }
            }

            else {
                foreach($childNode as $childID) {
                    $selectedNode->SetID($childID);
                    $nodePath[]		= $selectedNode->GetPath();
                    $nodeIsFolder[]		= $selectedNode->nodeType->isFolder() ? '1' : '0';
                    $nodeChildCount[]= sizeof($selectedNode->GetChildren());
                    $nodeIcon[]		= $selectedNode->nodeType->GetIcon();
                }


                if (($desde!=null) && ($hasta!=null)) {
                    for($i=0; $i < $hasta-$desde+1; $i++){
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
                    }
                }
            }
        }

    }

}

function getNodelist($userID, $nodeID) {
    $user = new User();
    $user->SetID($userID);
    $group = new Group();


    if (!\Ximdex\Utils\Session::get("nodelist") or $nodeID==1)
    {
        $groupList= $user->GetGroupList();
        $groupList = array_diff($groupList,array($group->GetGeneralGroup()));

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

        $nodeList = getParentList($nodeList);

        \Ximdex\Utils\Session::set("nodelist", $nodeList);

    }else {
        $nodeList = \Ximdex\Utils\Session::get("nodelist");
    }

    return $nodeList;
}

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

function getTypeAndSelectableList($contentType, &$typeList, &$selectableList) {

    switch($contentType) {
        case 'image':
            if (ModulesManager::isEnabled('ximNEWS')) {
                $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection','XimNewsImages','XimNewsImagesFolder','XimNewsImageFile', 'ImagesFolder', 'ImagesRootFolder', 'ImageFile', 'XimNewsColector', 'TemplateImages','XimNewsDateSection','XimNewsDateSection');
                $selectableList = array('ImageFile','XimNewsImageFile');
            } else {
                $typeList = array('Projects', 'Project', 'Server', 'Section', 'ImagesFolder', 'ImagesRootFolder', 'ImageFile', 'TemplateImages');
                $selectableList = array('ImageFile');
            }
            break;


        case 'ximnewsnewlanguage':
            $typeList = array('Projects', 'Project', 'Server', 'XimNewsSection', 'XimNewsNews','XimNewsNewLanguage','XimNewsDateSection','XimNewsNew');
            $selectableList = array('XimNewsNewLanguage');
            break;

        case  'ximlet':
            if (ModulesManager::isEnabled('ximNEWS')) {
                $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection','XimNewsBulletins','XimNewsColector','XimletRootFolder', 'XimletFolder', 'XmlDocument', 'XmlContainer', 'XimletContainer', 'Ximlet');
                $selectableList = array('XmlDocument', 'Ximlet');
            } else {
                $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimletRootFolder', 'XimletFolder', 'XmlDocument', 'XmlContainer', 'XimletContainer', 'Ximlet');
                $selectableList = array('XmlDocument', 'Ximlet');
            }
            break;


        case  'common':
            $typeList = array('Projects', 'Project', 'Server', 'Section','XimNewsSection', 'CommonRootFolder', 'CommonFolder', 'TextFile', 'BinaryFile', 'ImageFile');
            $selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile');
            break;

        case  'import':
            $typeList = array('Projects', 'Project', 'Server', 'Section','XimNewsSection','ImportRootFolder', 'ImportFolder', 'TextFile', 'BinaryFile', 'ImageFile', 'NodeHt');
            $selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile', 'Nodeht');
            break;

        case  'common_import':
            $typeList = array('Projects', 'Project', 'Server', 'Section','XimNewsSection', 'ImportRootFolder', 'ImportFolder', 'CommonRootFolder', 'CommonFolder', 'TextFile', 'BinaryFile', 'ImageFile');
            $selectableList = array('XmlDocument', 'Link', 'ImageFile', 'TextFile', 'BinaryFile', 'ImageFile');
            break;


        case  'xmldocs':
            $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection', 'XmlRootFolder', 'XmlDocument', 'XmlContainer' );
            $selectableList = array('XmlDocument');
            break;


        case  'pvds':
            $typeList = array('Projects', 'Project', 'TemplateViewFolder', 'VisualTemplate');
            $selectableList = array('TemplateViewFolder','VisualTemplate');
            break;

        case  'ximNEWS_common':
            $typeList = array('Projects', 'Project','Server','CommonRootFolder', 'BinaryFile');
            $selectableList = array('BinaryFile');
            break;

        case  'ximNEWS_images':
            $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection', 'XimNewsImages', 'XimNewsImageFile');
            $selectableList = array('XimNewsImageFile');
            break;

        case  'ximNEWS_links':
            $typeList = array('Projects', 'Project', 'LinkManager', 'LinkFolder','Link');
            $selectableList = array('Link');
            break;

        case 'links':
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

        case 'ximletContainer':
            if (ModulesManager::isEnabled('ximNEWS')) {
                $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimNewsSection','XimNewsBulletins','XimNewsColector','XimletRootFolder', 'XimletFolder', 'XimletContainer');
                $selectableList = array('XimletContainer');
            }else {
                $typeList = array('Projects', 'Project', 'Server', 'Section', 'XimletRootFolder', 'XimletFolder', 'XimletContainer'	);
                $selectableList = array('XimletContainer');
            }
            break;

        case 'all':
            $typeList = null;
            $selectableList = null;
            break;
    }

    if($typeList != null)
        $typeList = nameToId($typeList);

    if($selectableList != null)
        $selectableList = nameToId($selectableList);

}
