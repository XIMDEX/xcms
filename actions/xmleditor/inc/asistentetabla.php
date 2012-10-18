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
<TITLE>ximTABLEASSISTANT</TITLE>
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
</style>
<script type="text/javascript" FOR="window" EVENT="onload">


for ( elem in window.dialogArguments )
  {
    switch( elem )
    {
    case "xml":
		origenXML.loadXML(window.dialogArguments["xml"]);
		break;
    }
}

pintar();


</script>
<script>
var depura = false;
var corrigecabecera = false;
clasecaboriginal = new Array();
var origenXML = new ActiveXObject("Msxml2.DOMDocument");
origenXML.async = true;
origenXML.resolveExternals = true;

var otroestilo = false;
var clase, alin, ancho, sumario;

function repintar()
{
	var mascols = 0; 
	var masfilas = 0;
	var fila, mifila, cuerpo; 
	mascols = document.formulario.mascolumnas.value;
	if (mascols=='') mascols=0;
	mascols = parseInt(mascols);
	masfilas = document.formulario.masfilas.value;
	if (masfilas=='') masfilas=0;
	masfilas = parseInt(masfilas);
	hayfilas = document.formulario.filas.value;
	hayfilas = parseInt(hayfilas);
	haycolumnas = document.formulario.columnas.value;
	haycolumnas = parseInt(haycolumnas);
	var suma = haycolumnas + mascols;
	
	var newNode;
	var estilo = "filaclara";

	
	
	//crear las nuevas filas
	for (i=0; i<masfilas; i++)
	{
		var clase;
		fila = "fila" + haycolumnas;
		newNode = origenXML.createNode(1, fila, "");
		origenXML.childNodes.item(0).childNodes.item(0).appendChild(newNode);
		
		//crear los elementos de las nuevas filas (sin las nuevas columnas de mas)
		for (j=0; j<haycolumnas; j++)
		{			
			elemento= "elemento" + (j+1);
			newNode = origenXML.createNode(1, elemento, "");
			newNode.setAttribute("texto", "");
			newNode.setAttribute("alin", "left");
			newNode.setAttribute("columnas", "1");
			newNode.setAttribute("filas", "1");
			newNode.setAttribute("clase", estilo);
			newNode.setAttribute("posicion", "top");
			cuerpo = origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(hayfilas+i);
			cuerpo.appendChild(newNode);
			parrafo = origenXML.createNode(1, "parrafo", "");
			parrafo.setAttribute("clase", clase);
			parrafo.setAttribute("referencia", "");
			cuerpo.childNodes.item(j).appendChild(parrafo);
			
			texto = origenXML.createTextNode("[parrafo]");
			cuerpo.childNodes.item(j).childNodes.item(0).appendChild(texto);
		}
		
	}	
	
	if (mascols> 0)
	{

		//'renombrar' (borrar y crear) de nuevo los nodos <fila> con el nuevo nº de columnas
		for (h=0; h<(hayfilas+masfilas); h++)
		{	
			var vueltas = origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(h).childNodes.length;
			fila = "fila" + (vueltas+mascols);
			newNode = origenXML.createNode(1, fila, "");
								
			//coge los hijos del antiguo y se los pone al nuevo
			for (m=0; m<vueltas; m++)
			{
				resto = origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(h).childNodes.item(0);

				newNode.appendChild(resto);	
			}
		
			oldChild = origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(h);
	
			origenXML.childNodes.item(0).childNodes.item(0).replaceChild(newNode, oldChild);	
					
			var num;
			
			//crear las nuevas columnas
			for (k=0; k<mascols; k++)
			{
				//alert(estilo);
				num = vueltas +1 + k;
				elemento= "elemento" + num;
				newNode = origenXML.createNode(1, elemento, "");
				newNode.setAttribute("texto", "");
				newNode.setAttribute("alin", "left");
				newNode.setAttribute("columnas", "1");
				newNode.setAttribute("filas", "1");
				newNode.setAttribute("clase", estilo);
				newNode.setAttribute("posicion", "top");
				cuerpo = origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(h);
				cuerpo.appendChild(newNode);
				parrafo = origenXML.createNode(1, "parrafo", "");
				parrafo.setAttribute("clase", clase);
				parrafo.setAttribute("referencia", "");
				cuerpo.childNodes.item(num-1).appendChild(parrafo);
				texto = origenXML.createTextNode("[parrafo]");
				cuerpo.childNodes.item(num-1).childNodes.item(0).appendChild(texto);	
			}
			
		
		}// cierre for h
		
	}//cierre if masfilas
	if (depura)
	{
	//alert('medio:     ' + origenXML.xml);
	}
	
	sumario = document.formulario.sumario.value;
	ancho = document.formulario.ancho.value;
	origenXML.childNodes.item(0).setAttribute("sumario",sumario);
	origenXML.childNodes.item(0).setAttribute("ancho",ancho);

	if (otroestilo)
	{
		clase = document.formulario.preclase.value;
		//alin = document.formulario.alin.value;
		//salto = document.formulario.salto.value;
		//espacio = document.formulario.espacio.value; 
		//margen = document.formulario.margen.value;
		origenXML.childNodes.item(0).setAttribute("clase",clase);
		//origenXML.childNodes.item(0).setAttribute("alin",alin);

		//origenXML.childNodes.item(0).setAttribute("salto",salto);
		//origenXML.childNodes.item(0).setAttribute("espacio",espacio);
		//origenXML.childNodes.item(0).setAttribute("margen",margen);
	}
	
	
	if (document.formulario.crearalt.checked)
	{
		var estiloF1, estiloF2;
		estiloF1 = document.formulario.estiloFimpar.value;
		estiloF2 = document.formulario.estiloFpar.value;
		nuevon = 0;
		for(q=0; q < origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(0).childNodes.length; q++)
		{
				clasecaboriginal[q] = origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(0).childNodes.item(q).getAttribute("clase");
		
		}
		
		corrigecabecera=true;


		for (n=nuevon; n < origenXML.childNodes.item(0).childNodes.item(0).childNodes.length; n++)
		{
			if (estilo == estiloF1) 
			{
				estilo = estiloF2;
			}
			else 
			{
				estilo = estiloF1;
			}
					
			for(p=0; p < origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(n).childNodes.length; p++)
			{
				origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(n).childNodes.item(p).setAttribute("clase",estilo);	
					
			}
		}
	}
	
	if (depura)
	{
	//alert('re-estilado:     ' + origenXML.xml);
	}
	
	if (corrigecabecera)
	{
		for(q=0; q < origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(0).childNodes.length; q++)
		{
				origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(0).childNodes.item(q).setAttribute("clase",clasecaboriginal[q]);
		
		}
	}
	
		
	if (depura)
	{
	//alert('FIN:     ' + origenXML.xml);
	}
	else
	{
		var arr = new Array();
		arr["xml_content"] = origenXML.xml;
		window.returnValue = arr;
		window.close();
	}

}

function cambiafilas(objeto)
{
	if (objeto.checked){
		document.formulario.estiloFimpar.disabled = false;
		document.formulario.estiloFpar.disabled = false;
	}
	else{
		document.formulario.estiloFimpar.disabled = true;
		document.formulario.estiloFpar.disabled = true;
	}
}

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
<body bgcolor="#e0dfe3" topmargin="3" leftmargin="5" ">
<!-- onload="depuracion(); -->
<table>
<form name="formulario">
<tr>
	<td>El número de columnas de la tabla es de:</td>
	<td><input type="text" name="columnas" value="" disabled="true" size="3" /></td>
</tr>
<tr>
	<td>El número de filas de la tabla es:</td>
	<td><input type="text" name="filas" value="" disabled="true" size="3" /></td>
</tr>
<tr>
	<td  valign="top">Añadir&nbsp;<input type="text" name="masfilas" value="" size="3" />&nbsp;filas</td> 
	<td><!-- <input type="Radio" value="0" name="dondefila" />&nbsp;al principio<br />
		<input type="Radio" value="1" name="dondefila" checked />&nbsp;al final  -->
	</td>
</tr>
<tr>
	<td  valign="top">Añadir&nbsp;<input type="text" name="mascolumnas" value="" size="3" />&nbsp;columnas</td> 
	<td><!-- <input type="Radio" value="0" name="dondecols" />&nbsp;al principio<br />
		<input type="Radio" value="1" name="dondecols" checked />&nbsp;al final -->
	</td>
</tr>

</table>
<table class="PVtabla">
			<tr>
				<td class="filaclaranegrita">Ancho</td>
				<td class="filacla"><input name="ancho" type="text" size="35" class="cajap" onChange="javascript:otroestilo=true;" /></td>
			</tr>
			<tr>
				<td class="filaclaranegrita">Sumario</td>
				<td class="filacla"><input name="sumario" type="text" size="35" class="caja" onChange="javascript:otroestilo=true;" /></td>
			</tr>
			<tr>
				<td class="filaclaranegrita">Estilo</td>
				<td>
					<select name="preclase" class="caja" style="width: 170px;" onChange="javascript:otroestilo=true;">

						<option value="" selected>Seleccionar para cambiar</option>
				  		<option value="tabla">Tabla con borde</option>
						<option value="tablasinborde">Tabla sin borde</option>
						<option value="tablasinbordeazul">Tabla sin borde (letra azul)</option>
						<option value="tablablanca">Tabla sin borde y fondo blanco</option>
					</select><br />
					<script>if (depura) document.write('<input type="text" name="clase" disabled="true"></input>'); else document.write('<input type="hidden" name="clase" disabled="true"></input>');</script>
				</td>
			</tr>
			<!-- <tr>
				<td class="filaclaranegrita">Alineaci&#243;n Horizontal</td>
				<td>
					<select name="prealin" class="caja" style="width: 170px;" onChange="javascript:otroestilo=true; document.formulario.alin.value = document.formulario.prealin.value;">

						<option value="" selected>Seleccionar para cambiar</option>
				  		<option value="">Sin alineaci&#243;n</option>
						<option value="left">Izquierda</option>
						<option value="center">Centro</option>
						<option value="right">Derecha</option>
					</select><br />
					<script>if (depura) document.write('<input type="text" name="alin" disabled="true"></input>'); else document.write('<input type="hidden" name="alin" disabled="true"></input>');</script>
				
				</td>
			</tr>
			<tr>
				<td class="filaclaranegrita">Salto</td>
				<td>
					<select name="presalto"  class="caja" style="width: 170px;" onChange="javascript:otroestilo=true; document.formulario.salto.value = document.formulario.presalto.value;">
						<option value="si">Seleccionar para cambiar</option>
				  		<option value="si">Si</option>
						<option value="no">No</option>
					</select><br />
					<script>if (depura) document.write('<input type="text" name="salto" disabled="true"></input>'); else document.write('<input type="hidden" name="salto" disabled="true"></input>');</script>
				
				</td>
			</tr> -->
			<!-- <tr>
				<td class="filaclaranegrita">Espacio entre celdas</td>
				<td><input name="espacio" type="text" size="35" class="cajap"/>
				</td>
			</tr>
			<tr>
				<td class="filaclaranegrita">Margen de celda</td>
				<td><input name="margen" type="text" size="35" class="cajap"/>
				</td>
			</tr> -->
		</table>
		<table>
			<tr>
		<td valign="top"><input type="checkbox" value="" name="crearalt" onclick="cambiafilas(this);"> <strong>Alternar estilo de filas &raquo;</strong></td>

		<td>
			<table>
				<tr>
					<td>Fila impar</td>
					<td><select  class="caja" style="width: 200px;" name="estiloFimpar" disabled>
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
					<td><select  class="caja" style="width: 200px;" name="estiloFpar" disabled>
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
			</table>
		
		
	</tr>
	
			<tr>
	<td colspan="2"><input type="button" value="Modificar" onClick="repintar();"></td>
</tr>
		</table>
		</form>
<script>
function pintar()
{
	var cols2 = 0;
	var maxcols = 0;
	var cols;
	var nodotexto;

	//for para recorrer todas las filas
	for (i=0; i<origenXML.childNodes.item(0).childNodes.item(0).childNodes.length; i++)
	{
		cols2 = 0;
	
		//for para recorrer todos los elementos de una fila
		for (j=0; j<origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(i).childNodes.length; j++)
		{
	
			cols = origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(i).childNodes.item(j).getAttribute("columnas");
			cols = parseInt(cols);
			cols2 += cols;
			
			//nodotexto = origenXML.childNodes.item(0).childNodes.item(i).childNodes.item(0).nodeValue;
	
		
		}
		
		if (cols2 > maxcols) maxcols = cols2;
	
	
	}

	clase = origenXML.childNodes.item(0).getAttribute("clase");
	alin = origenXML.childNodes.item(0).getAttribute("alin");
	sumario = origenXML.childNodes.item(0).getAttribute("sumario");
	ancho = origenXML.childNodes.item(0).getAttribute("ancho");
	salto = origenXML.childNodes.item(0).getAttribute("salto");
	
		
	document.formulario.clase.value = clase;
	//document.formulario.alin.value = alin;
	document.formulario.sumario.value = sumario;
	document.formulario.ancho.value = ancho;
	//document.formulario.salto.value = salto;
	
	document.formulario.columnas.value = maxcols;
	document.formulario.filas.value = origenXML.childNodes.item(0).childNodes.item(0).childNodes.length;
}

</script>

</body>
</html>
