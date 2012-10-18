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

?>             

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Editar categorías</title>
<style type="text/css">

</style>
<link rel="STYLESHEET" type="text/css" href="style/ximdex.css" />
<script type="text/javascript" FOR="window" EVENT="onload">
for ( elem in window.dialogArguments )
  {
    switch( elem )
    {
    case "xml":
		xmlDoc.loadXML(window.dialogArguments["xml"]);
		origenXML.loadXML(window.dialogArguments["xml"]);
		break;
    }
}

prepara_boletines();
</script>
<script type="text/javascript">
// variable de depuración
var depura = false;

var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
xmlDoc.async = true;
xmlDoc.resolveExternals = true;

var origenXML = new ActiveXObject("Msxml2.DOMDocument");
origenXML.async = true;
origenXML.resolveExternals = true;

// codigo para poder depurar en local

if (depura)
	{
	xmlDoc.loadXML('<boletines><seccion_principal>la sección</seccion_principal><categoria titulo="Categoría generalaaa"><cuerpo-categoria><categoria_noticia a_enlaceid_noticia_url="13204"><categoria_noticia_titular>uno</categoria_noticia_titular></categoria_noticia><categoria_noticia a_enlaceid_noticia_url="13208"><categoria_noticia_titular>tres</categoria_noticia_titular></categoria_noticia><categoria_noticia a_enlaceid_noticia_url="13228"><categoria_noticia_titular>cuatro</categoria_noticia_titular></categoria_noticia></cuerpo-categoria></categoria><seccion_principal>la sección</seccion_principal><categoria titulo=""><cuerpo-categoria><categoria_noticia a_enlaceid_noticia_url="13206"><categoria_noticia_titular>dos</categoria_noticia_titular></categoria_noticia></cuerpo-categoria></categoria></boletines>');
	origenXML.loadXML('<boletines><seccion_principal>la sección</seccion_principal><categoria titulo="Categoría generalaaa"><cuerpo-categoria><categoria_noticia a_enlaceid_noticia_url="13204"><categoria_noticia_titular>uno</categoria_noticia_titular></categoria_noticia><categoria_noticia a_enlaceid_noticia_url="13208"><categoria_noticia_titular>tres</categoria_noticia_titular></categoria_noticia></cuerpo-categoria></categoria><seccion_principal>la sección</seccion_principal><categoria titulo=""><cuerpo-categoria><categoria_noticia a_enlaceid_noticia_url="13206"><categoria_noticia_titular>dos</categoria_noticia_titular></categoria_noticia></cuerpo-categoria></categoria></boletines>');
	}

	
// Fin código para depurar

// Función que carga las categorías existentes en el documento
// Además coje los elementos intermedios que haya dentro (párrafos, subtítulos, etc.) y los guarda

var origen = new Array();

function prepara_boletines(){
	var root = xmlDoc.documentElement;
	var cs = root.childNodes;
	for (n = 0; n < cs.length; n++){
			nueva_opcion = new Option();
			nueva_opcion2 = new Option();
	// si encuentro una categoría la introduzco en las combos
			if (cs.item(n).tagName == "categoria"){
				if (!cs.item(n).getAttribute("titulo")) opcion = "(sin nombre)";
				else opcion = cs.item(n).getAttribute("titulo");
				nueva_opcion.value = n;
				nueva_opcion.text = opcion;
				nueva_opcion2.value = n;
				nueva_opcion2.text = opcion;

				formulario.boletines_ini.add(nueva_opcion);
				formulario.boletines_fin.add(nueva_opcion2);
				}
	// si no es una categoría la borro del XML temporal
			else
				{
				root.removeChild(cs.item(n));
				//alert(origenXML.xml);
				n--;
				}			
			
	}
	visualiza_noticias(formulario.boletines_ini, formulario.noticias_ini);
}

// Función para visualizar las noticias existentes en un formulario

function visualiza_noticias(objeto, objeto2){
	if (!objeto.value){
		if (objeto.name == "boletines_ini") alert('Debe seleccionar una categoría');
		else limpia_combo(objeto2);
		return;
		}
	else{
		limpia_combo(objeto2);
		sel = -1;
		
		for (i=0;i<objeto.length;i++){
			if (objeto.options[i].selected) sel = i;
			}
		if (objeto.name == "boletines_fin") sel = sel - 1;
		var root = xmlDoc.documentElement;
		var cs = root.childNodes;

		if (cs.item(sel).childNodes.item(0).childNodes.length == 0 && objeto.name == "boletines_ini"){
			if (cs.item(sel).tagName != "seccion_principal")
			alert("No existen noticias asociadas a la categoría seleccionada");
			}
		else{
			if (cs.item(sel).tagName == "seccion_principal" && objeto.name == "boletines_fin"){
				alert("El ítem seleccionado es un titular de sección y no admite noticias asociadas");
				objeto.options[0].selected = true;
			}
		}
		for (l = 0; l < cs.item(sel).childNodes.item(0).childNodes.length; l++){
				nueva_opcion = new Option();
				nueva_opcion.value = l;
				nueva_opcion.text = cs.item(sel).childNodes.item(0).childNodes.item(l).childNodes.item(0).text
				objeto2.add(nueva_opcion);
			}
	}
}

// Función para crear una categoría nueva. Para el título coje el valor de la caja de texto. Si estuviera vacía
// crea una categoría sin título.

function crea_boletin(Ncategoria){
	var gen = false;
	if (formulario.nombre_categoria.value == "" && !Ncategoria){
		if (!confirm("¿Desea crear una categoría sin nombre?")) return;
		else{
			gen = true;
			formulario.nombre_categoria.value = "(sin nombre)";
		}
	}
	Bol = "categoria";
	newNode = xmlDoc.createNode(1, Bol, "");
	if (!Ncategoria){
		if (!gen) newNode.setAttribute ("titulo", formulario.nombre_categoria.value);
		else newNode.setAttribute ("titulo", "");
		}
	else{
		newNode.setAttribute ("titulo", Ncategoria);
	}
	newNode2 = xmlDoc.createNode(1, "cuerpo-categoria", "");
	newNode.appendChild(newNode2);
	//alert(newNode.xml);
	xmlDoc.documentElement.appendChild(newNode);
	limpia_combo(formulario.boletines_ini);
	limpia_combo(formulario.boletines_fin);
	limpia_combo(formulario.noticias_ini);
	limpia_combo(formulario.noticias_fin);
	nueva_opcion3 = new Option();
	nueva_opcion3.value = "";
	nueva_opcion3.text =  "« seleccione categoría »";
	formulario.boletines_fin.add(nueva_opcion3);
	prepara_boletines();
	formulario.nombre_categoria.value = "";
}

// Borrar un boletín

function borra_boletin(){
	if (!formulario.boletines_ini.value){
		alert('Debe seleccionar una categoría');
		return;
		}
	else{
		sel = -1;
		for (i=0;i<=formulario.boletines_ini.length-1;i++){
			if (formulario.boletines_ini.options[i].selected) sel = i;
			}
		if (confirm ("¿Está seguro de eliminar permanentemente la categoría ' " + formulario.boletines_ini.options[sel].text + " ' ?")){
			var root = xmlDoc.documentElement;
			var cs = root.childNodes;
			root.removeChild(cs.item(sel));
			limpia_combo(formulario.boletines_ini);
			limpia_combo(formulario.boletines_fin);
			prepara_boletines();
			}
	}
}

// función mover noticia

function mueve_boletin(){
	if (!formulario.noticias_ini.value){
		alert('Debe seleccionar una noticia a mover');
		return;
		}
	if (!formulario.boletines_fin.value){
		alert('Debe seleccionar una Categoría de destino');
		return;
		}
	else{
		var root = xmlDoc.documentElement;
		var cs = root.childNodes;
		var noti_sel = new Array();
		for (i=0;i<=formulario.noticias_ini.length-1;i++){
			if (formulario.noticias_ini.options[i].selected) noti_sel[noti_sel.length] = i;
			}
		bol_sel = -1;
		for (i=0;i<=formulario.boletines_ini.length-1;i++){
			if (formulario.boletines_ini.options[i].selected) bol_sel = i;
			}
		bol_sel2 = -1;
		for (i=0;i<=formulario.boletines_fin.length-1;i++){
			if (formulario.boletines_fin.options[i].selected) bol_sel2 = i-1;
			}
		if (bol_sel == bol_sel2) {
			alert("La Categoría origen y la Categoría destino no pueden ser la misma");
			return;
		}

		//cs.item(bol_sel).childNodes.item(0).removeChild(cs.item(bol_sel).childNodes.item(0).childNodes.item(noti_sel));
		
		var nuevas_opciones = new Array();
		for (var i=0; i<noti_sel.length; i++) {
			opcion = new Option();
			nuevas_opciones[nuevas_opciones.length] = opcion;
		}				
		for (var i=0; i<noti_sel.length; i++) {
			noti = noti_sel[i];
			nuevas_opciones[i].value = noti;
			nuevas_opciones[i].text = formulario.noticias_ini.options[noti].text;
			formulario.noticias_fin.add(nuevas_opciones[i]);		
			cs.item(bol_sel2).childNodes.item(0).appendChild(cs.item(bol_sel).childNodes.item(0).childNodes.item(noti-i));
		}
		for (var i=0; i<noti_sel.length; i++) {
			noti = noti_sel[i];
			formulario.noticias_ini.options[noti-i] = null;
		}
	}
}

// funciones generales con combos

function limpia_combo(objeto){
	l = objeto.length
		for(n=0; n < l; n++){
			objeto.options[0] = null;
		}
	if (objeto == "formulario.boletines_fin"){
			nueva_opcion3 = new Option();
			nueva_opcion3.value = "";
			nueva_opcion3.text =  "« seleccione categoría »";
			formulario.boletines_fin.add(nueva_opcion3);
	}	
}

function aceptar(){
if (!depura){
	unifica_XML();
	var arr = new Array();
	arr["xml"] = xmlDoc.xml;
	window.returnValue = arr;
	window.close();
	}
else{
	unifica_XML();
	alert(xmlDoc.xml);
	return;
	}
}

function unifica_XML(){
	var root = xmlDoc.documentElement;
	var cs = root.childNodes;

	var root2 = origenXML.documentElement;
	var cs2 = root2.childNodes;

	//alert(cs2.length + " " + cs.length);
	m = 0;
	var conta = false;
	for (n = 0; n < cs2.length; n++){
		if (cs2.item(n).tagName != "categoria"){
			root.insertBefore(cs2.item(n), root.childNodes.item(m));
			n--;
			m++;
		}
		m++;
	}
}


<?php 
$ximDEX_path = realpath(dirname(__FILE__) .  "/../../");

XSession::check();

$nodeID     =  $_GET["nodeid"];

$node=new Node($nodeID);
$idproject=$node->GetProject();

$arr_cat=SacaNombreCategorias($idproject);
$arr_secc=SacaNombreSecciones($idproject);


?>


function crea_boletin3(){
gen = false;
/* <seccion_principal>El Ministerio</seccion_principal> 
	<seccion_principal>Otras noticias</seccion_principal> 
 <seccion_principal>Comentario-Opinión</seccion_principal> */
<?php 
for($i=0;$i<count($arr_secc);$i++) {
	?>
	Bol = "seccion_principal";
	newNode = origenXML.createNode(1, Bol, "seccion_principal");
	MyText = origenXML.createTextNode("<?php echo $arr_secc[$i]?>");
	newNode.appendChild(MyText);
	origenXML.documentElement.appendChild(newNode);
<?php 	
}
?>
	alert("Secciones <?php for($i=0;$i<count($arr_secc);$i++) { if ($i<(count($arr_secc)-2)) {echo $arr_secc[$i].' ,'; } else if ($i<(count($arr_secc)-1)) {echo $arr_secc[$i].' '; } else {echo 'y '.$arr_secc[$i]; } }?> creadas correctamente");

	}
//var targetid = window.dialogArguments["nodeid"];

// creación de 18 categorías

function crea_C_provincias(){
var provincias = new Array();
<?php 
$str_provincias = "";
for($i=0;$i<count($arr_cat);$i++) {
	?>provincias[<?php echo $i?>] = "<?php echo $arr_cat[$i]?>";
	
<?php 	
	if ($i<(count($arr_cat)-2)) {
		$str_provincias.= $arr_cat[$i].' ,'; 
		} 
	else if ($i<(count($arr_cat)-1)) {
		$str_provincias.= $arr_cat[$i].' '; } 
	else {
		$str_provincias.= 'y '.$arr_cat[$i]; } 
		
}
?>


for (c = 0; c< provincias.length; c++){
	crea_boletin(provincias[c]);
	}
	alert("Categorías <?php  echo $str_provincias?> creadas correctamente");
}	

// fin personalización para MAP.
////////////////////////////////////

</script>
<style type="text/css">
body, td, button, input, .txt, select {
	font: MessageBox;
}
.txt,select { width:160px; padding:0px; margin:0px; border:1px inset window;}
</style>
</head>

<body>
<form name="formulario" id="formulario">
<table border="0" cellpadding="2" cellspacing="1" width="100%" class="tabla">
	<tr>
		<td class="cabeceratabla">Asitente de categorías</td>
	</tr>
	<tr>
		<td class="filaoscura">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td rowspan="2">&nbsp;&nbsp;&nbsp;<span class="normalnegrita">Crear categoría</span> <input type="Text" class="caja" name="nombre_categoria" id="nombre_categoria"/> <INPUT ID="useButton" TYPE="button"  VALUE="&raquo;"  onclick="crea_boletin();" class="botonxp" /></td>
				<td><INPUT ID="useButton" TYPE="button"  VALUE="Crear Secciones &raquo;"  onclick="crea_boletin3();" class="botong" style="width: 100px; text-align: center;" title="Crear secciones automáticas"/></td>
				<td><INPUT ID="useButton" TYPE="button"  VALUE="Crear Categorías &raquo;"  onclick="crea_C_provincias();" class="botong" style="width: 100px; text-align: center;" title="Crear categorías automáticas" /></td>
			</tr>
		</table>
		
		
		</td>
	</tr>
	<tr>
	<td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="filaoscuranegrita">Categorías existentes:</td>
				<td class="filaoscuranegrita">&nbsp;</td>
				<td class="filaoscuranegrita">Categorías destino:</td>
			</tr>
			<tr>
				<td class="filaclara">
					<select name="boletines_ini" id="boletines_ini" class="cajam" onchange="visualiza_noticias(formulario.boletines_ini, formulario.noticias_ini);" ></select><br/>
					
					
				</td>
				<td rowspan="2" class="filaclara">
					<INPUT ID="useButton" TYPE="button"  VALUE="Mover &raquo;" onclick="mueve_boletin();" class="botonp">
				</td>
				<td class="filaclara">
				<select name="boletines_fin" id="boletines_fin" class="cajam" onchange="visualiza_noticias(formulario.boletines_fin, formulario.noticias_fin);">
					<option value="">&laquo; seleccione categoría &raquo;</option>
				</select><br/>
				
				</td>
			</tr>
			
			<tr>
				<td class="filaclara"><select name="noticias_ini" id="noticias_ini" class="cajam" size="5" multiple></select></td>
				<td class="filaclara"><select name="noticias_fin" id="noticias_fin" class="cajam" size="5"></select></td>
			</tr>
			<tr>
				<td align="center" class="filaclara"><INPUT ID="useButton" TYPE="button"  VALUE="Borrar Categoría" onclick="borra_boletin();" class="boton"></td>
				<td class="filaclara">&nbsp;</td>
			</tr>
		</table>
	</td>
		
	</tr>
	<tr>
		<td colspan="3" align="right"><INPUT ID="useButton" TYPE="button"  VALUE="Aceptar" onclick="aceptar();" class="botonp"></td>
	</tr>
</table>
<!--textarea style="width: 400px; height: 150px; font-family: arial; font-size: 9px;" name="resultado"></textarea-->
</form>
</body>
</html>
