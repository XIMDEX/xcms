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

?>            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>ximlet</title>
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
<style TYPE="text/css">
<!--

body, td, button, input, .txt, select {
	font: MessageBox;
}

.txt,select { width:160px; padding:0px; margin:0px; border:1px inset window;}

-->
</style>
<script type="text/javascript">
function cerrar_ventana(valor){
		var arr = new Array();
		arr["valor"] = valor;
		window.returnValue = arr;
		window.close();
		}

</script>
</head>

<body bgcolor="#e6e6e6" topmargin=3 leftmargin=0>
<table border="0" cellpadding="0" cellspacing="3" width="100%" height="100%">
	<tr>
		<td colspan="2" align="center"><strong>Seleccione la acción que desea realizar:</strong></td>
	</tr>
	<tr>
		<td align="center"><INPUT ID="useButton" TYPE="image" src="../../../xmd/images/botones/cambiar_ximlet.gif" CLASS="but" VALUE="Cambiar ximlet" onClick="cerrar_ventana('cambiar');"/></td>
		<td align="center"><INPUT ID="useButton" TYPE="image" src="../../../xmd/images/botones/editar_ximlet.gif" CLASS="but" VALUE="Editar ximlet" onClick="cerrar_ventana('editar');"/></td>
	</tr>
</table>

 
</div>


</body>
</html>
