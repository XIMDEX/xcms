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

?>            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
             <head><link rel="STYLESHEET" type="text/css" href="../../xmd/style/estilo_ximDEX.css" />
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
<style>
h1{font-family:Arial;font-size:14px;margin:5px;}
p{font-family:Arial;font-size:12px;margin:2px;}
 input{font-family:Arial;font-size:14px;margin:2px;}
</style>
<link rel="STYLESHEET" type="text/css" href="estilo_popup.css" />

<style TYPE="text/css">
<!--

body, td, button, input, .txt, select {
	font: MessageBox;
}

.txt,select { width:160px; padding:0px; margin:0px; border:1px inset window;}

-->
</style>


<script src="../../../xmd/js/ximdex_common.js" type="text/javascript"></script>
<script>
function borrar(objeto){cadena='--Introduzca el contenido a pegar aqui--';
if (objeto.childNodes[0].nodeValue==cadena){
objeto.innerHTML='';
			}
		}

var el_edx = window.parent.params;
var el_h = window.parent.paramsH;

//window.parent.params["el_id"]	
var pos_cursor= window.parent.params.cursorIndex;
//var tr = window.parent.getSelection();
function funciona(){
	
	var sText = document.getElementById('eltext').value;
	
	if( el_edx.root.aFieldSelection != null )
		{
			el_edx.replaceSelection( sText );
			window.parent.document.getElementById('Vmodal').style.visibility = "hidden";
			return;
		}
	
	
	
	var s = el_edx.editText;
	
	el_edx.editText = s.substr( 0, pos_cursor ) + sText + s.substr( pos_cursor );
	el_h.innerText = el_edx.editText;
	
	el_edx.cursorIndex += sText.length;
	
	el_edx.saveNode();
	window.parent.paramsE.focus();
	el_edx.setCursorPosition( window.parent.paramsE );
//	alert(el_edx.editText);
	window.parent.document.getElementById('Vmodal').style.visibility = "hidden";
}

</script>
	 		 </head>
			 <body>
			 <table  class="tabla" width="100%" align="center" cellpadding="2">
	<tr>
		<td class="filacerrar" align="right"><a href="javascript:if(navegador == 'firefox15'){window.parent.toFirefox = true;window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';}" class="filacerrar">cerrar ventana <img src="../../../xmd/images/botones/cerrar.gif" alt="" border="0"></a></td>
	</tr>
<tr>
			<td align="center" class="filaclara">
				<table align=center class=tabla width=400 cellpadding="1" cellspacing="1">
					<tr>
						<td class="cabeceratabla" colspan="2">Portapapeles</td>
					</tr>
					<tr>
						<td>

<p class="normal">Introduzca el texto del portapapeles situando el cursor dentro de la ventana y pulsando Ctrl+V</p>
<form>
<textarea rows='10' cols='55' id="eltext" style='margin:10px; width:500;height:150' onclick='borrar(this);'>--Introduzca el contenido a pegar aqui--</textarea>
 <div align="right">
 
 <input ID="useButton" type='button' onclick="javascript:funciona();" value='Pegar' align='right' CLASS="botong" />&nbsp;&nbsp;
<input type='button' value='Cancelar' CLASS="botong" onclick="javascript:if(navegador == 'firefox15'){window.parent.toFirefox = true;window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';}" align='right'/>
 </form>
 </div>
 </td>
 </tr>
 </table>
 </td>
 </tr>
</body></html>