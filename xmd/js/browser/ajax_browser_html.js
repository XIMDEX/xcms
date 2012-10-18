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


function mensaje_area(idcapa,mensaje)
{
	var area = $(idcapa);
	if(area.firstChild){
	   area.removeChild(area.firstChild);
	}
	area.innerHTML="<div style='text-align:center' ><br/>"+mensaje+"</div>";
}

function OcultarDivs(){
	OcultarMenu();
	//OcultarColumnas();
	//OcultarTiposNodo();
	//OcultarFiltros();
}
function _menu_acciones_contextual(nodoID){
	//Left button of mouse has been pressed
	 if ($('menu_contextual').style.visibility == "visible"){
		$('menu_contextual').style.visibility = "hidden";
	 }
	 if (event.button==1){
		OcultarDivs();
		parent.parent.frames['toolbar'].SetSelectedNode(nodoID);
		//parent.parent.frames['toolbar'].reloadNode(nodoID);
		
	 }
	 //Right button of mouse has been pressed
	 else if (event.button==2){
	 
		OcultarTiposNodo();
		OcultarFiltros();
		if ($("config_filtros_ximnews")){
			OcultarFiltroXimnews();
		}
		CrearDivMenu(nodoID);
	 }
}

function addworkspace(titlebar, url, unclosable){
	parent.addtabpage(titlebar, url, unclosable);
}
	
function ExecuteAction(actionid, nodeid, titlebar){
	//alert('[EJECUTANDO ACCION]\n\tactionID: '+actionid+'\n\tnodeID: '+nodeid+'\n\ttitle: '+titlebar);
	addworkspace(titlebar, './loadaction.php?actionid='+actionid+'&nodeid='+nodeid);
	OcultarMenu();
}

function _CrearDivMenu(nodoID){
	var DivMenu = document.createDocumentFragment();
	var posX = event.x+document.body.scrollLeft;
	var posY = event.y+document.body.scrollTop;
	var bordeDerecho  = document.body.clientWidth  - event.clientX;  //it is what remains to right edge
	var bordeInferior = document.body.clientHeight - event.clientY;  //it is what remains to lower 
	//Creates a table with actions of selected node
	SetSelectedNodeContextual(nodoID,posY);
	DivMenu = $('menu_contextual'); 
	//Positions contextual menu in the screen
	DivMenu.style.left = posX;
	DivMenu.style.top = posY;
	DivMenu.style.visibility="visible";


   if (bordeDerecho<DivMenu.offsetWidth) {
       DivMenu.style.left=document.body.scrollLeft+event.clientX-DivMenu.offsetWidth;
	   

   } else {
       DivMenu.style.left=document.body.scrollLeft+event.clientX;

   }

  if (bordeInferior<DivMenu.offsetHeight) {
       DivMenu.style.top=document.body.scrollTop+event.clientY-DivMenu.offsetHeight;

   } else {
       DivMenu.style.top=document.body.scrollTop+event.clientY;

   }
return;

}
function OcultarMenu(){
	DivMenu = $('menu_contextual');
	DivMenu.style.visibility="hidden";
}

function NuevaCabecera(xmlobj){

	if(window.ActiveXObject)
	{
		var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
		xmlDoc.async = false;
		xmlDoc.resolveExternals = false;
		xmlDoc.loadXML(xmlobj.responseText);
		columnas = xmlDoc.getElementsByTagName("columna");
	}
	else
	{
		var dom = null;
		var parser = new DOMParser(); 
		dom = parser.parseFromString(xmlobj.responseText, "text/xml"); 
		columnas = dom.getElementsByTagName("columna");
	}
	//alert(xmlobj.responseText);
}

function ObtenerCabecera(userid){
	var url = nodeUrl+"&method=find";
	var tipos_nodos = recopilar_tipo_nodo(0,0,0,1);
	var pars = "&type=columnas&userID="+userid+"&tiposnodo="+tipos_nodos+"&opcion=inicio";
	var ajax = new Ajax.Request(
		url, {	method: 'post',	parameters: pars, onComplete: NuevaCabecera });
}

function ObtenerColumnasMenu(userid){
	var url = nodeUrl+"&method=find";
	var tipos_nodos = recopilar_tipo_nodo(0,0,0,1);
	var pars = "&type=columnas&userID="+userid+"&tiposnodo="+tipos_nodos+"&opcion=menu&nodeid="+nodeid;
	var ajax = new Ajax.Request(
		url, {	method: 'post',	parameters: pars, onComplete: seleccion_columnas });
}

function cabecera_resultados(ordenacion,ximnews)
{
	var orden=null;
	var ruta_botones = urlroot+"/xmd/images/botones/";
	var imagen_desc="<img src='"+ruta_botones+"mas_p_arriba.gif' />";
	var imagen_asc="<img src='"+ruta_botones+"mas_p.gif' />";
	var imagen="";
	var tipo="";
	var campo="";
	var htmlcab="";
	var style='';
	var indice = columnas.length;

	//htmlcab = "<tr class='filacampos' id='campos_ordenacion'><td class='celdacampo' name='TablaNodos_icono' onclick='buscar_documentos_ordenados(this)'>&nbsp;</td>";
	htmlcab = "<tr class='filacampos' id='campos_ordenacion'>";
	var k=0;
	for (i=0;i<indice;i++){
		//htmlcab=htmlcab+"<td onclick='buscar_documentos_ordenados(this)' class='celdacampo' ><input type='hidden' name='campo' value='"+columnas[i].getAttribute("name")+"' />";
		if (Nombre_TD == 'TablaNodos_'+columnas[i].getAttribute("name")){
			style="style='width:"+Width_TD+"'";
		}else{
			style="";
		}
		if (columnas[i].getAttribute("description")=='icono'){
			var descripciontd = '&nbsp;';
		}else{
			var descripciontd = columnas[i].getAttribute("description");
		}
		htmlcab=htmlcab+"<td id='Columna_"+columnas[i].getAttribute("name")+"' name='TablaNodos_"+columnas[i].getAttribute("name")+"' onclick='buscar_documentos_ordenados(this)' class='celdacampo' "+style+"><input type='hidden' name='campo' value='"+columnas[i].getAttribute("name")+"' />"; 
		if (i<ordenacion.length){
			tipo=ordenacion[i].getAttribute("tipo");
			campo=ordenacion[i].getAttribute("campo");
		     if (tipo=="DESC"){ 
			    imagen=imagen_desc;
		  	 }
			 else if (tipo=="ASC"){ 
				imagen=imagen_asc;
		   	 }			
		}
		if(campo==columnas[i].getAttribute("name") && tipo !=''){
			htmlcab=htmlcab+imagen+"<input type='hidden' name='tipo' value='"+tipo+"' />";
		}
		else{
			htmlcab=htmlcab+"<input type='hidden' name='tipo' value='' />";
		}
		if (i>0){
			k=i-1;
			arr_cabeceras[k] = columnas[i].getAttribute("name");
		}
		htmlcab=htmlcab+descripciontd+"</td>";
		
	}
	htmlcab=htmlcab+"</tr>";
	return htmlcab;
}
function escribirNodos(nodos,cabecera,ordenacion){
	var limite_nodos = nodos.length;
	var width = 0;
	var width_aux = 90;
	var style='';
	var limite_cabecera = columnas.length;
	var texto="<table id='tabla_nodos' class='resizable' style='width:100%;' cellspacing='0'>";
	var ruta_icono = '';
	texto = texto+cabecera_resultados(ordenacion,ximnews);
	numero_nodos = limite_nodos;
	
	for (i=0;i<limite_nodos;i++)
	{
			nodoID = nodos[i].getAttribute("id");
			nodetype = nodos[i].getAttribute("type");
			if(nodoID!="")
			{
				ruta_icono = urlroot+"/xmd/images/icons/"+nodos[i].getAttribute("icono");
				texto= texto + "<tr id='"+nodoID+"' class='doc' onmousedown='menu_acciones_contextual("+nodoID+")' ondblclick='add_doc(this)' ><td class='celda_icono'><img src='"+ruta_icono+"' /><input type='hidden' name='nodeID_sel' value='"+nodoID+"'/><input type='hidden' NAME='node_type' value='"+nodetype+"'/></td>";
				var elemento = '';
				var title = '';
				cont_items=0;
				for (j=0;j<limite_cabecera-1;j++)
				//for (j=0;j<limite_cabecera;j++)
				{	
					var elemento = nodos[i].getAttribute(arr_cabeceras[j]);
					elemento = elemento.replace("<\\",'&lt;');
					title = elemento;
					var nombreTD ="TablaNodos_"+arr_cabeceras[j];
					if (Nombre_TD == nombreTD){
						width = parseInt(Width_TD.substr(0,Width_TD.length-2));
						var longitud = title.length;
						elemento = TamanyoTexto(elemento,longitud,width);
						style = "style='width:"+Width_TD+"'";
					}
					else{
						if(Nombre_TD!=''){
							elemento = CalcularTexto(elemento,nombreTD,50);
						}
						style="";
					}
					elemento = EliminarBarraFinalRuta(elemento);
					texto = texto +"<td NOWRAP name='"+nombreTD+"' title='"+title+"'>"+elemento+"</td>";
					cont_items++;
				}
				texto = texto +"</tr>";
			}
	}
	texto= texto + "</table>";
return texto;
}
function AjustarTamanyoTabla(){
	var tds = $('tabla_nodos').getElementsByTagName("td");
	var limite = tds.length;
	var elemento = '';
	var limite_columnas = columnas.length;
	var width = Math.round(94 / limite_columnas)+20;
	var longitud = 0;
	var j=0;
	for (var i=0;i<limite;i++){
		if (i<limite_columnas+1){
			if (i==0){
				tds[i].style.width = "5%";
			}
		}
		else{
			elemento = tds[i].innerText;
			var long_maxima = 0;
			//All columns except the column with the icon file

			if(elemento != ''){
				if (tds[i].name=='TablaNodos_Name'){
					long_maxima = 52;
				}else{
					long_maxima = 46;
				}
				if (elemento.length>long_maxima){
						elemento = CalcularTexto(elemento,tds[i].name,width-5);
						tds[i].innerText = elemento;
				}
				tds[i].style.width = width+"%";
				 if (j==limite_columnas-1){
					j = 0;
				}else{
					j++;
				}
			}
			else{
				tds[i].style.width = "5%";
			}
		}
	}
}

function EliminarBarraFinalRuta(elemento){
	var barra = elemento.substr(elemento.length-1,elemento.length);
	if (barra == "/"){
		elemento = elemento.substr(0,elemento.length-1);
	}
return elemento;

}
function CalcularTexto(texto,nombreTD,width){
	if(nombreTD=='TablaNodos_Depth'){
		var arr_ruta = new Array();
		var ruta = '';
		arr_ruta = texto.split("/");
		//width = width+3;
		var limite = arr_ruta.length-1;
		for (var i=limite;i>0;i--){
			if (ruta.length>width){
				texto = ".../"+ruta;
				break;
			}else{
				if (arr_ruta[i]!=''){
					ruta = arr_ruta[i] + "/" + ruta;
				}
			}
		}
			//texto = ".../"+ruta;
	}else{
		width = width-16;
		if (width<3){
			width = 6;
		}
		texto = texto.substr(0,width);
		texto += '...';
	}
	texto = EliminarBarraFinalRuta(texto);
	return texto;
}
function obtenerDocs(xmlobj)
{
	var i;
	//Presentation of nodes
	var my_var = document.createDocumentFragment();
	//Call to ajax to obtain columns of nodetype where BROWSER is called
	my_var = $('area1');
	my_var.innerHTML="";
	ObtenerColumnasMenu(userid);
	XMlNodos=xmlobj;
	//alert(xmlobj.responseText);
	var nodos=null;
	var ordenacion = null;
	var nodos_raiz=null;
	if(window.ActiveXObject)
	{
		var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
		xmlDoc.async = false;
		xmlDoc.resolveExternals = false;
		xmlDoc.loadXML(xmlobj.responseText);
		nodos=xmlDoc.getElementsByTagName("nodo");
		ordenacion=	xmlDoc.getElementsByTagName("orden");
		nodos_raiz=xmlDoc.getElementsByTagName("nodos");
	}
	else
	{
		var dom = null;
		var parser = new DOMParser(); 
		dom = parser.parseFromString(xmlobj.responseText, "text/xml"); 
		nodos=dom.getElementsByTagName("nodo");
		ordenacion=dom.getElementsByTagName("orden");
		nodos_raiz=dom.getElementsByTagName("nodos");
	}
	//my_var.innerText=my_var.innerText+cabecera_resultados(ordenacion,ximnews);

	var nodoID;
	if (nodos.length==0)
	{
	  	$('paginador_area').innerHTML='';
		mensaje_area("area1","No se han encontrado resultados.");
		return;
	}
	else if ( (nodos.length==1) && (nodos[0].getAttribute("id")=="") )
	{
		$('paginador_area').innerHTML='';
		mensaje_area("area1","No se han encontrado resultados.");
		return;
	}		
	else
	{
		my_var.innerText = escribirNodos(nodos,columnas,ordenacion);
	}
    //my_var.innerText= my_var.innerText+"</table>";

	//guardado definitivo de todo el html
	my_var.innerHTML = my_var.innerText;
	asociar_clic_filas(my_var.firstChild);
	//Redimension cabeceras tabla;
	var width = 0;

	if(Nombre_TD==''){
		AjustarTamanyoTabla();
		//var limite = columnas.length;
		var limite = arr_cabeceras.length;
		for (var i=0;i<limite;i++){
			width = 15;
			$("Columna_"+arr_cabeceras[i]).style.width = width + "%";
		}
	}
	//paginador
	
	var pag_min = nodos_raiz[0].getAttribute("min");
	var pag_total = nodos_raiz[0].getAttribute("num");
	var pag_actual = parseInt(pag_min,10) + pag_max;
	var pag_minima = parseInt(pag_min,10) + 1;
	if(pag_actual>pag_total){
	  	 pag_actual = pag_total;
	}
	var indicador_html = "<table align='center'><tr><td><font color='#666666'>Elementos del </font><b>"+pag_minima+"</b><font color='#666666'> al  </font><b>"+pag_actual+"</b><font color='#666666'> de  </font><b>"+pag_total+"</b><font color='#666666'> en total.</font></td></tr><table>"
	$('paginador_area').innerHTML=indicador_html+paginador(pag_total,pag_min,pag_max);
	//Llamada a la función que redimensiona las celdas de la tabla
	if (columnas.length>1){
		TableKit.Resizable.init('tabla_nodos');
	}
	//guardado del xml del filtro
	var filtros=xmlDoc.getElementsByTagName("filtros");
	if(filtros.length>0){
	 escribir_filtro_temp(filtros[0].xml);
	}
	else{
	 escribir_filtro_temp("");
	}
}

//Mensaje que se muestra cuando se hace la busqueda por nombre y va mostrando resultados despues de insertar una letra
function cargandoDocsLista(){
	/*
	if (contador_exacta==false){
		$('contenedor_lista').innerHTML="<select name='resultados' id='desplegable' size='5'><option>Realizando búsqueda...</option></select>";
	}
	contador_exacta=true;
	*/
}

//Obtiene los documentos al ir insertando una letra en el input de buscar por nombre
function obtenerDocsLista (xmlobj)
{

// 	alert(xmlobj.responseText);
// 	return false;

	var i;
	var contenedor_lista=$('contenedor_lista');

	var lista;
	//alert(xmlobj.responseText);
	//presentacion de los nodos
	var my_var = document.createDocumentFragment();
	var nodos=null;
	var ordenacion = null;
	if(window.ActiveXObject)
	{
		var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
		xmlDoc.async = false;
		xmlDoc.resolveExternals = false;
		xmlDoc.loadXML(xmlobj.responseText);
		nodos=xmlDoc.getElementsByTagName("nodo");
		ordenacion=	xmlDoc.getElementsByTagName("orden");
	}
	else
	{
		var dom = null;
		var parser = new DOMParser(); 
		dom = parser.parseFromString(xmlobj.responseText, "text/xml"); 
		nodos=dom.getElementsByTagName("nodo");
		ordenacion=dom.getElementsByTagName("orden");
	}
	if(nodos.length > 0) 
	{
		if( (nodos.length==1) && (nodos[0].getAttribute("nombre")==""))
		{

			contenedor_lista.innerHTML="<select id='sin_resultados' size='5'><option>No se encuentran resultados</option></select>";
		}
		else
		{		
			contenedor_lista.innerHTML="<select name='resultados' id='desplegable' size='10' onclick='seleccionar_item_desplegable()'></select>";
			lista=$('desplegable');
			var indice_nodos = nodos.length;
			for (i=0;i<indice_nodos;i++)
			{
				inserta_opcion(lista,nodos[i].getAttribute("id"),nodos[i].getAttribute("Name"));
			}
			lista.style.visibility="visible";
		}
		
	}
	else 
	{
		contenedor_lista.innerHTML="<select id='sin_resultados' size='5'><option>No se encuentran resultados</option></select>";
	}
	contenedor_lista.firstChild.onblur=ocultar_desplegable_blur;
	contenedor_lista.firstChild.onmouseout=desplegable_out;
	contenedor_lista.firstChild.onmouseover=desplegable_over;
	$('area1').onclick=ocultar_desplegable; //permite ocultar el desplegable si hago clic en area1
	$('filtros').onclick=ocultar_desplegable; //oculta desplegabla si hago clic en el area de filtros
}

function limpia_combo( objeto ){
	if(objeto!=null)
	{
		l = objeto.length;
		for(var n=0; n < l; n++){
			objeto.options[0] = null;
		}
	}
}

function inserta_opcion( objeto , id, opcion )
{
	if(window.ActiveXObject)
	{
		nueva_opcion = new Option();
		nueva_opcion.value = id;
		nueva_opcion.text = opcion;
		objeto.add(nueva_opcion);
	}
	else
	{
		nueva_opcion = document.createElement("OPTION");
		nueva_opcion.setAttribute("value", id);
		nueva_opcion.textContent=opcion;
		objeto.appendChild(nueva_opcion);
	}
}

function seleccionar_item_desplegable(){
	var lista = $('desplegable');
	var caja = $('texto_buscar');
	var caja_oculta = $('texto_buscar_oculto');
	
	var elementos = lista.length;
	if (!lista.value){
		alert("Debe seleccionar un valor");
		return;
	}

	//tomamos el valor del desplegable

	for (var n=0; n < lista.length; n++){
		if(lista[n].selected){
			caja.value = lista[n].value;
			caja_oculta.value = lista[n].value;
		}
	}
	//se oculta el desplegable
	ocultar_desplegable();
	//se realiza una query con el nuevo valor del desplegable
	var nodoID=valor_nodeID();
	var userID=valor_userID();
	var ximnews=valor_ximnews();
	contador_exacta=false;
	buscar_documentos_filtro(nodoID,userID,ximnews);
}

var no_ocultar=false;

function ocultar_desplegable_blur(e)
{
	var evento = window.event ? window.event : e;
	if(!no_ocultar)
	{
		var contenedor_lista=$('contenedor_lista');
		contenedor_lista.innerHTML="";
	}	
}

function desplegable_out(e)
{
 	var evento = window.event ? window.event : e;
	no_ocultar=false;
}

function desplegable_over(e)
{
	var evento = window.event ? window.event : e;
	no_ocultar=true;
}

function ocultar_desplegable()
{
	var contenedor_lista=$('contenedor_lista');
	contenedor_lista.innerHTML="";

}

function tipo_busqueda_clic()
{
	//si cambio de busqueda exacta a aproximada, borra la lista desplegable
	var combo=$('combo_tipo_busqueda');
	ocultar_desplegable();	

	//if (combo.value=="exacta")
	//{
		var nodoID=valor_nodeID();
		var userID=valor_userID();
		var ximnews=valor_ximnews();
		buscar_documentos_lista(nodoID , userID, ximnews);
	//}
}
//funcion que oculta todos los componentes del filtro de búsqueda
function OcultarFiltros(){
	$("config_filtros").style.visibility="hidden";
	if($('texto_filtro')){
		$('texto_filtro').style.visibility = "hidden";
	}
	if($('idiomas_filtro')){
		$('idiomas_filtro').style.visibility = "hidden";
	}
	if($('propiedades_filtro')){
		$('propiedades_filtro').style.visibility = "hidden";
	}
	if($('canales_filtro')){
		$('canales_filtro').style.visibility = "hidden";
	}
	
}
//funcion que reemplaza los caracteres especiales no admitidos en xml para que no se produzca error
function CaracteresEspeciales(texto){

	texto = texto.replace("\&",escape('&amp;'));
	texto = texto.replace("\<",escape('&lt;'));
	return texto;
}

function CaracteresXimtaxValues(texto){

	texto = texto.replace(/ /g,'|#|');
	return texto;
}

function NodosDocumentos(arr_tipo_nodos){
	var num_nodos = arr_tipo_nodos.length;
	for (i=0;i<num_nodos;i++){
		if (id_nodos_documentos.indexOf(arr_tipo_nodos[i])==-1){
			return false;
		}
	}

return true;
}

function filtros_busqueda_avanzada(ctrl){
	urlfiltro = "";
	var url = nodeUrl+"&method=find";
	var filtro_columnas = document.getElementsByName("columnas_filtro");
	var filtro_contiene = document.getElementsByName("contiene_filtro");
	var filtro_texto = document.getElementsByName("texto_filtro");
	var filtro_operador = document.getElementsByName("operador_filtro");
	var input  = $("config_filtros").getElementsByTagName("input");
	var limite = 0;
	var operador = '';
	var parametrosSolr = '';
	var separador = '';
	var parentesis = '';
	//xml con los tipos de nodos seleccionados por el usuario
	var xmltipo = recopilar_tipo_nodo(nodeid,userid,pag_min,1);
	//Recopilacion del xml para el orden y dlos tipos de nodo
	var xmlorden = recopilar_ordenacion_filt();
	var arr_tipo_nodos = recopilar_tipo_nodo(0,0,0,3);
	
	//sw que segun los tipos de nodos a buscar usa solr o no
	var sw_solr = NodosDocumentos(arr_tipo_nodos);
	
	var paginacion="&pag_min=0&pag_max="+pag_max;
		
	limite = filtro_operador.length;
	if (limite==0){
		return -1;
	}
	 for (var i = 0; i <limite; i++) {
		if (filtro_operador[i].checked) {
			operador = filtro_operador[i].value;
			operador_filtro = filtro_operador[i].value;
			break;
		}
	}
	limite = input.length-1;
	if (limite==2 && ctrl==1){
		alert("No hay filtros de búsqueda.");
		return;
	}
	var j = 0;
	var k = 7;
	var filtro = '';
	var separador = '';
	//Segun los tipos de nodo elegidos por el usuario se busca por Solr o por BB.DD
	if (!sw_solr){
		var xmlfiltro = recopilar_filtros(0,1);
		pars =  "&nodeID="+nodeid+"&type=listado&userID="+userid+"&ximnews="+ximnews+paginacion+"&ordenacion="+xmlorden+"&filtro="+xmlfiltro+"&tiposnodo="+xmltipo;
		urlfiltro = xmlfiltro;
	}
	else{
		//Construccion del filtro para solr
		var valor_busqueda = '';
		var elemento_filtro = '';
		for (var i=6;i<limite;i=i+4){
			j = i;
			elemento_filtro = input[j+2].value;
			if ((elemento_filtro.indexOf("aprox")>-1 || elemento_filtro.indexOf("documento")==-1) && elemento_filtro.indexOf("ximtax_values")==-1){
				var valor_aux = elemento_filtro;
				valor_busqueda = valor_aux.replace("aprox",'');
			}
			else{
				valor_busqueda = elemento_filtro;
			}
			if (i==6){ 
				input[k].value = input[k].value.replace("aprox",'');
				if (valor_busqueda.indexOf("ximtax_values")>-1){
					urlfiltro += "%28"+CaracteresEspeciales(valor_busqueda,"ximtax")+input[k].value;
				}
				else{
					urlfiltro += "%28"+CaracteresEspeciales(valor_busqueda,"documento")+input[k].value;
				}
			}
			else{
				if (valor_busqueda.indexOf("ximtax_values")==0){
					separador = "+"+operador+"+";
					var reemplazo = valor_busqueda;
					urlfiltro += separador+CaracteresEspeciales(reemplazo,"ximtax")+input[k].value;	
				}
				else{
					separador = "+"+operador+"+";
					var reemplazo = valor_busqueda;
					if (filtro.indexOf("ximtax_values")-1){
						valor_busqueda=CaracteresXimtaxValues(valor_busqueda);
					}
					urlfiltro += separador+CaracteresEspeciales(reemplazo,"documento")+input[k].value;
				}
				filtro = valor_busqueda;
			}
			k = k + 4;
		}
		urlfiltro += "%29";
		var posicion_version = urlfiltro.indexOf("version");
		if (posicion_version==-1){
			parametrosSolr = "+AND+isLastVersion%3Atrue";
		}
		else{
			parametrosSolr = "";
		}
		urlfiltro += parametrosSolr+"&version=2.2&indent=on&nodeID="+nodeid+"&busqueda=avanzada&type=listado&userID="+userid+"&ximnews="+ximnews+"&ordenacion="+xmlorden;
		pars = "?filtro="+urlfiltro+paginacion+"&tiposnodo="+xmltipo;
		if(j==0){
			return -1;
		}
	}
	$('texto_buscar_oculto').value='';
	if (ctrl==0){
		return urlfiltro;
	}

	OcultarFiltros();

	//alert(pars);
	//Llamada ajax para la busqueda
	var ajax = new Ajax.Request(
		url, {	method: 'post',parameters:pars, onComplete: obtenerDocs });
}

function recopilar_filtros(pag_minima,ctrl){

	var filtro_columnas = document.getElementsByName("columnas_filtro");
	var filtro_contiene = document.getElementsByName("contiene_filtro");
	var filtro_texto = document.getElementsByName("texto_filtro");
	var filtro_operador = document.getElementsByName("operador_filtro");
	var input  = $("config_filtros").getElementsByTagName("input");
	var limite = 0;
	var operador = '';
	pag_min=pag_minima;
	var xmlfiltro = "<filtros>";
	if ($("config_filtros_ximnews")){
		var xmlfiltrosfecha = recopilar_valores_filtro(1);
		xmlfiltrosfecha=xmlfiltrosfecha.replace("<filtros>",'');
		xmlfiltrosfecha=xmlfiltrosfecha.replace("</filtros>",'');
	}
	limite = filtro_operador.length;
	 for (var i = 0; i <limite; i++) {
		if (filtro_operador[i].checked) {
			operador = filtro_operador[i].value;
			operador_filtro = filtro_operador[i].value;
			break;
		}
	}
	if (ximnews ==1){
		xmlfiltro += xmlfiltrosfecha;
	}	
	limite = input.length-1;
	if (limite==2 && ctrl==1){
		alert("No hay filtros de búsqueda.");
		return;
	}
	var valor_tipo;
	var valor_busqueda;
	var campo_busqueda;
	for (var i=6;i<limite;i=i+4){
		var j = i;
	    campo_busqueda = input[j+2].value;
		var indice = campo_busqueda.indexOf("%3A");
		if (indice>-1){
			campo_busqueda = campo_busqueda.substr(0,indice);
			campo_busqueda = campo_busqueda.replace("documento","Name");
		}
		else {
			campo_busqueda = input[j].value;
		}
		xmlfiltro += "<filtro campo='"+ campo_busqueda +"' ";
		
		valor_tipo = input[j+1].value;
		valor_tipo = 'aprox';
		xmlfiltro += "tipo='"+valor_tipo+"' ";
		
		valor_busqueda = CaracteresEspeciales(input[j+2].value);
		valor_busqueda = ReemplazarValorBusqueda(valor_busqueda);
		xmlfiltro += 'valor="'+valor_busqueda+'" ';
		xmlfiltro += "operador='"+operador+"' />";
	}

	xmlfiltro += "</filtros>";
	if ( ctrl==1){
		return xmlfiltro;
	}
	//xmlfiltro = xmlfiltro.replace(xmlfiltrosfecha,'');
	var filtros_avanzados = filtros_busqueda_avanzada(0);
	var xmlorden = recopilar_ordenacion_filt();
	var url = nodeUrl+"&method=find";
	var xmltipo = recopilar_tipo_nodo(nodeid,userid,pag_min,1);
	
	//Separacion de filtros por solr o por BB.DD
	if (filtros_avanzados != -1)
	{
	var paginacion ="&pag_min="+pag_minima+"&pag_max="+pag_max;
	 filtros_avanzados += paginacion+"&ordenacion="+xmlorden;
	 var pars = "?filtro="+filtros_avanzados+"&nodeID="+nodeid+"&type=listado&userID="+userid+"&ximnews="+ximnews+paginacion+"&ordenacion="+xmlorden+"&tiposnodo="+xmltipo;
	}
	else
	{
	 var paginacion ="&pag_min="+pag_min+"&pag_max="+pag_max;
	  var pars =  "&nodeID="+nodeid+"&type=listado&userID="+userid+"&ximnews="+ximnews+paginacion+"&ordenacion="+xmlorden+"&filtro="+xmlfiltro+"&tiposnodo="+xmltipo;
	}
	OcultarFiltros();
	mensaje_area("area1","Realizando búsqueda...");
	filtro_global = xmlfiltro;
	var ajax = new Ajax.Request(
		url, {	method: 'post',	parameters: pars, onComplete: obtenerDocs });
}

function ReemplazarValorBusqueda(texto){

var resultado = '';
var index = texto.indexOf("%3A");

if (index>-1){
	//Se suma tres pq son tres caracteres
	resultado = texto.substr(index+3,texto.length);	
}
else{
	resultado = texto;
}

return resultado
}
function EliminarFiltro(){
	html_filtro='';
	filtro_global='';
	urlfiltro='';
	var filtro_operador = document.getElementsByName("operador_filtro");
	var nodos = $('filtros_resultados').childNodes[0];
	var limite=nodos.childNodes.length-1;
	for (var i=1; i<limite; i++) { 
	  	nodos.removeChild(nodos.lastChild); 
	 }
	 $("texto_buscar_oculto").value='';
	operador_filtro = '';
	filtro_operador[0].checked = true;
	buscar_documentos_inicio(nodeid,userid,ximnews);
	return;
}

function inicializar_form_filtro(sw_solr){
	//Funcion que se encarga de los selects y checks del formulario de búsqueda
	var filtro_operador = '';
	var limite = 0;
	var valor_columnas = '';
	var valor_contiene = '';
	if (sw_solr){
		valor_columnas = "documento%3A";
		valor_contiene = '';
		$('idiomas_filtro').style.visibility = "hidden";
		$('propiedades_filtro').style.visibility = "hidden";
		$('doc_publicado').style.visibility = "hidden";
		$('texto_filtro').style.visibility = "visible";
		$('columnas_filtro').style.position="relative";
		$('columnas_filtro').style.top="20%";

	}
	else{
		valor_columnas = 0;
		valor_contiene = 'aprox';

	}
	$('columnas_filtro').value = valor_columnas;
	$('contiene_filtro').value = valor_contiene;
	$('texto_filtro').value = '';
	
	if (operador_filtro!=''){
		filtro_operador = document.getElementsByName("operador_filtro");
		limite = filtro_operador.length;
		for (var i=0;i<limite;i++){
			if(filtro_operador[i].value == operador_filtro){
				filtro_operador[i].checked=true;
				return;
			}
		
		}
	}
}

function guardar_cabecera_filtro(){
	var tbody = $("config_filtros").getElementsByTagName("TBODY")[1];
	cabecera_filtro = tbody.childNodes[2].innerHTML;
}

function IndiceSelect(valor){

	var separador = valor.indexOf("_");
	var indice = valor.substr(separador+1,valor.length);
	
return indice;
}


function anyadir_filtro(nodo,sw_solr){
	//Sube de nivel hasta tbody coge el primer hijo que es el tr con los selects y el input y se añade
	var nodopadre = nodo.parentNode.parentNode.parentNode;
	var formularios = nodopadre.childNodes[nodopadre.childNodes.length-2];
	var tbody = $("config_filtros").getElementsByTagName("TBODY")[1];
	var tbodyresultado=$('filtros_resultados').getElementsByTagName("TBODY")[0];
	var ultimohijo = tbodyresultado.childNodes[tbodyresultado.childNodes.length-1];
	var new_nodo = ultimohijo.cloneNode(true);
	var valor_combo = $('columnas_filtro').value;
	var seleccion = valor_combo.indexOf("columna");
	var indice = 0;
	var texto_select = '';
	var separador = '';
	var texto_filtro = $('texto_filtro').value;
	var	valor_filtro =  '';
	var valor_campo =  '';
	var contiene_filtro = $('contiene_filtro').value;
	var texto_mostrar = $('columnas_filtro').options[ $('columnas_filtro').selectedIndex].text;
	var tipo_busqueda = $('contiene_filtro').options[ $('contiene_filtro').selectedIndex].text;
	//Se establecen los caracteres especiales para los valores de ximtax para solr
	if (valor_combo=="ximtax_value"){
		separador = "|";
		texto_select = $('columnas_filtro').options[ $('columnas_filtro').selectedIndex].text;
	}
	else{
		texto_select = '';
		separador = "";
	}
	
	//Construccion del filtro segun el tipo de documentos (documentos o resto de nodos)
	if (sw_solr){
		var escape_comillas_dobles;
		if(contiene_filtro=='*' || valor_combo == "nodeid"){
			escape_comillas_dobles = '';
		}
		else{
			escape_comillas_dobles = escape('"');
		}
		//Creacion del filtro según  se ha seleccionado en el Select de 'columna'
		switch (valor_combo){
			case "canales":
				valor_filtro = $('canales_filtro').value+escape_comillas_dobles;
				$('texto_filtro').value = valor_filtro;
				texto_filtro = $('canales_filtro').options[$('canales_filtro').selectedIndex].text;
			break;
			case "idioma":
				valor_filtro = $('idiomas_filtro').value+escape_comillas_dobles;
				$('texto_filtro').value = valor_filtro;
				texto_filtro = $('idiomas_filtro').options[$('idiomas_filtro').selectedIndex].text;
			break;
			case "publicado":
				  var i;
				  var arr_filtro_publicado = document.getElementsByName("publicado_filtro");
				  var numero_radios = arr_filtro_publicado.length;
				  valor_filtro = "isPublishedVersion%3A"+escape_comillas_dobles;
			      for (i=0;i<numero_radios;i++){
				    if (arr_filtro_publicado[i].checked){
						  texto_filtro = arr_filtro_publicado[i].value;
						  if (texto_filtro == 'Si'){
							  valor_filtro += "true";
						  }
						  else{
						  	   valor_filtro += "false";
						  }
					  }
			      } 
				  valor_filtro += escape_comillas_dobles;
			 	$('texto_filtro').value = valor_filtro;
			break;
			case "propiedades":
				texto_mostrar = $('propiedades_filtro').options[$('propiedades_filtro').selectedIndex].text;
				texto_filtro = $('texto_filtro').value;
				valor_filtro = $('propiedades_filtro').value+$('texto_filtro').value+escape_comillas_dobles;
			break;
			default:
				valor_filtro = valor_combo+escape_comillas_dobles+texto_select+separador+$('texto_filtro').value+escape_comillas_dobles;
				indice = IndiceSelect(valor_combo);
				valor_campo = '';
		}
	}
	else{
		valor_filtro = texto_select+separador+$('texto_filtro').value;
		indice = $('columnas_filtro').value;
		valor_campo =  columnas_user[indice].getAttribute("name");
		
	}
	//se reemplaza por su entidad html pq sino no lo muestra
	valor_filtro = valor_filtro.replace("&","&amp;");
	valor_filtro = valor_filtro.replace("<",'&lt;');
	guardar_cabecera_filtro();
	//construye el html del filtro añadido desde el ultimo hijo
	if($('texto_filtro').value != ''){
		tbodyresultado.appendChild(new_nodo);
		ultimohijo.childNodes[0].innerHTML = "<input name='campo' type='hidden' value='"+valor_campo+"'>"+texto_mostrar;
		ultimohijo.childNodes[0].className='filaclara';
		ultimohijo.childNodes[1].innerHTML = "<input name='tipobusqueda' type='hidden' value="+contiene_filtro+">"+tipo_busqueda;
		ultimohijo.childNodes[1].className='filaclara';
		ultimohijo.childNodes[2].innerHTML = "<input name='texto' type='hidden' value='"+valor_filtro+"' >"+texto_filtro;
		ultimohijo.childNodes[2].className='filaclara';
		ultimohijo.childNodes[3].innerHTML = "<input type='button' onclick='borrar_filtro(this)' class='boton' style='width: 25px;' value='-' title='Eliminar'>";
		ultimohijo.childNodes[3].className='filaclara';
		ultimohijo.childNodes[3].style.align='center';
		tbodyresultado.appendChild(ultimohijo);
		html_filtro += "<TR>"+ultimohijo.innerHTML+"</TR>";
		inicializar_form_filtro(sw_solr);
	}
}

function borrar_filtro(nodo){
	var deletenodo = nodo.parentNode.parentNode;
	var tbody=$('filtros_resultados').getElementsByTagName("TBODY")[0];
	
	var titulos_busqueda = tbody.childNodes[0];
	var indice = tbody.childNodes.length-1;
	//se borra el nodo seleccionado por el usuario
	
	tbody.removeChild(deletenodo);
	html_filtro='';
	
	/*Se 'capturan' los filtros añadidos y no borrados para mantener los filtros de búsqueda
	se empieza por 2 pq los primeros elementos son las cabeceras y los select*/
	//html_filtro += "<tr>"+titulos_busqueda.innerHTML+"</tr>";
	
	for (var i=2;i<indice;i++){
		html_filtro += "<tr>"+tbody.childNodes[i].innerHTML+"</tr>";
	}
	
	$('texto_buscar_oculto').value='';
}

//Cambia el puntero del raton cuando se situa el raton en la barra gris
function cambiarPuntero(tabla){
	tabla.style.cursor="move";
}
//Rellena el select del filtro del tipo de busqueda
function HTMLSelectContiene(){
	var arr_tipo_nodos = recopilar_tipo_nodo(0,0,0,3);
	var sw_solr = NodosDocumentos(arr_tipo_nodos);
	var OptionEmpieza;
	var OptionIgual;
	var selectFiltro = $('contiene_filtro');
	$('contiene_filtro').innerHTML = '';
	
	if(sw_solr){
		OptionIgual = document.createElement("OPTION");
		OptionIgual.value = '';
		OptionIgual.innerText = "Igual";
		selectFiltro.appendChild(OptionIgual);

		OptionEmpieza = document.createElement("OPTION");
		OptionEmpieza.value = '*';
		OptionEmpieza.innerText = "Empieza por";
		selectFiltro.appendChild(OptionEmpieza);
	}
	else{
		OptionEmpieza = document.createElement("OPTION");
		OptionEmpieza.value = 'aprox';
		OptionEmpieza.innerText = "Contiene";
		selectFiltro.appendChild(OptionEmpieza);
		
	}
}
function OptionsCanales(){

	//Filtro de canales

	var nombre_idioma = '';
	var nombre_idioma_iso = ' ';
	var indice_canales = arr_canal_user.length;
	var obj_select = $('canales_filtro');
	var obj_option;
	var obj_texto;
	var escape_comillas_dobles = escape('"');
	if($('canales_filtro').innerHTML.length==0){
		for(var j=0;j<indice_canales;j++){
			nombre_canal = arr_canal_user[j].getAttribute("name");
			descripcion = arr_canal_user[j].getAttribute("description");
			obj_option = document.createElement("OPTION");
			obj_option.value = "canales_desc%3A"+escape_comillas_dobles+descripcion;
			obj_texto = document.createTextNode(descripcion);
			obj_option.appendChild(obj_texto);
			obj_select.appendChild(obj_option);
		}
	}
	else{
		return;
	}
}

function OptionsLanguages(){

	//Filtro de idiomas
	
	var nombre_idioma = '';
	var nombre_idioma_iso = ' ';
	var indice_idiomas = arr_idiomas_user.length;
	var opciones_idiomas = '';
	var obj_select = $('idiomas_filtro');
	var obj_option;
	var obj_texto;
	var escape_comillas_dobles = escape('"');
	if($('idiomas_filtro').innerHTML.length==0){
		for(var j=0;j<indice_idiomas;j++){
			nombre_idioma = arr_idiomas_user[j].getAttribute("name");
			nombre_idioma_iso = arr_idiomas_user[j].getAttribute("isoName");
			obj_option = document.createElement("OPTION");
			obj_option.value = "idioma%3A"+escape_comillas_dobles+nombre_idioma_iso;
			obj_texto = document.createTextNode(nombre_idioma);
			obj_option.appendChild(obj_texto);
			obj_select.appendChild(obj_option);
		}
	}
	else{
		return;
	}


}

function OptionsPropiedades(){

	//Filtro de propiedades Ximtax
	var nombre_propiedad = '';
	var opciones_propiedades = ' ';
	var indice_prop = propiedades_user.length;
	var obj_select = $('propiedades_filtro');
	var obj_option;
	var obj_texto;
	var escape_comillas_dobles = escape('"');
	var select_propiedades = $('propiedades_filtro').innerHTML.length;
	if (indice_prop==0){
		obj_texto = document.createTextNode("No hay propiedades");
		obj_option = document.createElement("OPTION");
		obj_option.appendChild(obj_texto);
		obj_select.appendChild(obj_option);
		$('texto_filtro').disabled=true;
	}
	else{
		if(select_propiedades==0){
			for(var j=0;j<indice_prop;j++){
				nombre_propiedad = propiedades_user[j].getAttribute("name");
				nombre_propiedad = nombre_propiedad.replace(/ /g,'%20');
				obj_option = document.createElement("OPTION");
				obj_option.value = "ximtax_values%3A"+escape_comillas_dobles+nombre_propiedad+'%23%7C%23';
				obj_texto = document.createTextNode(propiedades_user[j].getAttribute("name"));
			}
		}
		else{
			return
		}
	}
}

//Segun la opcion del filtro solr seleccionado se muestran u ocultan las partes que nos interesan
function SelectFiltros(){

var valor_select = $('columnas_filtro').value;
switch (valor_select){
	case 'canales':
		OptionsCanales();
		$('contiene_filtro').disabled=true;
		$('columnas_filtro').style.top = "20%";
		$('canales_filtro').style.visibility = "visible";
		$('texto_filtro').style.visibility = "hidden";
		$('doc_publicado').style.visibility = "hidden";
		$('idiomas_filtro').style.visibility = "hidden";
		$('propiedades_filtro').style.visibility = "hidden";
	break;
	case 'idioma':
		OptionsLanguages();
		$('contiene_filtro').disabled=true;
		$('columnas_filtro').style.top = "20%";
		$('texto_filtro').style.visibility = "hidden";
		$('canales_filtro').style.visibility = "hidden";
		$('doc_publicado').style.visibility = "hidden";
		$('idiomas_filtro').style.visibility = "visible";
		$('propiedades_filtro').style.visibility = "hidden";
	break;
	case 'publicado':
		$('contiene_filtro').disabled=true;
		$('columnas_filtro').style.top = "20%";
		$('texto_filtro').style.visibility = "hidden";
		$('canales_filtro').style.visibility = "hidden";
		$('doc_publicado').style.visibility = "visible";
		$('idiomas_filtro').style.visibility = "hidden";
		$('propiedades_filtro').style.visibility = "hidden";
	break;
	case 'propiedades':
		OptionsPropiedades();
		$('contiene_filtro').disabled=true;
		$('columnas_filtro').style.top = "5%";
		$('propiedades_filtro').style.visibility = "visible";
		$('texto_filtro').style.visibility =  "visible";
		$('canales_filtro').style.visibility = "hidden";
		$('doc_publicado').style.visibility = "hidden";
		$('idiomas_filtro').style.visibility = "hidden";
	break;
	//Filtro de busqueda por valores que no necesitan de select o radio buttoms
	default:
		$('contiene_filtro').disabled=false;	
		$('columnas_filtro').style.top = "20%";
		$('texto_filtro').style.visibility = "visible";
		$('doc_publicado').style.visibility = "hidden";
		$('idiomas_filtro').style.visibility = "hidden";
		$('propiedades_filtro').style.visibility = "hidden";
	
}

}
function ConfiguradorFiltro(){
	
	var indice = columnas_user.length;
	var texto ='';
	var arr_tipo_nodos = recopilar_tipo_nodo(0,0,0,3);
 	var sw_solr = NodosDocumentos(arr_tipo_nodos);
	//Elementos para la busqueda solr están ocultos hasta que son seleccionados por la opción Select de Columna
		var radio_publicado = "<span id='doc_publicado' style='visibility:hidden;position:absolute;left:50%;'><input type='radio' name='publicado_filtro' value='Si' checked>Si<input type='radio' name='publicado_filtro' value='No'>No</span>"
		var select_idiomas = "<select name='idiomas_filtro' id='idiomas_filtro' style='visibility:hidden;position:absolute;left:48%;width:140px'></select>";
		var select_ximtax = "<br><select name='propiedades_filtro' id='propiedades_filtro' style='position:relative;top:15%;visibility:hidden;'></select>";
		var select_canales = "<select name='canales_filtro' id='canales_filtro' style='visibility:hidden;position:absolute;left:47%;width:140px'></select>";
	//Fin elementos para la busqueda de solr
	$("config_filtros").style.visibility="visible";
	OcultarTiposNodo();
	OcultarColumnas();
	//sw para comprobar que los tipos de nodos seleccionados son filtrables por solr o no
	
	if ($("config_filtros_ximnews")){
		OcultarFiltroXimnews();
	}
	texto = "<table class='tabla' width='100%' onmouseover='cambiarPuntero(this)' onmousedown='dragStart(event,'config_filtros');><tr><td class='filacerrar'><a onclick='OcultarFiltros();' class='filacerrar' style='cursor:pointer'><b>Cerrar Ventana</b>&nbsp;<img src='"+urlroot+"/xmd/images/botones/cerrar.gif' alt='' border='0'></a></td></tr></table>";
	if(indice>0){
		texto += "<div align='center' id='contenedor_filtros' style='position:relative;top:5px;left:7px;width:582px; /height:150px; min-height: 150px; float: left; margin-bottom: 15px; background-color:#FFFFFF;'>";
		texto += "<div style='width:570px;'>";
		texto += "<table id='filtrador' align='center' width='100%' class='tabla'>";
		texto += "<tbody>";
		texto += "<tr><td colspan=5 class='cabeceratabla'>Filtros de Búsqueda</td></tr>";
		texto += "<tr><td  class='filaoscuranegritac'>Columna</td><td class='filaoscuranegritac'>Tipo</td><td class='filaoscuranegritac'>Valor Búsqueda</td><td  class='filaoscuranegritac'></td><td  class='filaoscuranegritac'></td></tr>";
		texto += "<tr id='elementosFiltro'>";
		texto += "<td><select name='columnas_filtro' id='columnas_filtro' onchange='SelectFiltros();' style='position:relative;top:20%;width:140px'>";
		//si el tipo de nodos es solo documentos se configura para el solr
		if (sw_solr){
			texto += "<option value='documento%3A'>Nombre</option>";
			texto += "<option value='canales'>Canales</option>";
			texto += "<option value='idioma'>Idioma</option>";
			texto += "<option value='nodeid%3A'>Id. del Documento</option>";
			texto += "<option value='parentnodeid%3A'>Id. del Contenedor</option>";
			texto += "<option value='propiedades'>Propiedades</option>";
			texto += "<option value='proyecto%3A'>Proyecto</option>";
			texto += "<option value='publicado'>Publicado</option>";
			texto += "<option value='servidor%3A'>Servidor</option>";
			texto += "<option value='subversion%3A'>Subversion</option>";
			texto += "<option value='tipo_documento%3A'>Tipo de documento</option>";
			texto += "<option value='version%3A'>Versión</option>";
			texto += "</select>"+select_ximtax+"</td>";
			texto += "<td><select name='contiene_filtro' id='contiene_filtro'>";
			//El select se completa con la funcion HTMLSelectContiene();
			texto += "</select></td>";
			
		}
		else{
			for(var i=0;i<indice;i++){
				if(columnas_user[i].getAttribute("filtrable")==1){
					texto += "<option value="+i+">"+columnas_user[i].getAttribute("description")+"</option>";
				}
				
			}
			//Se concatenan las propiedades
			texto += "</select></td>";
			texto += "<td><select name='contiene_filtro' id='contiene_filtro'>";
				//el select se completa con la funcion HTMLSelectContiene();
			texto += "</select></td>";

		}

	texto += "<td><input type='text' name='texto_filtro' id='texto_filtro'>"+select_idiomas+radio_publicado+select_canales+"</td>";
	texto += "<td><input type='button' onclick='anyadir_filtro(this,"+sw_solr+")' class='boton' style='width: 25px;' value=' + ' title='Añadir'></td>";
	texto += "<td><input type='radio' name='operador_filtro' value='AND' checked>Cumplir todas<br>";
	texto += "<input type='radio' name='operador_filtro' value='OR'>Cumplir algunas</td>";
	texto += "</tr>";
	//Fila vacia para que el ultimo hijo inicial no sea la parte de formularios RESPETAR EL NUMERO DE TD'S
	texto += "<tr><td></td><td></td><td></td><td></td><td></td></tr>"
	texto += "</tbody>";
	texto += "</table>";
	texto += "</div><br/>";
	texto += "<div style='overflow-y:auto;position:relative;width:570px;height:150px;' class='tabla'>";
	texto += "<table align='center' id='filtros_resultados' width='100%'>";
	texto += "<tr><td colspan=4 class='cabeceratabla'>Filtros a Buscar</td></tr>";
	texto += "<tr><td class='filaoscuranegritac'>Columna</td><td class='filaoscuranegritac'>Tipo</td><td class='filaoscuranegritac'>Valor Búsqueda</td><td class='filaoscuranegritac'></td></tr>";
	if (html_filtro==''){
		texto += "<tr width='100%'><td width='30%'></td><td width='30%'></td><td width='30%'></td><td width='10%'></td></tr>";	
	}
	texto += html_filtro;
	texto += "</table>";
	texto += "</div>";
	texto += "</div><br/>";
	texto += "<table width='100%' style='float: left; clear: both;'>";
	texto += "<tr><td align='left'><img src='"+urlroot+"/xmd/images/botones/limpiar.gif' alt='Buscar' border='0' onclick='EliminarFiltro();' style='cursor:pointer;'/></td>";
	texto += "<td align='right'><img src='"+urlroot+"/xmd/images/browser/buscar.gif' alt='Buscar' border='0' onclick='filtros_busqueda_avanzada(1);' style='cursor:pointer;'/></td></tr>";
	}
	else{
		texto += "<p align='center'><b>No hay elementos que filtrar.</b>";
	}
	$("config_filtros").innerHTML=texto;
	HTMLSelectContiene();
	inicializar_form_filtro(sw_solr);
	
}
