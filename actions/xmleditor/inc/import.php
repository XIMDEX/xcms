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

?>            <HTML>
<head>
<TITLE>Insertar archivo</TITLE>
<style type="text/css">
.caja{
	border: thin;
	border-style: solid;
	border-width: 1px;
	border-color: #3E3E3E;
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #3D3D3D;
	width: 150px;
}
.cajag{
	border: thin;
	border-style: solid;
	border-width: 1px;
	border-color: #3E3E3E;
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #3D3D3D;
	width: 260px;
}
.normal{
	
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #3D3D3D;
	
}
</style>
<script src="../../../xmd/js/ximdex_common.js" type="text/javascript"></script>
<script LANGUAGE="Javascript">
<!--
if (navegador == "ie")
	{
	var targetid = window.dialogArguments["nodeid"];
	}
else
	{

	if (window.parent.campo_activo.value > 0)
		{
			targetid = window.parent.campo_activo.value;
		}
	else
		{
			targetid = 	window.parent.Xnodeparent;
		}

	}

var curId = 0, curDesc = '', isFolder = true, ID_imagen, path_G;


function IsDigit(allowPercent)
{
	if (allowPercent)
		return ((event.keyCode >= 48) && (event.keyCode <= 57) || event.keyCode == 37)
	else
		return (event.keyCode >= 48) && (event.keyCode <= 57)
}

function setInfo(path, ID) {
	ID_imagen = ID;
	path_G = path;
	path = fin_ruta ( path );
	var w, h, factor;
	isFolder = false;
	document.getElementById('useButton').value = 'Insertar archivo';
	document.getElementById('previewImg').innerHTML = '<input type="Text"  class="cajag" NAME="previewImgSrc" value="' + path + '" BORDER=0 ALT="vista previa">';
	}


function cancelAttribs() {
	var d = document;
	d.getElementById('swfDiv').style.visibility='hidden';
	d.getElementById('imgDiv').style.visibility='hidden';
	d.getElementById('appletdiv').style.visibility='visible';
	if (parent.argsIn['upload'])
		d.getElementById('folderDiv').style.visibility='visible';
	d.phoundry['useButton'].disabled=false;
}


function checkAspect(f,what,value) {
	f = document.forms[f];
	if (!f.keepAspect.checked) return;
	var org, factor;
	if (what == 'width') {
		if (f.width.value == '')
			f.height.value = '';
		else
			f.height.value = Math.round(f.orgHeight.value / (f.orgWidth.value/value));
	}
	else if (what == 'height') {
		if (f.height.value == '')
			f.width.value = '';
		else
			f.width.value = Math.round(f.orgWidth.value / (f.orgHeight.value/value));
	}
}

function useFile()
{
	if (isFolder) {
		alert('Por favor seleccione un archivo'); return;
	}
	var arr = new Array();
  	arr["ID"] = ID_imagen;
	arr["imagen"] = path_G;
	if (navegador == "ie")
	{
window.returnValue = arr;
	window.close();
	}
	else
		{
		window.parent.campo_activo.value = arr["ID"];
		window.parent.campo_activo.focus();
		window.parent.document.getElementById('Vmodal').style.visibility = "hidden";
			window.parent.document.getElementById('toFirefox').value = "false";
			window.parent.aplica_firefox(window.parent.objeto_global, "enlace")
		}
	
}


function addFile() 
{
	if (!isFolder) {
		alert('Seleccione una carpeta'); return;
	}
	treeFrame.location='new_image.php?qs='+qs+'&l=english&bdir='+bdir+'&dirId='+curId+'&dir='+curPath;
}

function delFile()
{
	if (isFolder) {
		alert('Por favor seleccione un archivo'); return;
	}
	var imgname = curPath.substring(curPath.lastIndexOf('/')+1, curPath.length);
	var msg = "Are you sure you want to delete "+unescape(imgname)+"?";	if (confirm(msg))
		treeFrame.location.href = 'file_res.php?ref=/editor/img_props.php&qs='+qs+'&l=english&bdir='+bdir+'&dirId='+curId+'&action=delFile&file=' + curPath;
}

function setPaneColor(c) {
	document.getElementById('previewImg').style.background = c;
}


function init() {
	var D = document;
	var args = parent.argsIn;
	
}
function cerrar_ventana(){
	if (confirm("¿Desea eliminar el enlace?"))
		{
		var arr = new Array();
		arr["borrar"] = "si";
		window.returnValue = arr;
		window.close();
		}
}

//-->
</script>
<script LANGUAGE=Javascript FOR=window EVENT=onload>
<!--
  for ( elem in window.dialogArguments )
  {
    switch( elem )
    {
    case "url":
		isFolder = false;
		document.getElementById('previewImg').innerHTML = '<input type="Text"  class="cajag" NAME="previewImgSrc" value="' + window.dialogArguments["url"] + '" BORDER=0 ALT="vista previa">';
     	ID_imagen = window.dialogArguments["url"];
		my_location = "../treeselector.php?contenttype=import&targetid=" + ID_imagen;
		document.getElementById('appletdiv').src = my_location;
      break;
   	}
  }
// -->
</script>
<style TYPE="text/css">
<!--

body, td, button, input, .txt, select {
	font: MessageBox;
}

.txt,select { width:160px; padding:0px; margin:0px; border:1px inset window;}

-->
</style>
<link rel="STYLESHEET" type="text/css" href="estilo_popup.css">
<script src="../../../xmd/js/ximdex_common.js" type="text/javascript"></script>


</head>
<body bgcolor="#e0dfe3" topmargin=3 leftmargin=0 onLoad="init()">
<table  class="tabla" width="100%" align="center" cellpadding="2">
	<tr>
		<td class="filacerrar" align="right"><a href="#" onclick="javascript:if(navegador == 'firefox15'){window.parent.toFirefox = true;window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';} else{self.close();}" class="filacerrar">cerrar ventana <img src="../../../xmd/images/botones/cerrar.gif" alt="" border="0"></a></td>
	</tr>
</table>
<script LANGUAGE="Javascript">
document.write('<IFRAME application="yes" ID="appletdiv" scrolling="No" style="position:absolute;left:10px;top:27px;width:302px;height:308px;visibility:show" NAME="treeFrame" WIDTH=302 HEIGHT=308 SRC="../treeselector.php?contenttype=import&targetid=' + targetid + '"></IFRAME>');
</script>

<div id="imgDiv" style="position:absolute;left:10px;top:27px;width:302px;height:308px;visibility:hidden;border:2px inset window;background-color:#ffffff;padding:5px;"></div>
<div id="previewDiv" style="position:absolute;left:320px;top:25px;width:250px">
<FORM NAME="phoundry">
<TABLE WIDTH="100%">
<tr>
	<td ID="previewImg" COLSPAN=2 style="background:white;border:1px inset window;width:300px;height:18px;">&nbsp;</td></tr>
<tr>
		<td colspan="2" nowrap>
			<INPUT ID="useButton" TYPE="button" CLASS="but" VALUE="Insertar archivo" onClick="useFile()">
			
		</td>
	</tr>
</table>
</div>

<div id="folderDiv" style="position:absolute;left:320px;top:240px;width:188px;visibility:hidden"></div>

</body>
</HTML>
