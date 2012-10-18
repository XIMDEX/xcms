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
<!-- 
Editor de formularios. ximDEX 2.5e
-->
<html>
<head>
<?php

 

$ximDEX_path = realpath(dirname(__FILE__) .  "/../../../");


include_once( $ximDEX_path . "/inc/utils.inc" );

XSession::check();

?>
	<title>editor formularios</title>
<script language="Javascript" for="window" event="onload">
  for ( elem in window.dialogArguments )
  {
    switch( elem )
    {
    case "url":
		la_url = window.dialogArguments["url"];
		ejecuta_lectura(la_url);
		break;
   	}
  }
 
  	ejecuta_lectura('servicio-prueba2.xml');
 
</script>
<script type="text/javascript">
function ejecuta_lectura(archivo){
	var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
	xmlDoc.async = true;
	xmlDoc.resolveExternals = true;
	
	xmlDoc.load(archivo);
	inserta_servicio(xmlDoc);
}
var servicios;
var servicios_o;
var l_servicios;
function inserta_servicio(xmlDoc){
	debug = 0;
	if( xmlDoc == null || xmlDoc.documentElement == null)
		alert('error al leer el archivo:');
	else
		{
		
		root = xmlDoc.documentElement;
		cs = root.childNodes;
		formulario.nombre_servicio.value = cs[0].childNodes(0).nodeValue;
		formulario.nombre_manejador.value = cs[1].childNodes(0).nodeValue;
		formulario.resultados_dato_pagina.value = cs[2].childNodes.item(0).childNodes(0).nodeValue;
		formulario.resultados_identificador.value = cs[2].childNodes.item(1).childNodes(0).nodeValue;
		formulario.resultados_pagina.value = cs[2].childNodes.item(2).childNodes(0).nodeValue;
		formulario.servicio_detalle_variable.value = cs[3].childNodes(0).nodeValue;
		
		// bucle que recorre los elementos de un servicio
		servicios = cs[4].childNodes
		servicios_o = servicios;
		// fin bucle que recorre los elementos de un servicio
		}
}

function cambia_tipo(objeto){
	limpia_combo(elform.elselect);
	tipo = objeto.value;
	l_servicios = servicios.length;
	for (n=0; n < l_servicios; n++){
		if (servicios.item(n).getAttribute(tipo) == "si"){
			servicio = servicios.item(n);
			nueva_opcion = new Option();
			nueva_opcion.value = servicio.childNodes(0).childNodes(0).nodeValue;
			nueva_opcion.text = servicio.childNodes(0).childNodes(0).nodeValue;
			elform.elselect.add(nueva_opcion);
		}
	}
}

function pasa_valores(objeto){
	pos_seleccionado = -1;
		for (i=0;i<=objeto.length-1;i++){
			if (objeto.options[i].selected)
					pos_seleccionado = i;
			}
		if (pos_seleccionado == -1){
			alert("debe seleccionar un valor");
			return;
		}
	modifica_valores.campo.value = objeto.options[pos_seleccionado].text;
	modifica_valores.nombre_campo.value = objeto.options[pos_seleccionado].text;
	for (n=0; n < l_servicios; n++){
		servicio = servicios.item(n).childNodes;
		if (servicio[0].childNodes(0).nodeValue == objeto.options[pos_seleccionado].text){
			if (servicios.item(n).getAttribute("entrada") == "si"){
				limpia_combo(modifica_valores.validacion);
				limpia_combo(modifica_valores.tipo_entrada);
				modifica_valores.error_servidor.value = servicios.item(n).getAttribute("errorservidor");
				modifica_valores.requerido.value = servicios.item(n).getAttribute("requerido");
				modifica_valores.tipo_entrada.disabled = false;
				modifica_valores.estilo_campo.disabled = false;
				modifica_valores.validacion.disabled = false;
				modifica_valores.error_servidor.disabled = true;
				modifica_valores.requerido.disabled = true;
				for (i = 0; i<servicio.length; i++){
					switch (servicio[i].tagName)
						{
						case "elemento_validaciones":
							for (l=0; l < servicio[i].childNodes.length; l++){
								
								nueva_opcion = new Option();
								nueva_opcion.value = servicio[i].childNodes.item(l).childNodes(0).nodeValue;
								nueva_opcion.text = servicio[i].childNodes.item(l).childNodes(0).nodeValue;
								modifica_valores.validacion.add(nueva_opcion);
							}
							break;
						case "elemento_tipo_entradas":
							for (l=0; l < servicio[i].childNodes.length; l++){
								
								nueva_opcion = new Option();
								nueva_opcion.value = servicio[i].childNodes.item(l).childNodes(0).nodeValue;
								nueva_opcion.text = servicio[i].childNodes.item(l).childNodes(0).nodeValue;
								modifica_valores.tipo_entrada.add(nueva_opcion);
							}
							break;
						case "elemento_dinamico":
								modifica_valores.dinamico_variable.value = servicio[i].childNodes.item(0).childNodes(0).nodeValue;
							break;
						}
					}
				return;
			}
			else{
				modifica_valores.tipo_entrada.disabled = true;
				modifica_valores.estilo_campo.disabled = true;
				modifica_valores.validacion.disabled = true;
				modifica_valores.error_servidor.disabled = true;
				modifica_valores.requerido.disabled = true;
				return;
			}
		}
	}
}

function limpia_combo(objeto){
	l = objeto.length
		for(n=0; n < l; n++){
			objeto.options[0] = null;
		}
}

function inserta_valores(){
	if (!modifica_valores.campo.value){
		alert('debe seleccionar un valor');
		return;
		}
	else{
		for (n = 0; n < form_final.campo.length; n++){
			if (modifica_valores.campo.value == form_final.campo.options[n].value){
				alert("El campo [ " + modifica_valores.campo.value + " ] ya ha sido insertado en el formulario");
				return;
				}
		}
		nueva_opcion = new Option();
		nueva_opcion.value = modifica_valores.campo.value;
		nueva_opcion.text = modifica_valores.nombre_campo.value;
		form_final.campo.add(nueva_opcion);
		
		nueva_opcion = new Option();
		nueva_opcion.value = modifica_valores.estilo_campo.value;
		nueva_opcion.text = modifica_valores.estilo_campo.options[modifica_valores.estilo_campo.selectedIndex].text;
		form_final.estilo.add(nueva_opcion);
		
		nueva_opcion = new Option();
		nueva_opcion.value = modifica_valores.tipo_entrada.value;
		nueva_opcion.text = modifica_valores.tipo_entrada.value;
		form_final.tipo.add(nueva_opcion);
		
		nueva_opcion = new Option();
		nueva_opcion.value = modifica_valores.validacion.value;
		nueva_opcion.text = modifica_valores.validacion.value;
		form_final.validacion.add(nueva_opcion);
		
		nueva_opcion = new Option();
		nueva_opcion.value = modifica_valores.error_servidor.value;
		nueva_opcion.text = modifica_valores.error_servidor.value;
		form_final.error.add(nueva_opcion);
		
		nueva_opcion = new Option();
		nueva_opcion.value = modifica_valores.dinamico_variable.value;
		nueva_opcion.text = modifica_valores.requerido.value;
		form_final.requerido.add(nueva_opcion);
		
		modifica_valores.campo.value = "";
		modifica_valores.nombre_campo.value = "";
		if (modifica_valores.dinamico_variable) modifica_valores.dinamico_variable.value = "";
		limpia_combo(modifica_valores.tipo_entrada);
		limpia_combo(modifica_valores.validacion);
		
		formulario.tipo_formulario.disabled = true;
	}
}

function posiciona_elementos(el_objeto){

for (i=0;i<=el_objeto.length-1;i++){
	if (el_objeto.options[i].selected){
		ultima_posicion = i;
		form_final.campo.options[i].selected = true;
		form_final.tipo.options[i].selected = true;
		form_final.estilo.options[i].selected = true;
		form_final.validacion.options[i].selected = true;
		form_final.error.options[i].selected = true;
		form_final.requerido.options[i].selected = true;
	}
}
}

function edita_valor(objeto){
pos_seleccionado = -1;
	for (i=0;i<=objeto.length-1;i++){
			if (objeto.options[i].selected)
			pos_seleccionado = i;
			}
		if (pos_seleccionado == -1){
		alert("debe seleccionar un valor");
		return;
	}
	modifica_valores.campo.value = form_final.campo.options[ultima_posicion].value;
	modifica_valores.nombre_campo.value = form_final.campo.options[ultima_posicion].text;
	nueva_opcion = new Option();
	nueva_opcion.value = form_final.tipo.options[ultima_posicion].value;
	nueva_opcion.text = form_final.tipo.options[ultima_posicion].value;
	modifica_valores.tipo_entrada.add(nueva_opcion);
	modifica_valores.estilo_campo.value = form_final.estilo.options[ultima_posicion].value;
	nueva_opcion = new Option();
	nueva_opcion.value = form_final.validacion.options[ultima_posicion].value;
	nueva_opcion.text = form_final.tipo.options[ultima_posicion].value;
	modifica_valores.validacion.add(nueva_opcion);
	modifica_valores.error_servidor.value = form_final.error.options[ultima_posicion].value;
	modifica_valores.requerido.value = form_final.requerido.options[ultima_posicion].text;
	modifica_valores.dinamico_variable.value = form_final.requerido.options[ultima_posicion].value;
	form_final.campo.options[ultima_posicion] = null;
	form_final.tipo.options[ultima_posicion] = null;
	form_final.estilo.options[ultima_posicion] = null;
	form_final.validacion.options[ultima_posicion] = null;
	form_final.error.options[ultima_posicion] = null;
	form_final.requerido.options[ultima_posicion] = null;
}
function borra_valor(){
	form_final.campo.options[ultima_posicion] = null;
	form_final.tipo.options[ultima_posicion] = null;
	form_final.estilo.options[ultima_posicion] = null;
	form_final.validacion.options[ultima_posicion] = null;
	form_final.error.options[ultima_posicion] = null;
	form_final.requerido.options[ultima_posicion] = null;
}
function pilla_valor(modo){
	pos_seleccionado = -1;
	objeto = form_final.campo;
		for (i=0;i<=objeto.length-1;i++){
			if (objeto.options[i].selected)
					pos_seleccionado = i;
			}
		if (pos_seleccionado == -1){
			alert("debe seleccionar un valor");
			return;
		}
		else if (pos_seleccionado == 0 && modo=="sube"){
				alert("El objeto es el primero de la lista");
				return;
			}
		else if (pos_seleccionado == objeto.length-1 && modo=="baja"){
				alert("El objeto es el último de la lista");
				return;
			}
		else{
			if (modo=="sube") {
				moveup(form_final.campo, pos_seleccionado);
				moveup(form_final.tipo, pos_seleccionado);
				moveup(form_final.estilo, pos_seleccionado);
				moveup(form_final.validacion, pos_seleccionado);
				moveup(form_final.error, pos_seleccionado);
				moveup(form_final.requerido, pos_seleccionado);
				}
			if (modo=="baja"){
				moveup(form_final.campo, pos_seleccionado);
				moveup(form_final.tipo, pos_seleccionado);
				moveup(form_final.estilo, pos_seleccionado);
				moveup(form_final.validacion, pos_seleccionado);
				moveup(form_final.error, pos_seleccionado);
				moveup(form_final.requerido, pos_seleccionado);
				}
			}
		
}

function moveup(objeto, posicion){
		op_seleccionado = new Option();
		op_seleccionado.text = objeto[posicion].text;
		op_seleccionado.value = objeto[posicion].value;
		
		objeto[posicion].text = objeto[posicion-1].text;
		objeto[posicion].value = objeto[posicion-1].value;
		
		objeto[posicion-1].text = op_seleccionado.text;
		objeto[posicion-1].value = op_seleccionado.value;
		
		objeto[posicion-1].selected = true;
		}
	
	function movedown(objeto, posicion){
		op_seleccionado = new Option();
		op_seleccionado.text = objeto[posicion].text;
		op_seleccionado.value = objeto[posicion].value;
		
		objeto[posicion].text = objeto[posicion+1].text;
		objeto[posicion].value = objeto[posicion+1].value;
		
		objeto[posicion+1].text = op_seleccionado.text;
		objeto[posicion+1].value = op_seleccionado.value;
		
		objeto[posicion+1].selected = true;
		
	}
	function limpia_formfinal(){
	if (confirm ("A continuación se borrar&aacute;n todos los campos incluidos en el formulario \n &iquest;desea continuar?")){
		limpia_combo(form_final.campo);
		limpia_combo(form_final.tipo);
		limpia_combo(form_final.estilo);
		limpia_combo(form_final.validacion);
		limpia_combo(form_final.error);
		limpia_combo(form_final.requerido);
		}
	}
	
function convierte_formfinal(){
	var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
	xmlDoc.async = true;
	xmlDoc.resolveExternals = true;
	
	xmlDoc.appendChild(xmlDoc.createElement("cuerpo-formulario"));
	root = xmlDoc.documentElement;
	//nombre del servicio
	newNode = xmlDoc.createNode(1, "servicio_nombre", "");
	root.appendChild(newNode);
	MyText = xmlDoc.createTextNode(formulario.nombre_servicio.value);
	root.childNodes.item(0).appendChild(MyText);
	//
	newNode = xmlDoc.createNode(1, "servicio_manejador", "");
	root.appendChild(newNode);
	MyText = xmlDoc.createTextNode(formulario.nombre_manejador.value);
	root.childNodes.item(1).appendChild(MyText);
	//
	newNode = xmlDoc.createNode(1, "servicio_resultados", "");
	root.appendChild(newNode);
	
	newNode = xmlDoc.createNode(1, "resultados_dato_enlace", "");
	root.childNodes(2).appendChild(newNode);
	MyText = xmlDoc.createTextNode(formulario.resultados_dato_pagina.value);
	root.childNodes(2).childNodes.item(0).appendChild(MyText);
	
	newNode = xmlDoc.createNode(1, "resultados_identificador", "");
	root.childNodes(2).appendChild(newNode);
	MyText = xmlDoc.createTextNode(formulario.resultados_identificador.value);
	root.childNodes(2).childNodes.item(1).appendChild(MyText);
	
	newNode = xmlDoc.createNode(1, "resultados_pagina", "");
	root.childNodes(2).appendChild(newNode);
	MyText = xmlDoc.createTextNode(formulario.resultados_pagina.value);
	root.childNodes(2).childNodes.item(2).appendChild(MyText);
	
	newNode = xmlDoc.createNode(1, "servicio_detalle_variable", "");
	root.appendChild(newNode);
	MyText = xmlDoc.createTextNode(formulario.servicio_detalle_variable.value);
	root.childNodes.item(3).appendChild(MyText);
	
	newNode = xmlDoc.createNode(1, "tipo_formulario", "");
	root.appendChild(newNode);
	MyText = xmlDoc.createTextNode(formulario.tipo_formulario.value);
	root.childNodes.item(4).appendChild(MyText);
	
	newNode = xmlDoc.createNode(1, "elementos", "");
	root.appendChild(newNode);
		
	newNode = xmlDoc.createNode(1, "cuerpo_elementos", "");
	root.childNodes(5).appendChild(newNode);
	
	cuerpo = root.childNodes(5).childNodes;
	for (n = 0; n < form_final.campo.length; n++){
		newNode = xmlDoc.createNode(1, "elemento", "");
		newNode.setAttribute("nombre_campo", form_final.campo[n].value);
		newNode.setAttribute("etiqueta", form_final.campo[n].text);
		newNode.setAttribute("tipo", form_final.tipo[n].value);
		newNode.setAttribute("validacion", form_final.validacion[n].value);
		newNode.setAttribute("error", form_final.error[n].value);
		newNode.setAttribute("validacion", form_final.validacion[n].value);
		newNode.setAttribute("requerido", form_final.requerido[n].text);
		newNode.setAttribute("elemento_dinamico", form_final.requerido[n].value);
		cuerpo[0].appendChild(newNode);
		
	}
	
	//bot.final.value = xmlDoc.xml;
}
</script>
<link rel="STYLESHEET" type="text/css" href="../style/ximdex.css"/>
</head>

<body leftmargin="0" rightmargin="0" bottommargin="0" topmargin="0" style="overflow: hidden;">
<table border="0" width="100%" cellpadding="1" cellspacing="0" align="center">
		<tr>
			<td height="15" valign="bottom">
			<div class="tituloseccion">&nbsp;Editor de formularios</div>
			</td>
		</tr>
		<tr>
			<td>
			<table class="actionbar">
				<tr>
					<td class="normal"><b>Descripci&oacute;n:</b>
					Edita y crea formularios en ximDEX</td>
				</tr>				
			</table>
			</td>
		</tr>
		<tr>
			<td><br>
<form name="formulario">
<table class="tabla" cellpadding="0" cellspacing="1" align="center">
	<tr>
		<td colspan="2" class="cabeceratabla">Editor de formularios</td>
	</tr>
	<tr>
		<td class="tablamedia" nowrap>&nbsp;Nombre del servicio:</td>
		<td class="tablamedia"><input type="Text" name="nombre_servicio" class="tablamedia" style="width: 400px; font-weight: normal;"/></td>
	</tr>
	<tr>
		<td class="tablamedia" nowrap>&nbsp;Nombre del manejador:</td>
		<td class="tablamedia"><input type="Text" name="nombre_manejador" class="tablamedia" style="width: 400px; font-weight: normal;"/></td>
	</tr>
	<tr>
		<td class="filaoscuranegrita">Resultados dato página</td>
		<td><input type="text" name="resultados_dato_pagina" class="cajaxg" disabled="true"/></td>
	</tr>
	<tr>
		<td class="filaoscuranegrita">Identificador resultados</td>
		<td><input type="text" name="resultados_identificador" class="cajaxg" disabled="true"/></td>
	</tr>
	<tr>
		<td class="filaoscuranegrita">Nº resultados por página</td>
		<td class="filaclara"><input type="text" name="resultados_pagina" class="cajap" disabled="true"/></td>
	</tr>
	<tr>
		<td class="filaoscuranegrita">Servicio detalle variable</td>
		<td><input type="text" name="servicio_detalle_variable" class="cajaxg" disabled="true"/></td>
	</tr>
	<tr>
		<td class="tablamedia">Tipo de formulario a realizar:</td>
		<td class="tablamedia"><select name="tipo_formulario" class="caja" onchange="cambia_tipo(this);">
			<option value="">&raquo; seleccione tipo &laquo;</option>
			<option value="entrada">Búsqueda</option>
			<option value="lista">Listado</option>
			<option value="detalle">Detalle</option>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</form>
	</tr>
	<tr>
		<td class="filaclarac" valign="top" align="center" colspan="2">
			<table class="tablam" cellpadding="0" cellspacing="0" width="200" align="left">
				<tr>
					<td align="center" class="filaoscura" colspan="2"><strong>Campos origen</strong></td>
				</tr>
				<tr>
					<td>
					<form name="elform">
						<select name="elselect" class="cajag" size="10" ondblclick="pasa_valores(this);"></select>
					</form>
					</td>
					<td class="filaclara"><a href="#" onclick="pasa_valores(elform.elselect);"><img src="../images/utils-editor/right.gif" alt="" width="18" height="17" border="0"></a></td>
				</tr>
			</table>
		
			<table class="tablam" cellpadding="0" cellspacing="0" width="260" align="left">
				<form name="modifica_valores">
				<tr>
					<td align="center" class="filaoscura" colspan="2"><strong>Edición de Campo</strong></td>
				</tr>
				<tr>
					<td class="filaclara"><strong>Campo</strong>:</td>
					<td class="filaclara"><input type="Text" class="cajag" name="campo" disabled="true"></td>
				</tr>
				<tr>
					<td class="filaclara"><strong>Nombre</strong>:</td>
					<td class="filaclara"><input type="Text" class="cajag" name="nombre_campo"></td>
				</tr>
				<tr>
					<td class="filaclara" valign="top"><strong>Tipo entrada</strong>:</td>
					<td class="filaclara">
					<select name="tipo_entrada" class="cajag">
						
					</select></td>
				</tr>
				<tr>
					<td class="filaclara" valign="top"><strong>Estilo</strong>:</td>
					<td class="filaclara">
					<select name="estilo_campo" class="cajam">
						<option value="cajap">Caja pequeña (50 px)
						<option value="caja" selected>Caja (100 px)
						<option value="cajag">Caja grande (200 px)
					</select></td>
				</tr>
				<tr>
					<td class="filaclara" valign="top"><strong>Validación</strong>:</td>
					<td class="filaclara">
					<select name="validacion" class="cajam">
					</select></td>
				</tr>
				<tr>
					<td class="filaclara" valign="top" nowrap><strong>Error servidor</strong>:</td>
					<td class="filaclara">
					<select name="error_servidor" class="cajap">
						<option value="si">si</option>
						<option value="no">no</option>
					</select></td>
				</tr>
				<tr>
					<td class="filaclara" valign="top"><strong>Requerido</strong>:</td>
					<td class="filaclara">
					<select name="requerido" class="cajap">
						<option value="si">si</option>
						<option value="no">no</option>
					</select></td>
				</tr>
				<tr>
					<td align="center" colspan="2" class="filaclara">
						<a href="#" onclick="inserta_valores();"><img src="../images/utils-editor/down.gif" alt="" width="18" height="17" border="0"></a>&nbsp;&nbsp;
						<a href="#" onclick="edita_valores();"><img src="../images/utils-editor/up.gif" alt="" width="18" height="17" border="0"></a>
				</td>
				</tr>
				<input type="hidden" name="dinamico_variable" value="">
				</form>
			</table>
		</td>	
	</tr>
	<tr>
		<td colspan="2">
			<table border="0" class="tablam">
				<tr class="filaoscuranegritac">
					<td>Campo</td>
					<td>Estilo</td>
					<td>Tipo entrada</td>
					<td>Validacion</td>
					<td>Error</td>
					<td>Requerido</td>
				</tr>
				<tr><form name="form_final">
					<td><select name="campo" class="caja" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td><select name="estilo" class="caja" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td><select name="tipo" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td><select name="validacion" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td><select name="error" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td><select name="requerido" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td>
					<a href="#" onclick="borra_valor();"><img src="../images/utils-editor/borrar.gif" width="18" height="17" alt="" border="0"></a><br>
					<a href="#" onclick="pilla_valor('sube');"><img src="../images/utils-editor/up.gif" alt="" width="18" height="17" border="0"></a><br>
					<a href="#" onclick="pilla_valor('baja');"><img src="../images/utils-editor/down.gif" alt="" width="18" height="17" border="0"></a>
				</td>
					</form>
				</tr>
			</table>
		
		</td>
	</tr>
	<tr>
		<td align="center"><a href="#" onclick="limpia_formfinal();" class="botong">Restaurar</a></td>
		<td align="center"><a href="#" onclick="convierte_formfinal();" class="botong"><img src="../images/utils-editor/guardar_y_salir.gif" width="96" height="12" alt="" border="0"></td>
	</tr>
</table>
</body>
</html>
