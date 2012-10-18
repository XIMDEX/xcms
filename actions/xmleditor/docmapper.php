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
include_once(XIMDEX_ROOT_PATH.'/inc/persistence/XSession.class.php');

XSession::check();

////
//// Begins action flow.
////

$nodeID = $_GET["nodeid"];
//$release = isset($_GET["release"]) ? $_GET['release'] : '';

if ($nodeID !== null) // && $release !== 0)
	{

	$config = new Config();
	$docNode = new Node($nodeID);

	$section = new Node($docNode->GetParent());
	$section->SetID($section->GetParent());
	$section->SetID($section->GetParent());
	$docStruct	= new StructuredDocument($nodeID);
	$docContent	= $docStruct->GetContent();
	$translationName = $section->GetAliasForLang($docStruct->GetLanguage());

	$urlRoot = $config->GetValue("UrlRoot");

	//echo $section->GetNodeName(). "-" .$docStruct->GetLanguage();
	if(!$translationName)
		$translationName = 'Seccion por defecto';


	$templateID = $docStruct->GetDocumentType();
	$docXap = $docNode->class->GetRenderizedContent(null, null, true);//'<docxap>';
	/*
	$docContent = '<?xml version="1.0" encoding="ISO-8859-1"?>'.'<?edxview ./templatemapper.php?nodeid='.$templateID.' ?>'.$docXap.'<titulo_pagina>'.preg_replace("/<|>/","",$translationName).'</titulo_pagina>'.$docContent.'</docxap>';
	*/

	$docContent = '<?xml version="1.0" encoding="ISO-8859-1"?>'.'<?edxview ' . $urlRoot . '/actions/xmleditor/templatemapper.php?nodeid='.$templateID.' ?>'.$docXap.$docContent.'</docxap>';

	header("Content-type: application/xml");
	echo $docContent;
	}
else
	{
	gPrintHeader();
	gPrintBodyBegin();
	gPrintMsg(_("Error with parameters"));
	gPrintBodyEnd();
	}
?>
