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
<TITLE>ximTABLE</TITLE>
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
.cajaxp{
	border: thin;
	border-style: solid;
	border-width: 1px;
	border-color: #3E3E3E;
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #3D3D3D;
	width: 30px;
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
.celdagestion {
	background-color:#808080;
}
.celdaesquina{
	color:#808080;
}

</style>
<script LANGUAGE="Javascript">
var depura = false;
function crear_tabla(){
	var ximT = new ActiveXObject("Msxml2.DOMDocument");
	ximT.async = false;
	ximT.resolveExternals = false; 
	var root;
	var newNode;
	ximT.appendChild(ximT.createElement("tabla"));
	var tabla = ximT.childNodes;

// Creo la tabla
		tabla(0).setAttribute("clase", wtabla.estilotabla.value);
		tabla(0).setAttribute("ancho", wtabla.ancho.value);
		tabla(0).setAttribute("salto", "no");
		tabla(0).setAttribute("espacio", "0");
		tabla(0).setAttribute("alin", "center");
		tabla(0).setAttribute("sumario", wtabla.sumario.value);
		newNode = ximT.createNode(1, "cuerpo-tabla", "");
		tabla(0).appendChild(newNode);
// Fin creación tabla
	var Filas = wtabla.filas.value;
	var Cols = wtabla.columnas.value;
	var colCab = Cols;
	var estiloC = "filaclara";
	var estiloF1 = "filaclara";
	var estiloF2 = "filaclara";
	
// creo el parrafo general
		

	estiloP = wtabla.estiloparrafo.value;
	if (wtabla.crearcab.checked){
		if (wtabla.combinarcab.checked)	colCab = 1;
		estiloC = wtabla.estilocabecera.value;
		}
	if (wtabla.crearalt.checked){
		estiloF1 = wtabla.estiloFimpar.value;
		estiloF2 = wtabla.estiloFpar.value;
		}
// creo la cabecera
	cuerpoT = tabla(0).childNodes;
	fila = "fila" + colCab
	newNode = ximT.createNode(1, fila, "");
	newNode.setAttribute("clase", estiloC);
	cuerpoT(0).appendChild(newNode);
	cuerpocab = cuerpoT(0).childNodes;
	for (n = 0; n < colCab;	n ++){
		elemento = "elemento" + (n + 1);
		newNode = ximT.createNode(1, elemento, "");
		newNode.setAttribute("texto", "");
		newNode.setAttribute("alin", "left");
		if(wtabla.combinarcab.checked) newNode.setAttribute("columnas", Cols);
		else newNode.setAttribute("columnas", "1");
		newNode.setAttribute("filas", "1");
		newNode.setAttribute("clase", estiloC);
		newNode.setAttribute("posicion", "top");
		cuerpocab(0).appendChild(newNode);
		
		parrafo = ximT.createNode(1, "parrafo", "");
		parrafo.setAttribute("clase", wtabla.estiloparrafo.value);
		parrafo.setAttribute("referencia", "");
		cuerpocab(0).childNodes.item(n).appendChild(parrafo);
		
		texto = ximT.createTextNode("[parrafo]");
		cuerpocab(0).childNodes.item(n).childNodes.item(0).appendChild(texto);

		}
Filas--;
estilo = estiloF1;
	for (i = 0; i < Filas; i++){

		if (estilo == estiloF1) estilo = estiloF2;
		else estilo = estiloF1;

		fila = "fila" + Cols;
		newNode = ximT.createNode(1, fila, "");
		newNode.setAttribute("clase", estilo);
		cuerpoT(0).appendChild(newNode);
		cuerpoF = cuerpoT(0).childNodes.item(i+1);		
		
		for (l=0; l < Cols; l++){
			
			elemento = "elemento" + (l + 1);
			newNode = ximT.createNode(1, elemento, "");
			newNode.setAttribute("texto", "");
			newNode.setAttribute("alin", "left");
			newNode.setAttribute("columnas", "1");
			newNode.setAttribute("filas", "1");
			newNode.setAttribute("clase", estilo);
			newNode.setAttribute("posicion", "top");
			cuerpoF.appendChild(newNode);
			
			parrafo = ximT.createNode(1, "parrafo", "");
			parrafo.setAttribute("clase", wtabla.estiloparrafo.value);
			parrafo.setAttribute("referencia", "");
			cuerpoF.childNodes.item(l).appendChild(parrafo);
			
			texto = ximT.createTextNode("[parrafo]");
			cuerpoF.childNodes.item(l).childNodes.item(0).appendChild(texto);
		}
	}

	var arr = new Array();
	arr["xml_content"] = ximT.xml;
	window.returnValue = arr;
	window.close();
}
function cambiacab(objeto){
	if (objeto.checked){
		wtabla.combinarcab.disabled = false;
		wtabla.estilocabecera.disabled = false;
	}
	else{
		wtabla.combinarcab.disabled = true;
		wtabla.estilocabecera.disabled = true;
	}
}
function cambiafila(objeto){
	if (objeto.checked){
		wtabla.estiloFimpar.disabled = false;
		wtabla.estiloFpar.disabled = false;
	}
	else{
		wtabla.estiloFimpar.disabled = true;
		wtabla.estiloFpar.disabled = true;
	}
}

</script>

<style TYPE="text/css">
<!--

body, td, button, input, .txt, select {
	font: MessageBox;
}

.txt,select { width:160px; padding:0px; margin:0px; border:1px inset window;}
td {
font-family: Verdana;
font-size:10px;
}
-->
</style>
<link rel="STYLESHEET" type="text/css" href="estilo_popup.css">
</head>
<body bgcolor="buttonFace" topmargin="3" leftmargin="5">
<form name="wtabla">
<table border="0" cellpadding="1" cellspacing="1" style="left-margin: 5px;">
	<tr>
		<td colspan="3"><strong>Propiedades de la tabla</strong></td>
	</tr>
	<tr>
		<td>Nº de filas</td>
		<td colspan="2"><input type="Text" class="cajaxp" name="filas" value="2" tabindex="1" >
		&nbsp;Nº de columnas
		<input type="Text" class="cajaxp" name="columnas" value="5"  tabindex="2" ></td>
	</tr>
	<tr>
		<td>Estilo de la tabla</td>
		<td colspan="2"><select class="caja" style="width: 200px;" name="estilotabla"  tabindex="3" >
				  		<option value="tabla">Tabla con borde</option>
						<option value="tablasinborde">Tabla sin borde</option>
						<option value="tablasinbordeazul">Tabla sin borde (letra azul)</option>
						<option value="tablablanca">Tabla sin borde y fondo blanco</option>
					</select></td>
	</tr>
	<tr>
		<td>Ancho</td>
		<td colspan="2"><input type="Text" class="cajaxp" name="ancho" value="80"  tabindex="4" ></td>
	</tr>
	<tr>
		<td>Sumario</td>
		<td colspan="2"><input type="Text" class="cajag" name="sumario" value=""  tabindex="5" ></td>
	</tr>
	<tr>
		<td colspan="3"><hr></td>
	</tr>
	<tr>
		<td colspan="3"><input type="checkbox" value="" name="crearcab" onclick="cambiacab(this)"  tabindex="6" > <strong>Crear una cabecera de tabla</strong></td>
	</tr>
	<tr>
		<td colspan="2">¿Desea combinar todas las columnas en una?</td>
		<td><input type="checkbox" value="" name="combinarcab" disabled="true"  tabindex="7" ></td>
	</tr>
	<tr>
		<td>Estilo de la cabecera</td>
		<td colspan="2"><select  class="caja" style="width: 200px;" name="estilocabecera" disabled="true"  tabindex="8" >
				  		<option value="normal">Normal</option>
						<option value="normalgris">Normal gris</option>
						<option value="normalazul">Normal azul</option>
						<option value="normalazulnegrita">Normal azul negrita</option>
						<option value="textonegrita">Negrita </option>
						<option value="textocursiva">Cursiva</option>
              			<option value="textonegritasubrayado">Negrita y subrayado</option>
						<option value="textonegritasubrayadocursiva">Negrita, subrayado y cursiva</option>
						<option value="cabeceratabla" selected>Cabecera de tabla </option>
						<option value="cabeceratablagris">Cabecera de tabla gris</option>
						<option value="filaclara">Fondo gris claro</option>
						<option value="filaoscura">Fondo gris oscuro </option>
						<option value="filaclaranegrita">Fondo gris claro negrita</option>
						<option value="filaoscuranegrita">Fondo gris oscuro negrita</option>
              			</select></td>
	</tr>
	<tr>
		<td colspan="3"><hr></td>
	</tr>
	<tr>
		<td rowspan="2" align="left"><input type="checkbox" value="" name="crearalt" onclick="cambiafila(this);"  tabindex="9" > <strong>Alternar estilo de filas &raquo;</strong></td>
		<td>Fila impar</td>
		<td><select  class="caja" style="width: 200px;" name="estiloFimpar" disabled  tabindex="10" >
				  		<option value="normal">Normal</option>
						<option value="normalgris">Normal gris</option>
						<option value="normalazul">Normal azul</option>
						<option value="normalazulnegrita">Normal azul negrita</option>
						<option value="textonegrita">Negrita </option>
						<option value="textocursiva">Cursiva</option>
              			<option value="textonegritasubrayado">Negrita y subrayado</option>
						<option value="textonegritasubrayadocursiva">Negrita, subrayado y cursiva</option>
						<option value="cabeceratabla">Cabecera de tabla </option>
						<option value="cabeceratablagris">Cabecera de tabla gris</option>
						<option value="filaclara" selected>Fondo gris claro</option>
						<option value="filaoscura">Fondo gris oscuro </option>
						<option value="filaclaranegrita">Fondo gris claro negrita</option>
						<option value="filaoscuranegrita">Fondo gris oscuro negrita</option>
              			</select></td>
	</tr>
	<tr>
		<td>Fila par</td>
		<td colspan="2"><select  class="caja" style="width: 200px;" name="estiloFpar" disabled  tabindex="11" >
				  		<option value="normal">Normal</option>
						<option value="normalgris">Normal gris</option>
						<option value="normalazul">Normal azul</option>
						<option value="normalazulnegrita">Normal azul negrita</option>
						<option value="textonegrita">Negrita </option>
						<option value="textocursiva">Cursiva</option>
              			<option value="textonegritasubrayado">Negrita y subrayado</option>
						<option value="textonegritasubrayadocursiva">Negrita, subrayado y cursiva</option>
						<option value="cabeceratabla">Cabecera de tabla </option>
						<option value="cabeceratablagris">Cabecera de tabla gris</option>
						<option value="filaclara">Fondo gris claro</option>
						<option value="filaoscura" selected>Fondo gris oscuro </option>
						<option value="filaclaranegrita">Fondo gris claro negrita</option>
						<option value="filaoscuranegrita">Fondo gris oscuro negrita</option>
              			</select></td>
	</tr>
	<tr>
		<td colspan="3"><hr></td>
	</tr>
	<tr>
		<td colspan="3"><strong>Contenido</strong></td>
	</tr>
	<tr>
		<td>Estilo general</td>
		<td colspan="2"><select class="caja" style="width: 200px;" name="estiloparrafo" tabindex="12" >
				<option value="Tnormal" selected="selected">Normal</option>
				<option value="Tcursiva">Cursiva</option>
				<option value="Tenlacenegrita">Negrita</option>
				<option value="Tenlacenegritasubrayado">Negrita y subrayado</option>
				<option value="Tenlacenegritasubrayadocursiva">Negrita, subrayado y cursiva</option>
			</select></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center">
		
		<INPUT ID="cancelar" TYPE="button" CLASS="botong" VALUE="Cancelar" onClick="javascript: window.close();"  tabindex="13" /></td>
		<td align="center" colspan="2"><INPUT ID="pordefecto" TYPE="button" CLASS="botong" VALUE="Por defecto" onClick="wtabla.reset();"  tabindex="14" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT ID="aceptar" TYPE="button" CLASS="botong" VALUE="Crear tabla" onClick="crear_tabla()"  tabindex="15" /></td>
	</tr>
</table>
<!--textarea rows="20" cols="90" name="resultado"></textarea-->
</form>


</body>
</html>
