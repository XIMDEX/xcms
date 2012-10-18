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
body
{
	background-color: #EDEDED;
}
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
</style>
<script src="../../../xmd/js/ximdex_common.js" type="text/javascript"></script>
<script LANGUAGE="Javascript">


var el_edx = window.parent.params;
var el_H = window.parent.paramsH;
//var el_h = window.parent.paramsH;

window.parent.document.getElementById('Vmodal').style.width = "500px";
window.parent.document.getElementById('Vmodal').style.height = "345px";

var uid = el_edx.hobj.uniqueID;

var sp2 = window.parent.document.getElementById("PVcontenedor");
		if (sp2.firstChild)
			{
			sp2.removeChild(sp2.firstChild); 
			sp2.style.visibility = "hidden;"
			sp2.style.left = "-1000px";
			}

function crear_tabla(){
var depura = false;


	
	var parser = new DOMParser(); 
   	ximT = document.implementation.createDocument("","tabla",null);
	var root;
	var newNode;
	//ximT.appendChild(ximT.createElement("tabla"));
	var tabla = ximT.childNodes;

// Creo la tabla
		tabla[0].setAttribute("clase", wtabla.estilotabla.value);
		tabla[0].setAttribute("ancho", wtabla.ancho.value);
		tabla[0].setAttribute("salto", "no");
		tabla[0].setAttribute("espacio", "0");
		tabla[0].setAttribute("alin", "center");
		tabla[0].setAttribute("sumario", wtabla.sumario.value);
		newNode = ximT.createElement('cuerpo-tabla');
		tabla[0].appendChild(newNode);
// Fin creación tabla
	var Filas = wtabla.filas.value;
	var Cols = wtabla.columnas.value;
	var colCab = Cols;
	estiloC = "filaclara";
	estiloF1 = "filaclara";
	estiloF2 = "filaclara";
	
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
	cuerpoT = tabla[0].childNodes;
	fila = "fila" + colCab
	newNode = ximT.createElement(fila);
	cuerpoT[0].appendChild(newNode);
	cuerpocab = cuerpoT[0].childNodes;
	for (n = 0; n < colCab;	n ++){
		elemento = "elemento" + (n + 1);
		newNode = ximT.createElement(elemento);
		newNode.setAttribute("texto", "");
		newNode.setAttribute("alin", "left");
		if(wtabla.combinarcab.checked) newNode.setAttribute("columnas", Cols);
		else newNode.setAttribute("columnas", "1");
		newNode.setAttribute("filas", "1");
		newNode.setAttribute("clase", estiloC);
		newNode.setAttribute("posicion", "top");
		cuerpocab[0].appendChild(newNode);
		
		parrafo = ximT.createElement("parrafo");
		parrafo.setAttribute("clase", wtabla.estiloparrafo.value);
		parrafo.setAttribute("referencia", "");
		cuerpocab[0].childNodes.item(n).appendChild(parrafo);
		
		texto = ximT.createTextNode("[parrafo]");
		

			
		cuerpocab[0].childNodes.item(n).childNodes.item(0).textContent = "[parrafo]";

		}
Filas--;
estilo = estiloF1;
	for (i = 0; i < Filas; i++){
		fila = "fila" + Cols;
		newNode = ximT.createElement(fila);
		cuerpoT[0].appendChild(newNode);
		cuerpoF = cuerpoT[0].childNodes.item(i+1);		

		if (estilo == estiloF1) estilo = estiloF2;
		else estilo = estiloF1;
		
		for (l=0; l < Cols; l++){
			
			elemento = "elemento" + (l + 1);
			newNode = ximT.createElement(elemento);
			newNode.setAttribute("texto", "");
			newNode.setAttribute("alin", "left");
			newNode.setAttribute("columnas", "1");
			newNode.setAttribute("filas", "1");
			newNode.setAttribute("clase", estilo);
			newNode.setAttribute("posicion", "top");
			cuerpoF.appendChild(newNode);
			
			parrafo = ximT.createElement("parrafo");
			parrafo.setAttribute("clase", wtabla.estiloparrafo.value);
			parrafo.setAttribute("referencia", "");
			cuerpoF.childNodes.item(l).appendChild(parrafo);
						
			texto = "[parrafo]";
			
			cuerpoF.childNodes.item(l).childNodes.item(0).textContent = "[parrafo]";
			
		}
	}
	
		var serializer = new XMLSerializer();
		var obj = serializer.serializeToString(ximT);
		var x = ximT;
		var arr = new Array();
		arr["xml_content"] = obj;
		
		
		
	
		//window.parent.document.getElementById('Vmodal').style.visibility = "hidden";
		//window.parent.document.getElementById('toFirefox').value = "false";
////////////////////////////////////
////////////////////////////////////
		var i;
		
//	alert(window.parent.paramsE);
		var children =el_H.childNodes;

		if( children.length > 0 )
		{
			for( i = 0; i < children.length; i++ )
			{
			//alert(children[i].uniqueID)
				if( children[i].uniqueID == uid )
					break;
			}
			if( i == children.length )
			{
				err( "Error: no se pudo insertar la etiqueta en el contenedor" );
				return;
			}
		}
		else
		{
			i = 0;
		}
		// insertamos la tabla en XML
		
		var x = x.childNodes[0].cloneNode( true );
		
		var editnode = el_edx.parent.getXmlNode();

		var xmlmgr = el_edx.root.getXmlManager();
		xmlmgr.openTransaction( editnode );
		
		xmlmgr.process( "insertNode", editnode, x, i );
		xmlmgr.closeTransaction();
		

		
		
		// insert seed node into HTML tree
		var tag = x.nodeName;
		
		//alert(tag);

		var node = el_edx.parent.createNode( tag, i );

		limpia_blanco(node);

		// insert the fragment
		tag = el_H.tagName;

		if( tag == "TBODY" )
		{	

			utilInsertRowAt( hobj, node, i );
		}
		else if( tag == "TR" )
		{

			utilInsertCellAt( hobj, node, i );
		}
		else
		{

			if( el_H.childNodes.length > 0 )
			{

				if (navegador == "ie")
					{
					//alert(hobj.childNodes[i].innerHTML);
					el_H.childNodes[i].insertAdjacentHTML( "BeforeBegin", node.xml );
					}
				else
					{

					var frag = document.createElement("div");
					var s =  serializa_me(node);
					frag.innerHTML = s;
					el_H.insertBefore( frag, el_H.childNodes[i] );
					}
			}
			else
			{

				el_H.innerHTML = serializa_me(node);
			}
		}
	
		
		// and do the association
		el_edx.parent.childNodes[i].associate( el_H.childNodes[i] );
		el_edx.parent.performAssociation( el_H.childNodes[i] );
		
		// see if we need to clean up empty container placeholder
		if( el_edx.parent.bEmpty && el_edx.parent.childNodes.length == 2 )
		{
			el_edx.parent.deleteChild( el_edx.parent.childNodes[1] );
		}
		el_edx.parent.bEmpty = false;
		
		// alert about changes
		el_edx.root.alertChange( editnode, el_edx.parent );
		
//////////////////////////////////////////////////
//////////////////////////////////////////////////
		el_edx.parent.moveUp( el_edx );
		
		
		el_edx.parent.load();
		
		window.parent.document.getElementById('Vmodal').style.visibility = "hidden";
		
		
		
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

function cancelar(){
	if(navegador == 'firefox15'){
		window.parent.toFirefox = true;
		window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';
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
<table  class="tabla" width="100%" align="center" cellpadding="2">
	<tr>
		<td class="filacerrar" align="right"><a href="javascript:cancelar();" class="filacerrar">cerrar ventana <img src="../../../xmd/images/botones/cerrar.gif" alt="" border="0"></a></td>
	</tr>
</table>
<form name="wtabla">
<table border="0" cellpadding="1" cellspacing="1" style="left-margin: 5px;" width="100%">
	<tr>
		<td colspan="3"><strong>Propiedades de la tabla</strong></td>
	</tr>
	<tr>
		<td>Nº de filas</td>
		<td colspan="2"><input type="Text" class="cajaxp" name="filas" value="2"  tabindex="1">
		&nbsp;Nº de columnas
		<input type="Text" class="cajaxp" name="columnas" value="5"  tabindex="2"></td>
	</tr>
	<tr>
		<td>Estilo de la tabla</td>
		<td colspan="2"><select class="caja" style="width: 200px;" name="estilotabla" tabindex="3">
				  		<option value="tabla">Tabla con borde</option>
						<option value="tablasinborde">Tabla sin borde</option>
						<option value="tablasinbordeazul">Tabla sin borde (letra azul)</option>
						<option value="tablablanca">Tabla sin borde y fondo blanco</option>
					</select></td>
	</tr>
	<tr>
		<td>Ancho</td>
		<td colspan="2"><input type="Text" class="cajaxp" name="ancho" value="80" tabindex="4"></td>
	</tr>
	<tr>
		<td>Sumario</td>
		<td colspan="2"><input type="Text" class="cajag" name="sumario" value="" tabindex="5"></td>
	</tr>
	<tr>
		<td colspan="3" ><hr></td>
	</tr>
	<tr>
		<td colspan="3"><input type="checkbox" value="" name="crearcab" onclick="cambiacab(this)" tabindex="6"> <strong>Crear una cabecera de tabla</strong></td>
	</tr>
	<tr>
		<td colspan="2">¿Desea combinar todas las columnas en una?</td>
		<td><input type="checkbox" value="" name="combinarcab" disabled="true" tabindex="7"></td>
	</tr>
	<tr>
		<td>Estilo de la cabecera</td>
		<td colspan="2"><select  class="caja" style="width: 200px;" name="estilocabecera" disabled="true" tabindex="8">
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
		<td rowspan="2" align="left"><input type="checkbox" value="" name="crearalt" onclick="cambiafila(this);" tabindex="9"> <strong>Alternar estilo de filas &raquo;</strong></td>
		<td>Fila impar</td>
		<td><select  class="caja" style="width: 200px;" name="estiloFimpar" disabled tabindex="10">
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
		<td colspan="2"><select  class="caja" style="width: 200px;" name="estiloFpar" disabled tabindex="11">
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
		<td colspan="2"><select class="caja" style="width: 200px;" name="estiloparrafo" tabindex="12">
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
		<td align="center"><INPUT ID="cancelar" TYPE="button" CLASS="botong" VALUE="Cancelar" onClick="if(navegador == 'firefox15'){window.parent.toFirefox = true;window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';}"  tabindex="13"/></td>
		<td align="center" colspan="2"><INPUT ID="cancelar" TYPE="button" CLASS="botong" VALUE="Por defecto" onClick="wtabla.reset();"  tabindex="14"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT ID="aceptar" TYPE="button" CLASS="botong" VALUE="Crear tabla" onClick="crear_tabla()"  tabindex="15"/></td>
	</tr>
</table>
<!--textarea rows="20" cols="90" name="resultado"></textarea-->
</form>
</body>
</html>
