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


var pag_max=10;
var pag_min=0
var arr_width = new Array();
var numero_nodos = 0;
var numero_acciones_contextual = 0;

document.onkeydown = detectar_tecla; 
document.onmousedown = ocultar_menu_contextual;

//cuando se presiona la tecla ESC(keyCode=27) se oculta el menu contextual de las acciones
function ocultar_menu_contextual(){
$('menu_contextual').style.visibility='hidden';

}
function detectar_tecla(){
	with (event){
		if (keyCode==27) {
			ocultar_menu_contextual();
			return false;
		}
	}
}
function valor_nodeID()
{
 return document.getElementsByName('nodeID')[0].value;
}

function valor_userID()
{
 return document.getElementsByName('userID')[0].value;
}

function valor_ximnews()
{
 return document.getElementsByName('ximnews')[0].value;
}

function valor_min_dia()
{
 return document.getElementsByName('min_dia')[0].value;
}

function valor_min_mes()
{
 return document.getElementsByName('min_mes')[0].value;
}

function valor_min_anio()
{
 return document.getElementsByName('min_anio')[0].value;
}

function leer_filtro_temp()
{
 var filtro_temp= document.getElementsByName('filtro_temp')[0].value;
 return filtro_temp;
}

function escribir_filtro_temp(xml)
{
 document.getElementsByName('filtro_temp')[0].value=xml;
}

//FUNCIONES DE PETICIÓN DE DATOS DE BUSQUEDA
function buscar_documentos_inicio(nodoID,userID,ximnews)
{
	ObtenerCabecera(userid);
	var url = nodeUrl+"&method=find";
	if (ximnews == 1){
		var categoria=$('combo_categorias').value;
	}
	var paginacion="&pag_min=0&pag_max="+pag_max;
	var xmlorden="<ordenes><orden campo='Name' tipo='ASC' /></ordenes>";
	var xmltipo = recopilar_tipo_nodo(nodoID,userID,pag_min,1);
	var pars =  "&nodeID="+nodoID+"&type=listado&userID="+userID+"&ximnews="+ximnews+paginacion+"&ordenacion="+xmlorden+"&tiposnodo="+xmltipo;
	mensaje_area("area1","Realizando búsqueda...");
	//inicio=0;
	//setExpirationDelay:(40 * 1000),
	//requestTimeout: 20
	var ajax = new Ajax.Request(
		url, {	method: 'post',	 requestTimeout: 100, parameters: pars,onComplete: obtenerDocs });
}
function UnirFiltros(xmlfiltro,filtro_global){
	var filtro = filtro_global;
	var filtro_final='';
	filtro = filtro.replace("<filtros>",'');
	filtro = filtro.replace("</filtros>",'');
	xmlfiltro = xmlfiltro.replace("<filtros>",'');
	xmlfiltro = xmlfiltro.replace("</filtros>",'');
	xmlfiltro += filtro;
	filtro_final = "<filtros>"+xmlfiltro+"</filtros>";
	return filtro_final;
}

function buscar_documentos_filtro(nodoID,userID,ximnews)
{
	var pag_min_aux;
	var filtros_avanzados = "";
	ObtenerCabecera(userid);
	var url = nodeUrl+"&method=find";
	if (ximnews == 1){
		var categoria=$('combo_categorias').value;
	}
	OcultarFiltroXimnews();
	var xmlorden = recopilar_ordenacion_filt();
	var xmlfiltro = recopilar_valores_filtro(ximnews);
	if(xmlfiltro.indexOf("tipo='exacta'") >0){
		xmlfiltro = xmlfiltro.replace("tipo='exacta'","tipo='click'");
		xmlfiltro = xmlfiltro.replace("Name","id");
		filtro_global = xmlfiltro;
		pag_min = 0;
		filtros_avanzados = -1;
	}
	else{
		pag_min_aux = pag_min;
		filtros_avanzados = filtros_busqueda_avanzada(0);
	}
	var xmltipo = recopilar_tipo_nodo(nodoID,userID,pag_min,1);
	if (filtros_avanzados!=-1){
		var filtro = "?filtro="+filtros_avanzados;
	}
	else{
		var filtro = "?filtro="+xmlfiltro;
	}
	if(xmlfiltro==-1){
		return;
	}

	var paginacion="&pag_min="+pag_min_aux+"&pag_max="+pag_max;
	var pars =  filtro+"&nodeID="+nodoID+"&type=listado&userID="+userID+"&ximnews="+ximnews+paginacion+"&ordenacion="+xmlorden+"&tiposnodo="+xmltipo;
	mensaje_area("area1","Realizando búsqueda...");

	//Se inicializa la caja de texto de la búsqueda exacta una vez que se ha seleccionado el doc de la lista
	$('texto_buscar').value='';
	contador_exacta=false;
	var ajax = new Ajax.Request(
		url, {	method: 'post',	parameters: pars, onComplete: obtenerDocs });
}

function buscar_documentos_ordenados(celda)
{
	if(columnas.length==0){
		ObtenerCabecera(userid);
	}
	ocultar_menu_contextual();
	var pag_min=0; //PROVISIONAL
	var url = nodeUrl+"&method=find";
	if (ximnews == 1){
		var categoria=$('combo_categorias').value;
	}
	var nodoID=valor_nodeID();
	var userID=valor_userID();

	var filtro=leer_filtro_temp();
	var xmlorden = recopilar_ordenacion(celda);
	var xmltipo = recopilar_tipo_nodo(nodoID,userID,pag_min,1);
	var paginacion="&pag_min="+pag_min+"&pag_max="+pag_max;
	var pars =  "&nodeID="+nodoID+"&type=listado&userID="+userID+"&ximnews="+ximnews+paginacion+"&ordenacion="+xmlorden+"&filtro="+filtro+"&tiposnodo="+xmltipo;
	mensaje_area("area1","Realizando búsqueda...");
	var ajax = new Ajax.Request(
		url, {	method: 'post',	parameters: pars, onComplete: obtenerDocs});
}

// campo: lleva el nombre del item por el que hay que filtrar
// objeto: lleva el input. Este valor habrá que utilizarlo para hacer la "select"

function _buscar_documentos_lista(nodoID,userID,ximnews)
{
	if(columnas.length==0){
		ObtenerCabecera(userid);
	}
	$('texto_buscar_oculto').value='';
	var objeto=$('texto_buscar');
	var lista=$('desplegable');
	var url = nodeUrl+"&method=find";

	if (ximnews == 1){
			var categoria=$('combo_categorias').value;
	}
	var xmlorden="<ordenes><orden campo='Name' tipo='ASC' /></ordenes>";
	var xmlfiltro = recopilar_valores_filtro(ximnews);
	xmlfiltro = xmlfiltro.replace(/\& /g,'&amp;');

	xmlfiltro = escape(xmlfiltro);

	var xmltipo = recopilar_tipo_nodo(nodeid,userid,pag_min,1);
	var pars =  "&nodeID="+nodoID+"&type=listado&userID="+userID+"&ximnews="+ximnews+"&ordenacion="+xmlorden+"&filtro="+xmlfiltro+"&tiposnodo="+xmltipo;
		// si el input está vacío, limpiamos el texto y nos vamos de la función
	if (objeto.value == ""){
		ocultar_desplegable();
		return;
	}

// 	alert("Con parámetros:"+pars);

	var ajax = new Ajax.Request(
	url, { method: 'post',setRequestHeader:('Content-Type: text/plain'),parameters: pars,onLoading:cargandoDocsLista,onComplete: obtenerDocsLista});
	
}


//FUNCIONES DE RECOPILACION DE CADENAS XML (FILTRO, ORDENACION, ETC)


function recopilar_valores_filtro(ximnews)
{
	var i;
	var j;
	var campo="";
	var valor="";
	var tabla="";
	var tipo_input="";

	var tipo_busqueda="";
	var area_filtro1=$('filtros_texto_buscar');
	ObtenerCabecera(userid);
	var xmltexto='';
	var cajas= area_filtro1.getElementsByTagName('input');
	var indice_cajas = cajas.length;
	for (j=0; j<indice_cajas;j++)
	{
		tipo_input = cajas[j].type;
		campo=cajas[j].name;
		valor=cajas[j].value;
		tabla="";
		if (valor!=""){
			if(xmltexto.indexOf(valor) < 0){

				xmltexto=xmltexto+"<filtro campo='"+campo+"' ";
				xmltexto=xmltexto+"valor='"+CaracteresEspeciales(valor)+"' ";
		 		xmltexto=xmltexto+"tipo='exacta' ";
	
				if (tabla!=""){
				  xmltexto=xmltexto+"tabla='"+tabla+"' ";
				}

				xmltexto=xmltexto+"/>";
					
			}
		}	
	}
	if (ximnews==1){
		var area_filtro2=$('filtro_categoria');
		var combos=area_filtro2.getElementsByTagName('select');
		var tipo_nodo= new Array();
		var indice_combos = combos.length;
		for (i=0; i<indice_combos;i++)
		{
			campo=combos[i].name;
			valor=combos[i].value;
			tabla="";
			if (valor!="" && valor!="0")
			{
				xmltexto=xmltexto+"<filtro campo='"+campo+"' ";
			 	xmltexto=xmltexto+"valor='"+valor+"' ";
				if (tabla!="")
				{
				  xmltexto=xmltexto+"tabla='"+tabla+"' ";
				}
				xmltexto=xmltexto+"/>";
			}	
		}

	//incluimos filtros por fecha, si los hubiere
		var filtro_fecha;
		//Si es noticia coge fecha si es contenedor no la coge como filtro
		filtro_fecha=recopilar_filtros_fecha();
		if(filtro_fecha==-1){
			return -1;
		}
		else{
			xmltexto=xmltexto+filtro_fecha;
		}
	}//cierre if ximnews=1
	xmltexto= "<filtros>"+xmltexto+"</filtros>";

	if(xmltexto=="<filtros></filtros>"){ 
		xmltexto="";
	}
	return xmltexto;
}

//funcion que cambia el tipo de ordenacion segun requiera el usuario
function ProcesarTipo(tipo){
   switch(tipo){
		case "":
		  tipo="ASC";
		break;
		case "ASC":
		  tipo="DESC";
		break;
		case "DESC":
		  tipo="ASC";
		break;
  }
return tipo;
}

function recopilar_ordenacion(celda)
{
	var campo="";
	var campoaux="";
	var tipo="";
	var cont=0;
	var sw=0;
	var xmltexto="";
	var campos_ocultos=$('campos_ordenacion').getElementsByTagName("INPUT");
	var nombre=celda.getElementsByTagName("INPUT")[0].value;
	if(columnas.length==0){
		ObtenerCabecera(userid);
	}
	var limite_ocultos = campos_ocultos.length;
	var pos =limite_ocultos-1;
	xmltexto="<ordenes>";

	for (var i=0;i<limite_ocultos;i++)
	{
		if(campos_ocultos[i].name=="campo"){
	  	  campo=campos_ocultos[i].value;
		  if (campo!=campoaux){
			campoaux=campo;
		  }
		}

		if(campos_ocultos[i].name=="tipo"){
		  tipo=campos_ocultos[i].value;

			if(campo==nombre){
			   tipo=ProcesarTipo(tipo);
			   //Para que filtre por varios campos quitar el codigo de abajo y descomentar el if
			   xmltexto=xmltexto+ "<orden campo='"+campo+"' tipo='"+tipo+"' />";
			}
			/* filtro varios campos quitar comentarios cuando se arregle
			 	if (tipo!=''){
				xmltexto=xmltexto+ "<orden campo='"+campo+"' tipo='"+tipo+"' />";
			}
			*/
			tipo='';
		}//cierre if
	}//cierre for
		xmltexto=xmltexto+"</ordenes>";
	return xmltexto;
}

function _recopilar_tipo_nodo(nodoID,userID,pag_min,control){
	var arr_tipo = new Array();
	var arr_tipo_id = new Array();
	var xmltipo = "<tiponodo>";
	var arr_checkbox = $('tipo_nodo').getElementsByTagName("INPUT");
	var tipo='';
	var contador=0;
	var limite_checkbox = arr_checkbox.length;
	for (var i=1;i<limite_checkbox;i++)
	{
		if(arr_checkbox[i].checked==true){
			 arr_tipo[contador] = arr_checkbox[i].value;
			 arr_tipo_id[contador] = arr_checkbox[i].id;

			 tipo = tipo +"<tipo name='"+ arr_checkbox[i].id+"'/>";
			 contador++;
		}
	}
	xmltipo = xmltipo+tipo+"</tiponodo>";

	if ((contador==1 && arr_tipo.indexOf('XimNewsNewLanguage')==-1) ||contador>1){
		ximnews=0;
		$('filtros_ximnews').style.visibility='hidden';
	}
	else if(contador>0){
		ximnews=1;
		//if (nombre_nodo=='news' ){
			$('filtros_ximnews').style.visibility='visible';
		//}
	}
	if (contador==0 && control==0){
		alert("Tiene que seleccionar un tipo de nodo");
		return;
	}
	if(control==0)
	{
		var options_select = '';
		if($('contiene_filtro')){
			//segun los nodos seleccionados se configura el filtro de busqueda avanzada
			HTMLSelectContiene();
		}
		//envio tipos de nodos
		//Limpieza de los valores del filtro para que no se guarden al buscar por un tipo de nodo
		mostrar_tipo_archivos();
		var paginacion = "&pag_min="+pag_min+"&pag_max="+pag_max;
		var xmlorden = "<ordenes><orden campo='Name' tipo='ASC' /></ordenes>";;
		var xmlfiltro = recopilar_valores_filtro(ximnews);
		if(xmlfiltro==-1){
			return;
		}
		var url = nodeUrl+"&method=find";
		var pars =  "&nodeID="+nodoID+"&type=listado&userID="+userID+"&ximnews="+ximnews+paginacion+"&ordenacion="+xmlorden+"&filtro="+xmlfiltro+"&tiposnodo="+xmltipo;
		var ajax = new Ajax.Request(
			url, { method: 'post',parameters: pars,	onComplete: obtenerDocs});
	}
	else if(control==1){
		return xmltipo;
	}
	else if(control==2){
		return arr_tipo;
	}
	else if(control==3){
		return arr_tipo_id;
	}
	
}

function recopilar_ordenacion_filt(celda)
{
	var campo="";
	var campo_temp="";
	var tipo_temp="";
	var tipo="";
	var xmltexto="<ordenes>";

	if($('campos_ordenacion')!=null)
	{
		var campos_ocultos=$('campos_ordenacion').getElementsByTagName("INPUT");

		var indice_ocultos = campos_ocultos.length;
		for (var i=0;i<indice_ocultos;i++)
		{

			if(campos_ocultos[i].name=="campo"){ 
				campo=campos_ocultos[i].value;
			}
			if(campos_ocultos[i].name=="tipo"){
				tipo=campos_ocultos[i].value;
			 	if (tipo!='' && campo!=''){
					xmltexto=xmltexto+"<orden campo='"+campo+"' tipo='"+tipo+"' />";
				}//cierre if
			}//cierre if

		}//cierre for
	 }//cierre if
	 else{
	 	xmltexto=xmltexto+"<orden campo='Name' tipo='ASC' />";
	 }
	 
	xmltexto=xmltexto+"</ordenes>";

	return xmltexto;
}

function OcultarColumnas(){

	$("menu_columnas").style.visibility="hidden";
	$("menu_columnas").innerHTML='';
}

function seleccion_columnas(xmlobj){
	
	if(window.ActiveXObject){
		var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
		xmlDoc.resolveExternals = false;
		xmlDoc.loadXML(xmlobj.responseText);
		columnas_user = xmlDoc.getElementsByTagName("columna");
		propiedades_user = xmlDoc.getElementsByTagName("propiedad");
		arr_idiomas_user = xmlDoc.getElementsByTagName("idioma");
		arr_canal_user = xmlDoc.getElementsByTagName("canal");
	}
	else{
		var dom = null;
		var parser = new DOMParser(); 
		dom = parser.parseFromString(xmlobj.responseText, "text/xml"); 
		columnas_user = dom.getElementsByTagName("columna");
		propiedades_user = dom.getElementsByTagName("propiedad");
		arr_idiomas_user = dom.getElementsByTagName("idioma");
		arr_canal_user = dom.getElementsByTagName("canal");
	}
	
	//alert(xmlobj.responseText);
}
function menu_cabecera(){
	var DivColumnas = $("menu_columnas");
	var posX = 	event.x+document.body.scrollLeft;
	var posY = 	event.y+document.body.scrollTop;
	DivColumnas.style.top=posY;
	DivColumnas.style.left=posX;
	DivColumnas.style.visibility="visible";
	DivColumnas.innerTEXT = "<table width=100%>";
	DivColumnas.innerTEXT = DivColumnas.innerTEXT + "<tr width=100%><td class='filacerrar' colspan='2'><a onclick='OcultarColumnas()'  class='filacerrar' style='cursor:pointer'>Cerrar Ventana</b>&nbsp;<img src='"+urlroot+"/xmd/images/botones/cerrar.gif' alt='' border='0'></a></td></tr>";
	var indice = columnas_user.length;
	var obligatorio;
	var nombre_columna;
	var idcolumna;
	var posicion;
	var enlace;
	var icono;
	var style='';
	var ruta = urlroot+"/xmd/images/icons/";
	Nombre_TD = '';
	Width_TD = 0;
	OcultarTiposNodo();
	OcultarFiltros()	
	if ($("config_filtros_ximnews")){
		OcultarFiltroXimnews();
	}
	for(var i=0;i<indice;i++){
		obligatorio = columnas_user[i].getAttribute("obligatorio");
		nombre_columna = '"'+columnas_user[i].getAttribute("name")+'"';
		idcolumna = columnas_user[i].getAttribute("id");
		posicion = arr_cabeceras.indexOf(columnas_user[i].getAttribute("name"));
		var onclick = "onclick='set_cabecera("+nombre_columna+","+idcolumna+","+posicion+");'";
		//Si el elemento de cabecera está en la cabecera de usuario aparece marcado
		if (posicion!=-1){
			icono = "<img src='"+urlroot+"/xmd/images/botones/editar.gif'/>";
		}
		else{
			icono = "&nbsp;";
		}
		
		if (obligatorio == 1){
			style="style='background-color:#dddddd;'";
			enlace = columnas_user[i].getAttribute("description");
		}
		else{
			style="style='cursor:pointer;'";
			enlace = "<a "+onclick+">"+columnas_user[i].getAttribute("description")+"</a>";
			
		}

		DivColumnas.innerTEXT = DivColumnas.innerTEXT +  "<tr><td "+style+">"+icono+"</td><td "+style+">"+enlace+"</td></tr>";
	}

	if (indice==1){
	 	var fila_aux="<tr><td>&nbsp;&nbsp;</td></tr>";
	}
	else{
		var fila_aux="";
	}
	if (indice==0){
	 	DivColumnas.innerTEXT += "<tr><td><b>No existen columnas.</b></td></tr>";
	}
	DivColumnas.innerTEXT = DivColumnas.innerTEXT + fila_aux+ "</table>";
	DivColumnas.innerHTML = DivColumnas.innerTEXT;

}

function set_cabecera(nombre,id_columna,operacion){

	var url = nodeUrl+"&method=find";
	OcultarColumnas();
	arr_cabeceras=new Array();

	//Si operacion es -1 se añade columna si es mayor que -1 se elimina columna
	var xmltipo = recopilar_tipo_nodo(0,userid,0,1);
	var pars =  "&type=columnas&userID="+userid+"&idcolumna="+id_columna+"&opcion="+operacion+"&tiposnodo="+xmltipo;
	var ajax = new Ajax.Request(
	url, { method: 'post',parameters: pars,onComplete: repintar_nodos});
}

function repintar_nodos(xmlobj){
	var nodos=null;
	var ordenacion = null;

	if(window.ActiveXObject)
	{
		var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
		var xmlDocNodos = new ActiveXObject("Msxml2.DOMDocument");
		xmlDoc.async = false;
		xmlDoc.resolveExternals = false;
		xmlDoc.loadXML(xmlobj.responseText);
		columnas=xmlDoc.getElementsByTagName("columna");
		xmlDocNodos.loadXML(XMlNodos.responseText);
		nodos=xmlDocNodos.getElementsByTagName("nodo");
		ordenacion=	xmlDocNodos.getElementsByTagName("orden");
	}
	else
	{
		var dom = null;
		var domNodos = null;
		var parser = new DOMParser(); 
		dom = parser.parseFromString(xmlobj.responseText, "text/xml"); 
		columnas = dom.getElementsByTagName("columna");
		domNodos = parser.parseFromString(xmlobj.responseText, "text/xml"); 
		nodos = domNodos.getElementsByTagName("nodo");
		ordenacion = domNodos.getElementsByTagName("orden");
		nodos_raiz = domNodos.getElementsByTagName("nodos");
	}
	//alert(xmlobj.responseText);

	var my_var = $('area1');
	my_var.innerText = escribirNodos(nodos,columnas,ordenacion);
	my_var.innerHTML = my_var.innerText;
	//Llamada a la función que redimensiona las celdas de la tabla
		if (columnas.length>1){
			TableKit.Resizable.init('tabla_nodos');
		}
		

}
function OcultarFiltroXimnews(){
	$('config_filtros_ximnews').style.visibility='hidden';
	$('seleccionaDiaDesde').style.visibility='hidden';
	$('seleccionaMesDesde').style.visibility='hidden';
	$('seleccionaAnyoDesde').style.visibility='hidden';
	$('seleccionaDiaHasta').style.visibility='hidden';
	$('seleccionaMesHasta').style.visibility='hidden';
	$('seleccionaAnyoHasta').style.visibility='hidden';
	$('combo_categorias').style.visibility='hidden';
}

function mostrar_filtros_ximnews(){
	var visibilidad = $("config_filtros_ximnews").style.visibility;
	if(visibilidad=='hidden'){
		var posX = 	525;
		var posY = 	event.y+document.body.scrollTop;
		OcultarTiposNodo();
		OcultarFiltros();
		OcultarColumnas();
		$('config_filtros_ximnews').style.visibility='visible';
		$('config_filtros_ximnews').style.top=posY+5;
		$("config_filtros_ximnews").style.left=posX;
		$('seleccionaDiaDesde').style.visibility= "visible";
		$('seleccionaMesDesde').style.visibility= "visible";
		$('seleccionaAnyoDesde').style.visibility= "visible";
		$('seleccionaDiaHasta').style.visibility= "visible";
		$('seleccionaMesHasta').style.visibility= "visible";
		$('seleccionaAnyoHasta').style.visibility= "visible";
		$('combo_categorias').style.visibility= "visible";
	}
	else{
		OcultarFiltroXimnews();
	}

}
function comprobar_marcado_archivos(idpropiedad)
{
 if ($(idpropiedad).checked == false && $('marcar_todos_nodos').checked==true)
  {
	$('marcar_todos_nodos').checked=false;
 }
}

function marcar_todos_archivos(){
	var check_todos = $('marcar_todos_nodos');
	var marcar;
	var checkboxes = document.getElementsByName("tiposNodo");
	var limite_checks = checkboxes.length;
    if (check_todos.checked){
		 marcar=true;
	}
	else{
		marcar=false;
	}
	 for(var i=0;i<limite_checks;i++)
	 {
	 	 if(marcar){
			  checkboxes[i].checked=true;
		  }
		  else {
			  checkboxes[i].checked=false;
		  }
	 }

}
function OcultarTiposNodo(){
	$("tipo_nodo").style.visibility='hidden';
}

function _mostrar_tipo_archivos(e){
	alert("ccc");
	var visibilidad = $("tipo_nodo").style.visibility;
	if(visibilidad=='hidden'){

		if (navigator.appName=="Microsoft Internet Explorer") { 
			var tempX = event.clientX + document.body.scrollLeft ;
			var tempY = event.clientY + document.body.scrollTop ;
		} else {  
			var tempX = e.pageX ;
			var tempY = e.pageY ;
		}  
		var element = document.getElementById("filtros_fila2");
		var positionX = 0;
		var positionY = 0;
		while (element != null) 
		{
			positionX += element.offsetLeft;
			positionY += element.offsetTop;
			element = element.offsetParent;
		}
		var yPosition = (positionY);
		var xPosition = (positionX);


		OcultarFiltros();
		OcultarColumnas();
		if ($("config_filtros_ximnews")){
			OcultarFiltroXimnews();
		}
		$("tipo_nodo").style.visibility='visible';
		$("tipo_nodo").style.top=yPosition   + "px";
		$("tipo_nodo").style.left=xPosition   + "px";
		Nombre_TD = '';
		Width_TD = 0;
	}
	else{
		OcultarTiposNodo();
		OcultarColumnas();
	}
	

}
//PAGINADOR
function cambiar_num_pag()
{
 var paginador_radios=$('Formrecargapag');
 var radios= paginador_radios.getElementsByTagName("input");
 var limite_radios = radios.length;
 for(var i=0;i<limite_radios;i++)
 {

	if(radios[i].checked)
	{
		if(radios[i].value!=pag_max)
		{ 
			pag_max = parseInt(radios[i].value);
			var nodoID = valor_nodeID();
			var userID = valor_userID();
			var ximnews = valor_ximnews();
			//buscar_documentos_inicio(nodoID,userID,ximnews);
			//buscar_documentos_filtro(nodoID,userID,ximnews);
			buscar_documentos_lista(nodoID,userID,ximnews);
		}
		return;
	}	 
 }
}
function TamanyoTexto(texto,longitud,width){
	var subs = 0;
	subs = texto.length*6;
	var resta = Math.round((subs-width)/6)+3;
	var resultado = texto.length-resta;
	if (resultado<0){
		resultado = 4;
	}
	texto = texto.substr(0,resultado);
	if (texto.length<longitud){
		texto += "...";
	}
return texto;
}
function redimensionar_texto(NombreTd){
	var tds = $('tabla_nodos').getElementsByTagName("td");
	var TextoSiguiente = '';
	var limite = tds.length;
	var limite_columnas = columnas.length;
	var texto = '';
	var texto1 = '';
	var widthCheck = 0;
	var width = 0;
	var subs1 = 0;
	Width_TD = 0;
	for (var i=0;i<limite;i++){
		widthCheck = tds[i].style.width;
		if (widthCheck != '' && i<limite_columnas){
			width = parseInt(widthCheck.substr(0,widthCheck.length-2));
			Nombre_TD = NombreTd;
			Width_TD = widthCheck;
		}
		else if(NombreTd == tds[i].name){
			texto = tds[i].title;
			var longitud = texto.length;
			texto = TamanyoTexto(texto,longitud,width);
			if (tds[i+1].title.length>46){
				var width_siguiente = tds[i+1].style.width;
				if (width_siguiente==''){
					width_siguiente = Math.round(longitud / 2)+5;
				}else{
					width_siguiente = parseInt(width_siguiente.substr(0,width_siguiente.length-1))+20;
				}
				if(tds[i+1].name != 'TablaNodos_Depth'){
					width_siguiente -= texto.length+3;
				}
				TextoSiguiente = CalcularTexto(tds[i+1].title,tds[i+1].name,width_siguiente);
			}else{
				TextoSiguiente = tds[i+1].title;
			}
			tds[i+1].innerHTML = TextoSiguiente;
			tds[i].innerHTML=texto;
		}
		
		tds[i].style.width='';
	}
}
