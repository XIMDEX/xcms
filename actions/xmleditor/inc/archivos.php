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
<TITLE>Insertar enlace</TITLE>
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
<link rel="STYLESHEET" type="text/css" href="estilo_popup.css">
<script src="../../../xmd/js/ximdex_common.js" type="text/javascript"></script>

<script LANGUAGE="Javascript">
<!--


if (navegador == "ie")
	{
	var targetid = window.dialogArguments["nodeid"];
	var id_proj = window.dialogArguments["id_proj"];
	}
else
	{
		var targetid = window.parent.params["nodeid"];
		var id_proj = window.parent.params["id_proj"];
		isFolder = true;
		if (window.parent.params["url"] == "")
			{
			my_location = "../treeselector.php?contenttype=links&targetid=" + ID_imagen;
			document.getElementById('appletdiv').src = my_location;
			
//				alert(targetid);
			}
	}
var curId = 0, curDesc = '', isFolder = true, ID_imagen, path_G, el_id;
var el_id = null;


function IsDigit(allowPercent)
{
	if (allowPercent)
		return ((event.keyCode >= 48) && (event.keyCode <= 57) || event.keyCode == 37)
	else
		return (event.keyCode >= 48) && (event.keyCode <= 57)
}

function setInfo(path, ID, arr1, arr2) {
	if (phoundry.canal.length > 0){
		l = phoundry.canal.length
		for(n=0; n < l; n++){
			phoundry.canal.options[0].selected = true;
			phoundry.canal.options[phoundry.canal.selectedIndex] = null;
		}
	}
	if (arr1.length > 0 & arr1.length == arr2.length){
		nueva_opcion = new Option();
		nueva_opcion.value = "";
		nueva_opcion.text = "«seleccione canal»"
		if (navegador == "ie")
			{
			phoundry.canal.add(nueva_opcion);
			for (n=0; n<arr1.length; n++){
				nueva_opcion = new Option();
				nueva_opcion.value = arr1[n];
				
				nueva_opcion.text = arr2[n];
				phoundry.canal.add(nueva_opcion);
			}
		}
		else
			{
			phoundry.canal.appendChild(nueva_opcion);
			for (n=0; n<arr1.length; n++){
				nueva_opcion = new Option();
				nueva_opcion.value = arr1[n];
				
				nueva_opcion.text = arr2[n];
				phoundry.canal.appendChild(nueva_opcion);
				}
			}

		if (el_id!=null) phoundry.canal.value = el_id[1];
	}
	ID_imagen = ID;
	path_G = path;
	path = fin_ruta ( path );
	var w, h, factor;
	isFolder = false;
	document.getElementById('useButton').value = 'Insertar enlace';
	document.getElementById('previewImg').innerHTML = '<input type="Text"  class="cajag" style="width: 270px;" NAME="previewImgSrc" value="' + path + '" BORDER=0 ALT="vista previa">';
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
	if (isFolder && phoundry.referenciador.value =="") {
		alert('Por favor seleccione un archivo o introduzca un referenciador'); return;
	}
	var arr = new Array();
  	
	if(phoundry.canal.length > 0){
		if (phoundry.canal.value == "") {
			alert ('debe seleccionar un canal');
			return;
			}
		else{
			arr["ID"] = ID_imagen + "," + phoundry.canal.value;		
		}
	}
	else arr["ID"] = ID_imagen;
	arr["imagen"] = path_G;
	arr["clase"] = phoundry.clase.value;
	arr["referenciador"] = phoundry.referenciador.value;
	arr["ventana"] = phoundry.ventana.value;
	arr["borrar"] = "no";
	if (navegador == "ie")
		{
		window.returnValue = arr;
		window.close();
		}
	else
		{
		window.parent.params = arr;
		window.parent.document.getElementById('toFirefox').value = "false";
		window.parent.document.getElementById('Vmodal').style.visibility = "hidden";
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
	if (navegador == "firefox15")
 {
 		
if (window.parent.params["url"] != "") isFolder = false;
		el_id = limpia_cadena(window.parent.params["url"], ",");
		ID_imagen = el_id[0];
//		alert(ID_imagen);
		my_location = "../treeselector.php?contenttype=links&targetid=" + ID_imagen;
		document.getElementById('appletdiv').src = my_location;
		document.getElementById('previewImg').innerHTML = '<input type="Text"  class="cajag" NAME="previewImgSrc" value="' + el_id[0] + '" BORDER=0 ALT="vista previa">';
		document.getElementById('clase').value = window.parent.params["clase"];
		document.getElementById('referenciador').value = window.parent.params["referenciador"];
		document.getElementById('ventana').value = window.parent.params["ventana"];
 
	
	 if (window.parent.params["clase"] == '')
	 {
	 	 return;
	 }
	 else
	 {	
	 	document.getElementById('eliminar').style.visibility = "visible";
	 }
	 
}
 
}
function cerrar_ventana(){
	if (confirm("¿Desea eliminar el enlace?"))
		{
		var arr = new Array();
		arr["borrar"] = "si";
		if (navegador == "ie")
			{
			window.returnValue = arr;
			window.close();
			}
		else{
			window.parent.params = arr;
			window.parent.toFirefox = true;
			window.parent.document.getElementById('Vmodal').style.visibility = "hidden";
			window.parent.document.getElementById('toFirefox').value = "false";
			window.parent.aplica_firefox(window.parent.objeto_global, "enlace")
			}
		}
}
function limpia_cadena(item,delimiter) {
	tempArray=new Array(1);
	var Count=0;
	var tempString=new String(item);
	
  while (tempString.indexOf(delimiter)>0) {
    tempArray[Count]=tempString.substr(0,tempString.indexOf(delimiter));
	tempString=tempString.substr(tempString.indexOf(delimiter)+delimiter.length, tempString.length-tempString.indexOf(delimiter)); 
    Count=Count+1;
  }
  tempArray[Count]=tempString;
  return tempArray;
}

function ocultar_ventana ()
	{
	if (navegador == "firefox")
		{
		
		window.parent.toFirefox = true;
		window.parent.document.getElementById('Vmodal').sytle.visibility = "hidden";
		}
	}

//-->
</script>
<script LANGUAGE=Javascript FOR=window EVENT=onload>
<!--
if (navegador == "ie")
	{
  for ( elem in window.dialogArguments )
  {
    switch( elem )
    {
    case "url":
		isFolder = false;
		el_id = limpia_cadena(window.dialogArguments["url"], ",");
		document.getElementById('previewImg').innerHTML = '<input type="Text"  class="cajag" NAME="previewImgSrc" value="' + el_id[0] + '" BORDER=0 ALT="vista previa">';
     	ID_imagen = el_id[0];
		my_location = "../treeselector.php?contenttype=links&targetid=" + ID_imagen;
		document.getElementById('appletdiv').src = my_location;
		document.getElementById('eliminar').style.visibility = "visible";
      break;
    case "clase":
      phoundry.clase.value = window.dialogArguments["clase"];
      break;
    case "referenciador":
       phoundry.referenciador.value = window.dialogArguments["referenciador"];
      break;
	case "ventana":
       phoundry.ventana.value = window.dialogArguments["ventana"];
      break;
    }
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


</head>
<body bgcolor="#e0dfe3" topmargin=3 leftmargin=0 onLoad="init()">
<table  class="tabla" width="100%" align="center" cellpadding="2">
	<tr>
		<td class="filacerrar" align="right"><a href="#" onclick="javascript:if(navegador == 'firefox15'){window.parent.toFirefox = true;window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';} else{self.close();}" class="filacerrar">cerrar ventana <img src="../../../xmd/images/botones/cerrar.gif" alt="" border="0"></a></td>
	</tr>
</table>
<script LANGUAGE="Javascript">
document.write('<IFRAME application="yes" scrolling="No" ID="appletdiv" style="position:absolute;left:10px;top:27px;width:302px;height:308px;visibility:show" NAME="treeFrame" WIDTH=302 HEIGHT=308 SRC="../treeselector.php?contenttype=links&targetid=' + targetid + '"></IFRAME>');
</script>

<div id="imgDiv" style="position:absolute;left:10px;top:27px;width:302px;height:308px;visibility:hidden;border:2px inset window;background-color:#ffffff;padding:5px;"></div>
<div id="previewDiv" style="position:absolute;left:320px;top:25px;width:250px">
<FORM NAME="phoundry">
<table width="100%" class="tabla">
<tr>
	<td  COLSPAN=2 style="background:white;border:1px inset window;width:300px;height:18px;">
	<div id="previewImg"></div>
	</td></tr>
	<tr>
		<td class="filaclara">Canales disponibles</td>
		<td class="filaclara">
			<select name="canal" id="canal" class="cajag"></select>
		</td>
	</tr>
	<tr>
		<td class="filaclara">Referenciador</td>
		<td>
			<input type="Text" class="cajag" name="referenciador" id="referenciador"/>
		</td>
	</tr>
	<tr>
		<td class="filaclara">Ventana</td>
		<td>
			<select type="Text" class="cajag" name="ventana" id="ventana">
				<option value="_self">Misma ventana
				<option value="nueva">Ventana nueva
			</select>
		</td>
	</tr>	
	<tr>
		<td class="filaclara">Clase</td>
		<td>
			<select type="Text" class="cajag" name="clase" id="clase">
			
<?php


 ModulesManager::file("/inc/fsutils/FsUtils.class.php");

$estilosXml = FSUtils::file_get_contents("../xml/estilos.xml");

$domDoc = new DOMDocument();
$domDoc->validateOnParse = true;
$domDoc->preserveWhiteSpace = false;
$domDoc->loadXML($estilosXml);
$matriz = $domDoc->getElementsByTagname('ventana');

for ($i=0; $i<$matriz->length; $i++)
{
	$nombreventana = $matriz->item($i)->getAttribute("nombre");
	if ($nombreventana=='archivos.php')
	{
		
		$estilos = $matriz->item($i)->childNodes;//('estilos');
		$estilos2= $estilos->item(1)->childNodes;
		$num_estilos = $estilos2->length;
		echo "n= ".$num_estilos;
		for ($j=0; $j<$num_estilos; $j++)
		{
			
			$mivar = $estilos2->item($j)->nodeName;
			//echo "<br>".$mivar;
			if ($j%2!=0)
			{
			$nombre = $estilos2->item($j)->getAttribute("nombre");
			$vernombre = $estilos2->item($j)->getAttribute("vernombre");
			
			echo '<option value="'.$nombre.'">'.$vernombre.'</option>';
			
			}
		}
		
	}	
	
}

?>
				
				
			</select>
		</td>
	</tr>
	<tr>
		<td class="filaclara" align="center">
			<INPUT ID="useButton" TYPE="button" CLASS="botong" VALUE="Insertar enlace" onClick="useFile()"/>
		</td>
		<td class="filaclara" align="center">
			<INPUT ID="eliminar" TYPE="button" CLASS="botong" VALUE="Eliminar enlace" style="visibility:hidden;" onClick="cerrar_ventana();"/>
		</td>
	</tr>
</table>
</div>

<div id="folderDiv" style="position:absolute;left:320px;top:240px;width:188px;visibility:hidden"></div>

</body>
</HTML>
