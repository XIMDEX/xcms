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
<HEAD>
<TITLE>Propiedades de Imagen</TITLE>
<link rel="STYLESHEET" type="text/css" href="estilo_popup.css">
<script src="../../../xmd/js/ximdex_common.js" type="text/javascript"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--

function IsDigit(allowPercent)
{
	if (allowPercent)
		return ((event.keyCode >= 48) && (event.keyCode <= 57) || event.keyCode == 37)
	else
		return (event.keyCode >= 48) && (event.keyCode <= 57)
}

function setInfo(path, ID) {
	ID_imagen = ID;
	var w, h, factor;
	isFolder = false;
	document.getElementById('useButton').value = 'Insertar Imagen';
	document.getElementById('previewImg').innerHTML = '<IMG NAME="previewImgSrc" SRC="../../../xmd/loadaction.php?action=filemapper&nodeid='+ID_imagen+'">';
	}


function cancelAttribs() {
	var d = document;
	d.getElementById('swfDiv').style.visibility='hidden';
	d.getElementById('imgDiv').style.visibility='hidden';
	d.getElementById('appletDiv').style.visibility='visible';
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
		alert('Por favor seleccione una imagen'); return;
	}
	var d = document
	img = d.images['previewImgSrc'].src
	var arr = new Array();
  	arr["ID"] = ID_imagen;
	arr["imagen"] = img;

	if (navegador == "ie")
	{
	window.returnValue = arr;
	window.close();
	}
	else
		{
		window.parent.campo_activo.value = ID_imagen;
		window.parent.campo_activo.focus();
		window.parent.document.getElementById('Vmodal').style.visibility = "hidden";
			//window.parent.document.getElementById('toFirefox').value = "false";
			//window.parent.aplica_firefox(window.parent.objeto_global, "enlace")
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
		alert('Select an image!'); return;
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
//-->
</SCRIPT>

<script LANGUAGE=Javascript FOR=window EVENT=onload>
<!--
  for ( elem in window.dialogArguments )
  {
    switch( elem )
    {
    case "url":
	if (window.dialogArguments["url"] == "")
		{
		isFolder = true;
		}
	else
		{
		isFolder = false;
		}
		document.getElementById('previewImg').innerHTML = '<input type="Text"  NAME="previewImgSrc" value="' + window.dialogArguments["url"] + '" BORDER=0 style="border-color:white; border-width: 0px;" ALT="vista previa">';
     	ID_imagen = window.dialogArguments["url"];
		my_location = "../treeselector.php?contenttype=images&targetid=" + ID_imagen;
		document.getElementById('appletdiv').src = my_location;
      break;
    case "clase":
      phoundry.clase.value = window.dialogArguments["clase"];
      break;
    case "referenciador":
       phoundry.referenciador.value = window.dialogArguments["referenciador"];
      break;
    }
  }
  
 
function cancelar(){
	if(navegador == 'firefox15'){
		window.parent.toFirefox = true;
		window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';
		}
}
// -->
</script>
<STYLE TYPE="text/css">
<!--

body, td, button, input, .txt, select {
	font: MessageBox;
}

.txt,select { width:160px; padding:0px; margin:0px; border:1px inset window;}

-->
</STYLE>
</HEAD>
<BODY BGCOLOR="#e0dfe3" TOPMARGIN=3 LEFTMARGIN=0 onLoad="init()">
<script>
if(navegador == 'firefox15')
{
	document.write('<table  class="tabla" width="100%" align="center" cellpadding="2"><tr><td class="filacerrar" align="right"><a href="javascript:cancelar();" class="filacerrar">cerrar ventana <img src="../../../xmd/images/botones/cerrar.gif" alt="" border="0"></a></td></tr></table>');
}
</script>
<SCRIPT LANGUAGE="Javascript">
document.write('<IFRAME application="yes" ID="appletDiv" scrolling="No" STYLE="position:absolute;left:10px;top:27px;width:302px;height:308px;visibility:show" NAME="treeFrame" WIDTH=302 HEIGHT=308 SRC="../treeselector.php?contenttype=images&targetid=' + targetid + '"></IFRAME>');
</SCRIPT>
<DIV ID="imgDiv" STYLE="position:absolute;left:10px;top:27px;width:302px;height:308px;visibility:hidden;border:2px inset window;background-color:#ffffff;padding:5px;">
<FORM NAME="imgForm">
<INPUT TYPE="hidden" NAME="orgWidth"><INPUT TYPE="hidden" NAME="orgHeight">
<FIELDSET>
<LEGEND><B>Propiedades de la Imagen</B></LEGEND>
<TABLE>
<TR>
	<TD ALIGN="right">Ancho:</TD>
	<TD><INPUT CLASS="txt" TYPE="text" NAME="width" SIZE=4 MAXLENGTH=4 onKeyPress="event.returnValue=IsDigit(0)" onBlur="checkAspect('imgForm','width',this.value)">
	<TD ROWSPAN=2 BGCOLOR="#ffffff"><INPUT TYPE="checkbox" NAME="keepAspect" CHECKED><IMG SRC="../images/images-images/lock.gif" WIDTH=11 HEIGHT=13 ALT="Constrain proportions"></TD>
</TR>
<TR>
	<TD ALIGN="right">Alto:</TD>
	<TD><INPUT CLASS="txt" TYPE="text" NAME="height" SIZE=4 MAXLENGTH=4 onKeyPress="event.returnValue=IsDigit(0)" onBlur="checkAspect('imgForm','height',this.value)">
</TR>
<TR>
	<TD ALIGN="right">Borde:</TD>
	<TD COLSPAN=2><INPUT CLASS="txt" TYPE="text" NAME="border" SIZE=2 MAXLENGTH=2 onKeyPress="event.returnValue=IsDigit(0)">
</TR>
<TR>
	<TD ALIGN="right">Espacio Vertical:</TD>
	<TD COLSPAN=2><INPUT CLASS="txt" TYPE="text" NAME="vspace" SIZE=2 MAXLENGTH=2 onKeyPress="event.returnValue=IsDigit(0)">
</TR>
<TR>
	<TD ALIGN="right">Espacio Horizontal:</TD>
	<TD COLSPAN=2><INPUT CLASS="txt" TYPE="text" NAME="hspace" SIZE=2 MAXLENGTH=2 onKeyPress="event.returnValue=IsDigit(0)">
</TR>
<TR>
	<TD ALIGN="right">Alineación:</TD>
	<TD COLSPAN=2><SELECT NAME="align">
	<OPTION VALUE="">[none]</OPTION>
	<OPTION VALUE="left">Izquierda</OPTION>
	<OPTION VALUE="right">Derecha</OPTION>
	<OPTION VALUE="top">Alto</OPTION>
	<OPTION VALUE="middle">Medio</OPTION>
	<OPTION VALUE="bottom">Abajo</OPTION>
	<OPTION VALUE="absMiddle">Medio Absoluto</OPTION>
	</SELECT></TD>
</TR>
<TR>
	<TD ALIGN="right">Texto Alternativo:</TD>
	<TD COLSPAN=2><INPUT CLASS="txt" TYPE="text" NAME="alt"></TD>
</TR>
<TR ID="css" STYLE="display:none">
	<TD ALIGN="right">Class:</TD>
	<TD COLSPAN=2><INPUT CLASS="txt" TYPE="text" NAME="Fclass"></TD>
</TR>
<TR>
	<TD></TD>
	<TD COLSPAN=2>
	<INPUT CLASS="but" TYPE="button" VALUE="Aceptar" onClick="embedFile(false)">
	<INPUT CLASS="but" TYPE="button" VALUE="Cancelar" onClick="cancelAttribs()">
	</TD>
</TR>
</TABLE>
</FIELDSET>
</FORM>
</DIV>

<DIV ID="previewDiv" STYLE="position:absolute;left:320px;top:25px;width:260px">
<FORM NAME="phoundry">
<div style="background:white;border:1px inset window;width:260px;height:190px; overflow: auto;">
<table width="100%">
<TR><TD COLSPAN=2 align="center" ID="previewImg" STYLE="background:white;border:1px inset window;width:260px;height:180px;" />&nbsp;</TD></TR>
</table>
</div>
<TABLE WIDTH="100%">
<TR><TD>
	<!--TABLE CELLSPACING=1 CELLPADDING=0 BORDER=0><TR>
	<TD BGCOLOR="#000000"><A HREF="javascript:setPaneColor('#000000')"><IMG SRC="../images/images-images/pixel.gif" WIDTH=10 HEIGHT=10 BORDER=0></A></TD>
	<TD BGCOLOR="#444444"><A HREF="javascript:setPaneColor('#444444')"><IMG SRC="../images/images-images/pixel.gif" WIDTH=10 HEIGHT=10 BORDER=0></A></TD>
	<TD BGCOLOR="#888888"><A HREF="javascript:setPaneColor('#888888')"><IMG SRC="../images/images-images/pixel.gif" WIDTH=10 HEIGHT=10 BORDER=0></A></TD>
	<TD BGCOLOR="#cccccc"><A HREF="javascript:setPaneColor('#cccccc')"><IMG SRC="../images/images-images/pixel.gif" WIDTH=10 HEIGHT=10 BORDER=0></A></TD>
	<TD BGCOLOR="#ffffff"><A HREF="javascript:setPaneColor('#ffffff')"><IMG SRC="../images/images-images/pixel.gif" WIDTH=10 HEIGHT=10 BORDER=0></A></TD>
	</TR></TABLE-->
</TD><TD ALIGN="right">
	<INPUT ID="useButton" TYPE="button" CLASS="but" VALUE="Insertar Imagen" onClick="useFile()">
</TD></TR>
</TABLE>
</DIV>

<DIV ID="folderDiv" STYLE="position:absolute;left:320px;top:240px;width:188px;visibility:hidden">
<TABLE>
<TR>
	<TD>Folder:</TD>
	<TD NOWRAP>
	<A HREF="javascript:addDir()"><IMG SRC="../images/images-images/add.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="New folder"></A>
	<A HREF="javascript:delDir()"><IMG SRC="../images/images-images/delete.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Delete folder"></A>
	</TD>
</TR>
<TR>
	<TD>Image/Flash movie:</TD>
	<TD NOWRAP>
	<A HREF="javascript:addFile()"><IMG SRC="../images/images-images/add.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="New image/Flash file"></A>
	<A HREF="javascript:delFile()"><IMG SRC="../images/images-images/delete.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Delete image/Flash file"></A>
	</TD>
</TR>
</TABLE>
</FORM>
</DIV> 

<FIELDSET>
<LEGEND><B>Propiedades de la imagen</B></LEGEND>
<TABLE WIDTH="100%">
<TR><TD><IMG SRC="../images/images-images/pixel.gif" HEIGHT="300" WIDTH="1"></TD></TR>
<TR>
	<TD WIDTH="100%" ALIGN="right">
	<INPUT ID="useButton" TYPE="button" CLASS="botong" VALUE="Cancelar" onClick="if(navegador == 'firefox15'){window.parent.toFirefox = true;window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';} else{self.close();}"/>
	</TD>
</TR>
</TABLE>
</FIELDSET>

</BODY>
</HTML>
