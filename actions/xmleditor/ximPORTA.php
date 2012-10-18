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



ModulesManager::file('/actions/xmleditor/ximportautils.inc');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title></title>
	<script type="text/javascript">
		
	</script>
<style type="text/css">
.fuente{
	font-family: verdana;
	font-size: 10px;
	width: 530px;;
	height: 340px;
	border-color : #8e8e8e;
	border-width : 1px;
	border-style : solid;
	background-color : #e6e6e6;
}
</style>
</head>

<body>
<img src="images/icons-editor/add-links.gif" alt="" border="0" id="addlink"><br/>
<?php


XSession::check();

$nodeID     = $_POST["nodeid"];
$nodeXimFolder =  $_POST["nodeLINK"];
$xml    = $_POST["xml"];

$ximporta=new ximportautils();

$xml_transformado=$ximporta->InsertaEnlacesXml(String::stripslashes($xml),$nodeID,$nodeXimFolder);

//GetRoot()
$nodeXX=new Node($nodeXimFolder);
$nombreXX=$nodeXX->GetNodeName();
?>
<script type="text/javascript">
	document.getElementById('addlink').style.visibility = "hidden";
	if(top.document.getElementById('aceptar2')) top.document.getElementById('aceptar2').style.visibility = "visible";
</script>
<img src="images/icons-editor/links-added.gif" alt="" border="0" id="addlink">

Los enlaces se han dado de alta en: <?echo $nombreXX;?>
<form name="enlaces">
<textarea name="xml" style="visibility:visible; width: 500px; height: 500px;"><?php
 echo $xml_transformado ?></textarea>
</form>
