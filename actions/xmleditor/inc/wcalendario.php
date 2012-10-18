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
<TITLE>ximCALENDAR</TITLE>
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
<script LANGUAGE="Javascript">


var depura = false;

var alternar = false;
var cambiodeanio = true;
var contenidotexto = 'parrafo';
var today    = new Date(); // formato por defecto del tipo <Fri Apr 15 17:23:34 UTC+0200 2005>
var dateNow  = today.getDate(); // numero de dia
var monthNow = today.getMonth(); // numero de mes
var yearNow  = today.getYear(); // anio
	
function pintaCalendario(mes, anio, num)
{	
	alert('Según el número de meses escogido el proceso puede tardar, espere hasta recibir respuesta.');
	//validar el numero de meses introducido

	if (wcalendario.nmeses.value=='')
	{
		var siono = confirm('El número de meses por defecto será 1');
		if (!siono) return false;

	}


	wcalendario.estilocabecera.disabled = true;

	if (num=='') num=1;
	var diasdelmes = new Array (31,0,31,30,31,30,31,31,30,31,30,31);
	var letradia = '';
	var nombredemes = '';
	var letrasdias = new Array ('L','M','X','J','V','S','D');
	var nombredemeses = new Array ('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	mes = mes -1;
	var fechacomienzo = new Date(anio, mes, '1');
	var diasemana = fechacomienzo.getDay();
	var mes = fechacomienzo.getMonth();
	var aniocomienzo = fechacomienzo.getYear();
	var cuentadiasemana = 0;
	
	if (diasemana == 0) diasemana= 7;
	var i = 0;
	var j = 0;
	var q = 0;
	var dm = 0;

	cuentadiasemana = diasemana;
	
	var num2 = mes + parseInt(num); 
	var nuevomes = mes;
	var qq=0;
	

	var ximT = new ActiveXObject("Msxml2.DOMDocument");
	ximT.async = false;
	ximT.resolveExternals = false; 
	var root;
	var newNode;
	var calendario = ximT.childNodes;
	
	ximT.appendChild(ximT.createElement("cuerpo-calendario"));


	//var Filas = wcalendario.filas.value;
	var Cols = 7;
	var colCab = Cols;
	estiloC = "filaclara";
	estiloF1 = "filaclara";
	estiloF2 = "filaclara";
	
// creo el parrafo general
		
	colCab = 1;
	estiloP = wcalendario.estiloparrafo.value;
	
	estiloC = wcalendario.estilocabecera.value;
	estiloF1 = wcalendario.estiloFimpar.value;
	estiloF2 = wcalendario.estiloFpar.value;
	estilonom = wcalendario.estilonombremes.value;
	
	var numtabla = 0; 
	
	
	// creacion de las tablas para los meses
	for (q=mes; q<num2; q++)
	{	
		if (q==mes) qq = q;

		//escribir el nombre del anio si el usuario lo ha pedido
		if (wcalendario.aniosiono[0].checked && cambiodeanio)
		{
			newNode = ximT.createNode(1,"subtitulo","");
			newNode.setAttribute("clase", wcalendario.estilotabla.value);
			calendario(0).appendChild(newNode);
			texto = ximT.createTextNode("Año " +aniocomienzo);
			aniocomienzo++;
			cambiodeanio = false;
			calendario(0).childNodes.item(calendario(0).childNodes.length-1).appendChild(texto);
		}
	
	
		// Creo la tabla
		numtabla = calendario(0).childNodes.length;   //0;//calendario.length-1;

		nombredemes = nombredemeses[nuevomes];
		
		newNode = ximT.createNode(1,"tabla","");
		newNode.setAttribute("clase", wcalendario.estilotabla.value);
		newNode.setAttribute("ancho", wcalendario.ancho.value);
		newNode.setAttribute("salto", "no");
		newNode.setAttribute("espacio", "0");
		newNode.setAttribute("alin", "center");
		newNode.setAttribute("sumario", nombredemes);
		
		calendario(calendario.length-1).appendChild(newNode);

		newNode = ximT.createNode(1,"cuerpo-tabla","");
		calendario(calendario.length-1).childNodes.item(numtabla).appendChild(newNode);


		if (nuevomes==11) { cambiodeanio=true;}
		
		//cabecera con el nombre del mes
		
		cuerpoT = calendario(calendario.length-1).childNodes(numtabla).childNodes(0);
		colCab = 1;
		fila = "fila" + colCab
		newNode = ximT.createNode(1, fila, "");

		cuerpoT.appendChild(newNode);
		cuerpocab = cuerpoT.childNodes;
		

		for (n = 0; n < colCab;	n ++)
		{
			elemento = "elemento" + (n + 1);
			newNode = ximT.createNode(1, elemento, "");
			newNode.setAttribute("texto", "");
			newNode.setAttribute("alin", "left");
			newNode.setAttribute("columnas", "7");
			newNode.setAttribute("filas", "1");
			newNode.setAttribute("clase", estilonom);
			newNode.setAttribute("posicion", "top");
			cuerpocab(0).appendChild(newNode);
			parrafo = ximT.createNode(1, contenidotexto, "");
			parrafo.setAttribute("clase", wcalendario.estiloparrafo.value);
			parrafo.setAttribute("referencia", "");
			cuerpocab(0).childNodes.item(n).appendChild(parrafo);
			nombredemes = nombredemeses[nuevomes];
			texto = ximT.createTextNode(nombredemes);
			cuerpocab(0).childNodes.item(n).childNodes.item(0).appendChild(texto);

		}
		
			
		//cabecera con los dias de la semana

		colCab = 7;
		fila = "fila" + colCab
		newNode = ximT.createNode(1, fila, "");

		cuerpoT.appendChild(newNode);
		cuerpocab = cuerpoT.childNodes;

		for (n = 0; n < colCab;	n ++)
		{
			elemento = "elemento" + (n + 1);
			newNode = ximT.createNode(1, elemento, "");
			newNode.setAttribute("texto", "");
			newNode.setAttribute("alin", "left");
			newNode.setAttribute("columnas", "1");
			newNode.setAttribute("filas", "1");
			newNode.setAttribute("clase", estiloC);
			newNode.setAttribute("posicion", "top");
			cuerpocab(1).appendChild(newNode);
			parrafo = ximT.createNode(1, contenidotexto, "");
			parrafo.setAttribute("clase", wcalendario.estiloparrafo.value);
			parrafo.setAttribute("referencia", "");
			cuerpocab(1).childNodes.item(n).appendChild(parrafo);
			
			letradia = letrasdias[n];
			texto = ximT.createTextNode(letradia);
			
			cuerpocab(1).childNodes.item(n).childNodes.item(0).appendChild(texto);
		
		}

		<!-- Filas--; -->
		estilo = estiloF1;
		nuevomes = nuevomes +1;
		if (nuevomes==12) nuevomes=0;
		diasemana = cuentadiasemana;
		
		//primera fila de numeros con celdas vacias

		fila = "fila" + Cols;
		newNode = ximT.createNode(1, fila, "");
		cuerpoT.appendChild(newNode);
		cuerpoF = cuerpoT.childNodes.item(2);

		var l=0;

		//indica el numero de celdas vacias segun el dia de la semana en que empieza el mes
		for (j=1; j < diasemana; j++)
		{
			elemento = "elemento" + (j);
			newNode = ximT.createNode(1, elemento, "");
			newNode.setAttribute("texto", "");
			newNode.setAttribute("alin", "left");
			newNode.setAttribute("columnas", "1");
			newNode.setAttribute("filas", "1");
			newNode.setAttribute("clase", estilo);
			newNode.setAttribute("posicion", "top");
			
			cuerpoF.appendChild(newNode);
				
			parrafo = ximT.createNode(1, contenidotexto, "");
			parrafo.setAttribute("clase", wcalendario.estiloparrafo.value);
			parrafo.setAttribute("referencia", "");
			cuerpoF.childNodes.item(j-1).appendChild(parrafo);

			texto = ximT.createTextNode("");
			cuerpoF.childNodes.item(j-1).childNodes.item(0).appendChild(texto);

			
		}
		

		if (qq==12) qq=0;

		dm = diasdelmes[qq] + 1;
		qq+=1;

		if (dm==1) 
		{
			endDate = new Date (anio,2,1);
			endDate = new Date (endDate - (24*60*60*1000));
			diasdefebrero= endDate.getDate();
			dm = diasdefebrero+1;
		}
		
		
		//pinta los numeros del mes en su celda correspondiente
		
		my_v = cuerpoT.childNodes.length-1;
		for(i = 1; i < dm;  i ++)
		{
			//alert('cuentadiasemana= ' + cuentadiasemana);	
	
			elemento = "elemento" + (cuentadiasemana);
			newNode = ximT.createNode(1, elemento, "");
			newNode.setAttribute("texto", "");
			newNode.setAttribute("alin", "left");
			newNode.setAttribute("columnas", "1");
			newNode.setAttribute("filas", "1");
			newNode.setAttribute("clase", estilo);
			newNode.setAttribute("posicion", "top");
			cuerpoF.appendChild(newNode);
		
			parrafo = ximT.createNode(1, contenidotexto, "");
			parrafo.setAttribute("clase", wcalendario.estiloparrafo.value);
			parrafo.setAttribute("referencia", "");
			cuerpoF.childNodes.item(cuentadiasemana - 1).appendChild(parrafo);
			texto = ximT.createTextNode(i);  
			cuerpoF.childNodes.item(cuentadiasemana - 1).childNodes.item(0).appendChild(texto);
			cuentadiasemana = cuentadiasemana +1;
			
			
			//cierre de la fila
			if (cuentadiasemana == 8)
			{
				
				if (alternar)
				{
					if (estilo == estiloF1) estilo = estiloF2;
						else estilo = estiloF1;
				}

				
				cuentadiasemana = 1;
				
				fila = "fila" + Cols;
				newNode = ximT.createNode(1, fila, "");
				cuerpoT.appendChild(newNode);
				my_v++;
				cuerpoF = cuerpoT.childNodes.item(my_v);

			}

			
		}// cierre for i
		
		copiadecuentadiasemana = cuentadiasemana
		var filasresto = 8 - copiadecuentadiasemana;
		for (j=0; j < filasresto; j++)
		{

			elemento = "elemento" + (copiadecuentadiasemana);
			newNode = ximT.createNode(1, elemento, "");
			newNode.setAttribute("texto", "");
			newNode.setAttribute("alin", "left");
			newNode.setAttribute("columnas", "1");
			newNode.setAttribute("filas", "1");
			newNode.setAttribute("clase", estilo);
			newNode.setAttribute("posicion", "top");
			cuerpoF.appendChild(newNode);
			
			parrafo = ximT.createNode(1, contenidotexto, "");
			parrafo.setAttribute("clase", wcalendario.estiloparrafo.value);
			parrafo.setAttribute("referencia", "");
			cuerpoF.childNodes.item(copiadecuentadiasemana - 1).appendChild(parrafo);
			
			texto = ximT.createTextNode("");
			cuerpoF.childNodes.item(copiadecuentadiasemana - 1).childNodes.item(0).appendChild(texto);

			copiadecuentadiasemana = copiadecuentadiasemana +1;
		}
		
		
		
		numtabla++;
			
	}// cierre for q	
	
	if (!depura){
		//creo un nuevo objeto "calendario" y le introduzco ximT
		var ximDoc = new ActiveXObject("Msxml2.DOMDocument");
		ximDoc.async = false;
		ximDoc.resolveExternals = false; 
		
		ximDoc.appendChild(ximDoc.createElement("calendario"));
		ximDoc.childNodes.item(0).appendChild(ximT.childNodes(0));
		
		var arr = new Array();
		arr["xml_content"] = ximDoc.xml;
		window.returnValue = arr;
		window.close();
		}
	else {
		
		
		var ximDoc = new ActiveXObject("Msxml2.DOMDocument");
		ximDoc.async = false;
		ximDoc.resolveExternals = false; 
		
		ximDoc.appendChild(ximDoc.createElement("calendario"));
		ximDoc.childNodes.item(0).appendChild(ximT.childNodes(0));
		document.getElementById('resulxml').value = ximDoc.xml
		return;
	}
					

}// cierre function pintacalendario


function cambiafila(objeto){
			if (objeto.checked){
				wcalendario.estiloFimpar.disabled = false;
				wcalendario.estiloFpar.disabled = false;
				alternar = true;
			}
			else{
				wcalendario.estiloFimpar.disabled = true;
				wcalendario.estiloFpar.disabled = true;
				alternar = false;
			}
		}

function depuracion(){
	
	
	if(depura){
		
		document.getElementById('nmeses').value = "2"
		document.getElementById('capaxml').style.visibility = "visible"
		
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
<body bgcolor="#e0dfe3" topmargin="3" leftmargin="5" onload="depuracion();">
<form name="wcalendario">
<table border="0" cellpadding="3" cellspacing="3" style="left-margin: 5px;">
	<tr><td colspan="4" align="left"><table cellpadding="3" cellspacing="3">
	<tr>
		<td colspan="4"><strong>Seleccionar fecha de comienzo</strong></td>
	</tr>
	<tr>
		<td>A&#241;o</td>
		<td><select class="caja" style="width: 75px;" name="aniocomienzo">
		<script>

		if (yearNow=='2004') document.write('<option value="2004" selected>2004</option>'); else document.write('<option value="2004">2004</option>');
if (yearNow=='2005') document.write('<option value="2005" selected>2005</option>'); else document.write('<option value="2005">2005</option>');
if (yearNow=='2006') document.write('<option value="2005" selected>2006</option>'); else document.write('<option value="2006">2006</option>');
if (yearNow=='2007') document.write('<option value="2005" selected>2007</option>'); else document.write('<option value="2007">2007</option>');
if (yearNow=='2008') document.write('<option value="2005" selected>2008</option>'); else document.write('<option value="2008">2008</option>');
if (yearNow=='2009') document.write('<option value="2005" selected>2009</option>'); else document.write('<option value="2009">2009</option>');
if (yearNow=='2010') document.write('<option value="2005" selected>2010</option>'); else document.write('<option value="2010">2010</option>')
if (yearNow=='2011') document.write('<option value="2005" selected>2011</option>'); else document.write('<option value="2011">2011</option>')
if (yearNow=='2012') document.write('<option value="2005" selected>2012</option>'); else document.write('<option value="2012">2012</option>')

</script>
			</select>
		</td>
		<td>Mes</td>
		<td><select class="caja" style="width: 75px;" name="mescomienzo">
		<script>
		if (monthNow=='0') document.write('<option value="1" selected>Enero</option>'); else document.write('<option value="1">Enero</option>');
if (monthNow=='1') document.write('<option value="2" selected>Febrero</option>'); else document.write('<option value="2">Febrero</option>');
if (monthNow=='2') document.write('<option value="3" selected>Marzo</option>'); else document.write('<option value="3">Marzo</option>');
if (monthNow=='3') document.write('<option value="4" selected>Abril</option>'); else document.write('<option value="4">Abril</option>');
if (monthNow=='4') document.write('<option value="5" selected>Mayo</option>'); else document.write('<option value="5">Mayo</option>');
if (monthNow=='5') document.write('<option value="6" selected>Junio</option>'); else document.write('<option value="6">Junio</option>');
if (monthNow=='6') document.write('<option value="7" selected>Julio</option>'); else document.write('<option value="7">Julio</option>');
if (monthNow=='7') document.write('<option value="8" selected>Agosto</option>'); else document.write('<option value="8">Agosto</option>');
if (monthNow=='8') document.write('<option value="9" selected>Septiembre</option>'); else document.write('<option value="9">Septiembre</option>');
if (monthNow=='9') document.write('<option value="10" selected>Octubre</option>'); else document.write('<option value="10">Octubre</option>');
if (monthNow=='10') document.write('<option value="11" selected>Noviembre</option>'); else document.write('<option value="11">Novimbre</option>');
if (monthNow=='11') document.write('<option value="12" selected>Diciembre</option>'); else document.write('<option value="12">Diciembre</option>');
						
		</script>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top">N&#250;mero de meses</td>
		<td valign="top"><input name="nmeses" type="text" size="10"> </input>
		<input type="hidden" class="cajaxp" name="columnas" value="7"></td>
		<td valign="top">Escribir el a&#241;o</td>
		<td valign="top"><input type="radio" value="si" name="aniosiono" checked>S&#237;<br />
		<input type="radio" value="no" name="aniosiono" >No</td>
	</tr>
	</table></td></tr>
	<tr>
		<td colspan="4"><strong>Propiedades de la tabla</strong></td>
	</tr>
	<tr>
		<td>Estilo de la tabla</td>
		<td><select class="caja" style="width: 180px;" name="estilotabla">
				  		<option value="tabla">Tabla con borde</option>
						<option value="tablasinborde">Tabla sin borde</option>
						<option value="tablasinbordeprincipal">Tabla sin borde (letra principal)</option>
						<option value="tablablanca">Tabla sin borde y fondo blanco</option>
					</select></td>
	
		<td colspan="2" align="left">Ancho&nbsp;<input type="Text" class="cajaxp" name="ancho" value="50%" size="4"></td>
	</tr>
	<tr>
		<td>Estilo nombre del mes</td>
		<td colspan="3" align="left"><select  class="caja" style="width: 180px;" name="estilonombremes">
				  		<option value="normal">Normal</option>
						<option value="normalgris">Normal gris</option>
						<option value="normalprincipal">Normal principal</option>
						<option value="normalprincipalnegrita">Normal principal negrita</option>
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
		<td>Estilo d&#237;as de la semana</td>
		<td colspan="3" align="left"><select  class="caja" style="width: 180px;" name="estilocabecera">
				  		<option value="normal">Normal</option>
						<option value="normalgris">Normal gris</option>
						<option value="normalprincipal">Normal principal</option>
						<option value="normalprincipalnegrita">Normal principal negrita</option>
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
		<td colspan="4"><input type="checkbox" value="" name="crearalt" onclick="cambiafila(this);">&nbsp;Alternar estilo de filas &raquo;</td>
	</tr>
	<tr>
		<td>Fila impar</td>
		<td><select  class="caja" style="width: 180px;" name="estiloFimpar" disabled>
				  		<option value="normal">Normal</option>
						<option value="normalgris">Normal gris</option>
						<option value="normalprincipal">Normal principal</option>
						<option value="normalprincipalnegrita">Normal principal negrita</option>
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
		<td nowrap>Fila par</td>
		<td align="left"><select  class="caja" style="width: 180px;" name="estiloFpar" disabled>
				  		<option value="normal">Normal</option>
						<option value="normalgris">Normal gris</option>
						<option value="normalprincipal">Normal principal</option>
						<option value="normalprincipalnegrita">Normal principal negrita</option>
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
		<td>Contenido: Estilo general</td>
		<td colspan="3"><select class="caja" style="width: 180px;" name="estiloparrafo">
				<option value="Tnormal" selected="selected">Normal</option>
				<option value="Tcursiva">Cursiva</option>
				<option value="Tenlacenegrita">Negrita</option>
				<option value="Tenlacenegritasubrayado">Negrita y subrayado</option>
				<option value="Tenlacenegritasubrayadocursiva">Negrita, subrayado y cursiva</option>
			</select></td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td align="center"><INPUT ID="cancelar" TYPE="button" CLASS="but" VALUE="Cancelar" onClick="cancelar();"/></td>
		<td align="center"><INPUT ID="cancelar" TYPE="button" CLASS="but" VALUE="Limpiar" onClick="wcalendario.reset();"/></td>
<td colspan="2" align="center"><INPUT ID="aceptar" TYPE="button" CLASS="but" VALUE="Crear Calendario" onClick="javascript:pintaCalendario(document.forms[0].mescomienzo.value,document.forms[0].aniocomienzo.value, document.forms[0].nmeses.value );" /></td>
	</tr>
</table>
<div id="capaxml" style="visibility: hidden;">
	<textarea name="resulxml" style="font-family: verdana;font-size: 10px;width: 550px;height: 350px;border-color : #8e8e8e;border-width : 1px;	border-style : solid;background-color : #e6e6e6;"></textarea>
</div>
</form>
</body>
</html>
