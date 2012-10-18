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




 

ModulesManager::file('/inc/utils.inc');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- 
Form editor. ximDEX 2.5e
-->
<html>
<head>
<?php
XSession::check();

$nodeID			= $_GET["nodeid"];
$node = new Node($nodeID);
		$fileNode = new Node($nodeID);
		$fileName = $fileNode -> GetNodeName();
		$content = $fileNode->class->GetContent();
		$content = htmlspecialchars($content);
?>
	<title>editor formularios</title>
<script language="Javascript" for="window" event="onload">
var eldoc;
for ( elem in window.dialogArguments )
  {
    switch( elem )
    {
    
	case "elnodo":
		elnodo = window.dialogArguments["elnodo"];
		var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
		xmlDoc.async = true;
		xmlDoc.resolveExternals = true;
		xmlDoc.loadXML(bot.contenido.value);
//
		inserta_servicio(xmlDoc);
		break;
	case "xml_content":
		
		eldoc = window.dialogArguments["xml_content"];
		xmlSer.async = true;
		xmlSer.resolveExternals = true;
		xmlSer.loadXML(eldoc);
		coloca_serv();
		break;
   	}

}
 
	
</script>
<script src="ximFORM/ximform.js" type="text/javascript"></script>
<script type="text/javascript">


var xmlSer = new ActiveXObject("Msxml2.DOMDocument");
var elnodo;
var eldoc;
var num_nodo;
var Gtipo;
var ultimo_servicio;

var posiciones = new Array();
</script>
<link rel="STYLESHEET" type="text/css" href="style/ximdex.css"/>
</head>

<body leftmargin="0" rightmargin="0" bottommargin="0" topmargin="0" style="overflow: hidden;">
<table border="0" width="100%" cellpadding="1" cellspacing="0" align="center">
		<tr>
			<td height="15" valign="bottom">
			<div class="tituloseccion">&nbsp;_(Form editor)</div>
			</td>
		</tr>
		<tr>
			<td>
			<table class="actionbar">
				<tr>
					<td class="normal"><b>_(Description):</b>
					_(Create and edit forms on Ximdex)</td>
				</tr>				
			</table>
			</td>
		</tr>
		<tr>
			<td><br>
<form name="formulario">
<input type="hidden" name="resultados_dato_pagina" class="cajaxg" disabled="true"/>
<input type="hidden" name="resultados_dato_enlacef" class="cajaxg" disabled="true"/>
<input type="hidden" name="resultados_dato_enlacel" class="cajaxg" disabled="true"/>
<input type="hidden" name="resultados_identificador" class="cajaxg" disabled="true"/>
<input type="hidden" name="resultados_pagina" class="cajap" disabled="true"/>
<input type="hidden" name="servicio_detalle_variable" class="cajaxg" disabled="true"/>
<input type="hidden" name="nombre_formulario" class="cajaxg" disabled="true"/>
<input type="hidden" name="clase_formulario" class="cajaxg" disabled="true"/>
<table class="tabla" cellpadding="0" cellspacing="1" align="center">
	<tr>
		<td colspan="2" class="cabeceratabla">_(Editor de formularios)</td>
	</tr>
	<tr>
		<td class="tablamedia" nowrap>&nbsp;_(Service name):</td>
		<td class="tablamedia"><input type="Text" name="nombre_servicio" class="tablamedia" style="width: 400px; font-weight: normal;"/></td>
	</tr>
	<tr>
		<td class="tablamedia" nowrap>&nbsp;_(Handler name):</td>
		<td class="tablamedia"><input type="Text" name="nombre_manejador" class="tablamedia" style="width: 400px; font-weight: normal;"/></td>
	</tr>
	<tr>
		<td class="tablamedia">_(Type of form to perform):</td>
		<td class="tablamedia"><select name="tipo_formulario" class="cajag" onchange="cambia_tipo(this);">
			<option value="">&raquo; _(select type) &laquo;</option>
			<option value="entrada">_(Search / Insertion)</option>
			<option value="lista">_(List)</option>
			<option value="detalle">_(Detail / Simple deletion)</option>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</form>
	</tr>
	<tr>
		<td class="filaclarac" valign="top" align="center" colspan="2">
			<table class="tablam" cellpadding="0" cellspacing="0" width="200" align="left">
				<tr>
					<td align="center" class="filaoscura" colspan="2"><strong>_(Source fields)</strong></td>
				</tr>
				<tr>
					<td>
					<form name="elform">
						<select name="elselect" class="cajag" size="10" ondblclick="pasa_valores(this);"></select>
					</form>
					</td>
					<td class="filaclara"><a href="#" onclick="pasa_valores(elform.elselect);"><img src="images/utils-editor/right.gif" alt="" width="18" height="17" border="0"></a></td>
				</tr>
				<tr>
					<td align="center"><a href="#" onclick="pasa_todos(elform.elselect);" class="boton">_(All)</a></td>
				</tr>
			</table>
		
			<table class="tablam" cellpadding="0" cellspacing="0" width="260" align="left">
				<form name="modifica_valores">
				<input type="hidden" name="dinamico_propiedad"/>
				<input type="hidden" name="dinamico_etiqueta"/>
				<input type="hidden" name="dinamico_filtrado"/>
				<input type="hidden" name="descripcion_campo"/>
				<input type="hidden" name="elemento_valor"/>
				<tr>
					<td align="center" class="filaoscura" colspan="2"><strong>_(Field edition)</strong></td>
				</tr>
				<tr>
					<td class="filaclara"><strong>_(Field)</strong>:</td>
					<td class="filaclara"><input type="Text" class="cajag" name="campo" disabled="true"></td>
				</tr>
				<tr>
					<td class="filaclara"><strong>_(Name)</strong>:</td>
					<td class="filaclara"><input type="Text" class="cajag" name="nombre_campo"></td>
				</tr>
				<tr>
					<td class="filaclara" valign="top"><strong>_(Input type)</strong>:</td>
					<td class="filaclara">
					<select name="tipo_entrada" class="cajag">
						
					</select></td>
				</tr>
				<tr>
					<td class="filaclara" valign="top"><strong>_(Style)</strong>:</td>
					<td class="filaclara">
					<select name="estilo_campo" class="cajam">
						<option value="cajap">_(Small box (50 px))
						<option value="caja" selected>_(Box (100 px))
						<option value="cajag">_(Large box (200 px))
					</select></td>
				</tr>
				<tr>
					<td class="filaclara" valign="top"><strong>_(Validation)</strong>:</td>
					<td class="filaclara">
					<select name="validacion" class="cajam">
					</select></td>
				</tr>
				<tr>
					<td class="filaclara" valign="top" nowrap><strong>_(Error on server)</strong>:</td>
					<td class="filaclara">
					<select name="error_servidor" class="cajap">
						<option value="si">_(Yes)</option>
						<option value="no">_(No)</option>
					</select></td>
				</tr>
				<tr>
					<td class="filaclara" valign="top"><strong>_(Requested)</strong>:</td>
					<td class="filaclara">
					<select name="requerido" class="cajap">
						<option value="si">_(Yes)</option>
						<option value="no">_(No)</option>
					</select></td>
				</tr>
				<tr>
					<td class="filaclara" valign="top"><strong>_(Advanced)</strong>:</td>
					<td class="filaclara">
					<input type="Text" name="avanzada" class="cajap"/></td>
				</tr>
				<tr>
					<td align="center" colspan="2" class="filaclara">
						<a href="#" onclick="inserta_valores();"><img src="images/utils-editor/down.gif" alt="" width="18" height="17" border="0"></a>&nbsp;&nbsp;
						<a href="#" onclick="edita_valores();"><img src="images/utils-editor/up.gif" alt="" width="18" height="17" border="0"></a>
				</td>
				</tr>
				<input type="hidden" name="dinamico_variable" value="">
				</form>
			</table>
		</td>	
	</tr>
	<tr>
		<td colspan="2" align="center">
			<table border="0" class="tablam">
				<tr class="filaoscuranegritac">
					<td>_(Field)</td>
					<td>_(Style)</td>
					<td>_(Input type)</td>
					<td>_(Validation)</td>
					<td>_(Error)</td>
					<td>_(Requested)</td>
				</tr>
				<tr><form name="form_final">
					<td><select name="campo" class="caja" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td><select name="estilo" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td><select name="tipo" class="caja" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td><select name="validacion" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td><select name="error" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td><select name="requerido" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)"></select></td>
					<td>
					<a href="#" onclick="borra_valor();"><img src="images/utils-editor/borrar.gif" width="18" height="17" alt="" border="0"></a><br>
					<a href="#" onclick="pilla_valor('sube');"><img src="images/utils-editor/up.gif" alt="" width="18" height="17" border="0"></a><br>
					<a href="#" onclick="pilla_valor('baja');"><img src="images/utils-editor/down.gif" alt="" width="18" height="17" border="0"></a>
					</td>
					<td><select name="dinamico" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)" style="visibility: hidden;"></select></td>
					<td><select name="dinamico2" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)" style="visibility: hidden;"></select></td>
					<td><select name="descripcion_campo" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)" style="visibility: hidden;"></select></td>
					<td><select name="elemento_valor" class="cajap" size="10" ondblclick="edita_valor(this);" onclick="posiciona_elementos(this)" style="visibility: hidden;"></select></td>
					</form>
				</tr>
			</table>
		
		</td>
	</tr>
	<tr>
		<td align="center"><a href="#" onclick="limpia_formfinal();" class="botong">_(Reset)</a></td>
		<td align="center"><a href="#" onclick="convierte_formfinal();"><img src="images/utils-editor/guardar_y_salir.gif" width="96" height="12" alt="" border="0"></td>
	</tr>
</table>
<form name="bot"><textarea name="contenido" style="visibility:hidden;"><?php

 echo $content;?></textarea></form>
</body>
</html>
