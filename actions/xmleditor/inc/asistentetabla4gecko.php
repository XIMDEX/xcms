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
body {
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
<link rel="STYLESHEET" type="text/css" href="estilo_popup.css">
<script type="text/javascript" FOR="window" EVENT="onload">

</script>
<script>
var depura = false;
var corrigecabecera = false;

window.parent.document.getElementById('Vmodal').style.width = "440px";
window.parent.document.getElementById('Vmodal').style.height = "290px";

clasecaboriginal = new Array();
origenXML = document.implementation.createDocument("","",null);
temp = document.implementation.createDocument("","",null);
var otroestilo = false;
var clase, alin, ancho, sumario;

var el_edx = window.parent.params;

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

	//if (depura) alert(serializa_me(origenXML));
	
	estilo = estiloF1;
	//crear las nuevas filas
	for (i=0; i<masfilas; i++)
	{
		var clase;
		fila = "fila" + haycolumnas;
		
		//if (depura)	alert(fila);
		
		//alert(temp);
		newNode = temp.createElement(fila);
		
		origenXML.childNodes.item(0).appendChild(newNode);
		
		//if (depura) alert('antes: '+ serializa_me(origenXML));
		
		//if (depura) alert(haycolumnas);
		
		//crear los elementos de las nuevas filas (sin las nuevas columnas de mas)
		for (j=0; j<haycolumnas; j++)
		{			
			elemento= "elemento" + (j+1);
			newNode = temp.createElement(elemento);
			newNode.setAttribute("texto", "");
			newNode.setAttribute("alin", "left");
			newNode.setAttribute("columnas", "1");
			newNode.setAttribute("filas", "1");
			newNode.setAttribute("clase", estilo);
			newNode.setAttribute("posicion", "top");
			cuerpo = origenXML.childNodes.item(0).childNodes.item(hayfilas+i);
			cuerpo.appendChild(newNode);
			parrafo = temp.createElement("parrafo");
			parrafo.setAttribute("clase", clase);
			parrafo.setAttribute("referencia", "");
			cuerpo.childNodes.item(j).appendChild(parrafo);
			
			cuerpo.childNodes.item(j).childNodes.item(0).textContent = "[parrafo]";
			
			//if (depura) alert('bucle'+serializa_me(cuerpo));
		}
		
		//if (depura) alert('cuerpo:' + serializa_me(cuerpo));
		
	}	
	
	if (mascols> 0)
	{

		//'renombrar' (borrar y crear) de nuevo los nodos <fila> con el nuevo nº de columnas
		for (h=0; h<(hayfilas+masfilas); h++)
		{	
			var vueltas = origenXML.childNodes.item(0).childNodes.item(h).childNodes.length;
			
			//if (depura) alert(vueltas);
			
			fila = "fila" + (vueltas+mascols);
			newNode = temp.createElement(fila);
								
			//coge los hijos del antiguo y se los pone al nuevo
			for (m=0; m<vueltas; m++)
			{
				resto = origenXML.childNodes.item(0).childNodes.item(h).childNodes.item(0);			
				newNode.appendChild(resto);	
			}
		
			//if (depura) alert('newnode:' + serializa_me(newNode));
		
			oldChild = origenXML.childNodes.item(0).childNodes.item(h);
	
			origenXML.childNodes.item(0).replaceChild(newNode, oldChild);	
			
			//if (depura) alert('origen:' + serializa_me(origenXML));	
				
			var num;
			
			//if (depura) alert(mascols);
			
			//crear las nuevas columnas
			for (k=0; k<mascols; k++)
			{
				
				num = vueltas +1 + k;
				elemento= "elemento" + num;
				newNode = temp.createElement(elemento);
				newNode.setAttribute("texto", "");
				newNode.setAttribute("alin", "left");
				newNode.setAttribute("columnas", "1");
				newNode.setAttribute("filas", "1");
				newNode.setAttribute("clase", estilo);
				newNode.setAttribute("posicion", "top");
				cuerpo = origenXML.childNodes.item(0).childNodes.item(h);
				
			//	if (depura) alert('cuerpo:' + serializa_me(cuerpo));
				
				cuerpo.appendChild(newNode);
				parrafo = temp.createElement("parrafo");
				parrafo.setAttribute("clase", clase);
				parrafo.setAttribute("referencia", "");
				cuerpo.childNodes.item(num-1).appendChild(parrafo);
				
				cuerpo.childNodes.item(num-1).childNodes.item(0).textContent = "[parrafo]";	
				
			//	if (depura) alert('cuerpofinal:' + serializa_me(cuerpo));
			}
			
		
		}// cierre for h
		
	}//cierre if masfilas
	if (depura)
	{
		var serializer = new XMLSerializer();
		var xml = serializer.serializeToString(origenXML);
		alert('medio:     '+xml);

	}
	
	sumario = document.formulario.sumario.value;
	ancho = document.formulario.ancho.value;
	origenXML.setAttribute("sumario",sumario);
	origenXML.setAttribute("ancho",ancho);

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
		for(q=0; q < origenXML.childNodes.item(0).childNodes.item(0).childNodes.length; q++)
		{
				clasecaboriginal[q] = origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(q).getAttribute("clase");
		
		}
		
		corrigecabecera=true;
	}
	else
	{
		nuevon = hayfilas;
	}

	
	for (n=nuevon; n < origenXML.childNodes.item(0).childNodes.length; n++)
	{
		if (estilo == estiloF1) 
		{
			estilo = estiloF2;
		}
		else 
		{
			estilo = estiloF1;
		}
				
		for(p=0; p < origenXML.childNodes.item(0).childNodes.item(n).childNodes.length; p++)
		{
			
			
			origenXML.childNodes.item(0).childNodes.item(n).childNodes.item(p).setAttribute("clase",estilo);	
				
		}
				
	}
	
	if (depura)
	{
		var serializer = new XMLSerializer();
		var xml = serializer.serializeToString(origenXML);
		alert('re-estilado:     '+xml);
	}
	
	if (corrigecabecera)
	{
		for(q=0; q < origenXML.childNodes.item(0).childNodes.item(0).childNodes.length; q++)
		{
				origenXML.childNodes.item(0).childNodes.item(0).childNodes.item(q).setAttribute("clase",clasecaboriginal[q]);
		
		}
	}
	
	
	
	var serializer = new XMLSerializer();
		var obj = serializer.serializeToString(origenXML);
		var arr = new Array();
		arr["xml_content"] = obj;
		
	if (!depura)
	{
		//window.returnValue = arr;
		//window.close();
		
		newNode = origenXML.childNodes.item(0)
//		alert(serializa_me(newNode));
		oldChild = editnode.childNodes.item(0);
		editnode.replaceChild(newNode, oldChild);
		
		
		


		el_edx.parent.load();
		
		
		window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';
		
		
	}
	else
	{

		//alert(arr["xml_content"]);
		

		alert('alfinal:' + window.parent.e_global.el_obj);
		window.parent.campo_activo.value = arr["ID"];
		window.parent.document.getElementById('Vmodal').style.visibility = "hidden";
		window.parent.document.getElementById('toFirefox').value = "false";
		//window.parent.aplica_firefox(window.parent.objeto_global, "enlace")
		

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
<script src="../../../xmd/js/ximdex_common.js" type="text/javascript"></script>
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
td {
font-family: Verdana;
font-size:10px;
}
</style>
</head>
<body bgcolor="buttonFace" topmargin="3" leftmargin="5">
<table  class="tabla" width="100%" align="center" cellpadding="2">
	<tr>
		<td class="filacerrar" align="right"><a href="javascript:if(navegador == 'firefox15'){window.parent.toFirefox = true;window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';}" class="filacerrar">cerrar ventana <img src="../../../xmd/images/botones/cerrar.gif" alt="" border="0"></a></td>
	</tr>
</table>
<table width="100%" align="center" cellpadding="2">
<form name="formulario">
<tr>
	<td>Número actual de columnas:</td>
	<td width="60%"><input type="text" name="columnas" value="" disabled="true" size="3" class="cajap" /></td>
</tr>
<tr>
	<td>Número actual de filas:</td>
	<td width="60%"><input type="text" name="filas" value="" disabled="true" size="3" class="cajap" /></td>
</tr>
<tr>
	<td colspan="2"><hr /></td>
</tr>
</table>
<table class="PVtabla" width="100%">
<tr>
	<td  valign="top">Añadir&nbsp;</td> 
	<td><input type="text" name="masfilas" value="" size="3" class="cajap" />&nbsp;filas
	</td>
</tr>
<tr>
	<td  valign="top">Añadir&nbsp;</td> 
	<td><input type="text" name="mascolumnas" value="" size="3" class="cajap" />&nbsp;columnas
	</td>
</tr>
<tr>
	<td colspan="4"><hr /></td>
</tr>

			<tr>
				<td class="filacla"><strong>Ancho</strong></td>
				<td class="filacla"><input name="ancho" type="text" size="35" class="cajap" onChange="javascript:otroestilo=true;" /></td>
				<td class="filacla"><strong>Estilo general</strong></td>
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
			<tr>
				<td class="filacla"><strong>Sumario</strong></td>
				<td class="filacla" colspan="4"><input name="sumario" type="text" size="35" class="cajag" onChange="javascript:otroestilo=true;" /></td>
			</tr>
			<tr>
	<td colspan="4"><hr /></td>
</tr>
			
		</table>
		<table width="100%">
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
	<td colspan="4"><hr /></td>
</tr>
			<tr>
				<td align="center"><INPUT ID="cancelar" TYPE="button" CLASS="botong" VALUE="Cancelar" onClick="if(navegador == 'firefox15'){window.parent.toFirefox = true;window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';}"/></td>
				<td align="center"><input type="button" value="Modificar" onClick="repintar();" class="botong"></td>
</tr>
		</table>
		</form>
<script>

var editnode = window.parent.params.parent.getXmlNode();


origenXML = editnode;

function pintar()
{
	var cols2 = 0;
	var maxcols = 0;
	var cols;
	var nodotexto;

	//for para recorrer todas las filas
	for (i=0; i<origenXML.childNodes.item(0).childNodes.length; i++)
	{
		cols2 = 0;
	
		//for para recorrer todos los elementos de una fila
		for (j=0; j<origenXML.childNodes.item(0).childNodes.item(i).childNodes.length; j++)
		{
	
			cols = origenXML.childNodes.item(0).childNodes.item(i).childNodes.item(j).getAttribute("columnas");
			cols = parseInt(cols);
			cols2 += cols;
			
			//nodotexto = origenXML.childNodes.item(0).childNodes.item(i).childNodes.item(0).nodeValue;
	
		
		}
		
		if (cols2 > maxcols) maxcols = cols2;
	
	
	}

	clase = origenXML.getAttribute("clase");
	alin = origenXML.getAttribute("alin");
	sumario = origenXML.getAttribute("sumario");
	ancho = origenXML.getAttribute("ancho");
	salto = origenXML.getAttribute("salto");
	
	//alert(clase);
		
	document.formulario.clase.value = clase;
	//document.formulario.alin.value = alin;
	document.formulario.sumario.value = sumario;
	document.formulario.ancho.value = ancho;
	//document.formulario.salto.value = salto;
	
	document.formulario.columnas.value = maxcols;
	document.formulario.filas.value = origenXML.childNodes.item(0).childNodes.length;
}

			xmlDoc = origenXML;


pintar();


</script>

</body>
</html>
