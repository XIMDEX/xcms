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


var nombre_nodo_sel = '';
var id_nodo_sel = 0;
var area3H;
var ventana_aux = "";

function iniciar(nodoID,userID,ximnews)
{
 redimension();
 cambiar_num_pag();

 var arrtipos = recopilar_tipo_nodo(nodoID,userID,0,2);

 if (arrtipos.indexOf('XimNewsNewLanguage') <0 && arrtipos.length==1){
	ximnews=1;
 }
 else{
	 ximnews=0;
 }

return false;
 buscar_documentos_inicio(nodoID,userID,ximnews);
 $('texto_buscar').onblur=ocultar_desplegable_blur;
 if(ximnews==1) {
	 minima_fecha();
 }
}

function redimension()
{

 var br_iz=$("browser_iz");
 var br_der=$("browser_der");
 var br_inter=$("browser_intermedio");
 var br_cuerpo=$('browser_cuerpo');
 var br_acciones=$('acciones');
 var br_filtros=$('filtros');
 //en panel "browser_medio_btn" está al 11%

 my_winH = document.body.offsetHeight;
 my_winW = document.body.offsetWidth;	

 var area1dH;
 var area2dH;
// var area3dH;
 var area3W;
 var br_inL;
 var br_inT;
 
 if(window.ActiveXObject)
 {
	 area1dH=230;
	 area2dH=160;
	 area3dH=200;
	 area3W=parseInt(my_winW/2);
	 br_inL=parseInt(my_winW/4);
	 br_inT=parseInt(my_winH/3);
 }
 else
 {
	 area1dH=310;
	 area2dH=240;
	 area3dH=200;
	 area3W=parseInt(my_winW/2);
	 br_inL=parseInt(my_winW/4);
	 br_inT=parseInt(my_winH/3);

	 var area2W=parseInt(my_winW/2)-98;
   	 $("area2").style.width = area2W;
	 br_cuerpo.style.width=parseInt(my_winW);
	 br_acciones.style.width=parseInt(my_winW);
 	 br_filtros.style.width=parseInt(my_winW);

  }
 area3H=my_winH - area3dH;

 $("area1").style.height = my_winH - area1dH-15;
 $("area2").style.height = my_winH - area2dH;
 $("area3").style.height = area3H ;
 $("area3").style.width = area3W ;
// br_inter.style.left = br_inL;
 //br_inter.style.top = br_inT; 
}

function minima_fecha()
{
 dm=valor_min_dia();
 if(dm.substring(0,1)=='0')
 {
  dm=dm.substring(1);
 }
 mm=valor_min_mes();
 if(mm.substring(0,1)=='0')
 {
  mm=mm.substring(1);
 } 
 $('seleccionaDiaDesde').selectedIndex=parseInt(dm);
  $('seleccionaMesDesde').selectedIndex=parseInt(mm);

 var min_anio=valor_min_anio();
 var combo_anio= $('seleccionaAnyoDesde');

 for(var i=0;i<combo_anio.length;i++)
 {
	  if(combo_anio[i].innerHTML==min_anio){
	  	combo_anio.selectedIndex=i;
	  }
 }
}
//PANELES DE SELECCION DEL BROWSER
function cambiar_panel(ventana)
{

 if(ventana.id == "undefined") return false;

//  var acciones = parent.parent.frames['toolbar'].document.getElementsByTagName('IMG');

 if(ventana.id=="browser_iz")
 {
	 var panel = document.getElementById("panel_iz");
	ventana.className="browser_lateral_sel";
	parent.parent.frames['toolbar'].borraBotones();
	$('browser_der').className="browser_lateral";
	$('panel_iz').className="panel_sel";
 	$('panel_der').className="panel";
	ventana_aux = ventana_seleccionada();
 }
 else 
 {
	 var panel = document.getElementById("panel_der");
	 ventana.className="browser_lateral_sel";
	$('browser_iz').className="browser_lateral";
 	$('panel_der').className="panel_sel";
	$('panel_iz').className="panel";
	//habilitar_botones();
 }
	
	var ventana= ventana_seleccionada();
	if (ventana=="area2" && ventana_aux != ventana){
		ventana_aux = ventana_seleccionada();
		Obtener_Menu("area2",1);
	}
}

function habilitar_botones()
{

	var clase=$('browser_der').className;
	
	if(clase=="browser_lateral_sel")
	{
		var elementos=$('area2').getElementsByTagName('INPUT');
		//var acciones=$('acciones').getElementsByTagName('IMG');
		var acciones = parent.parent.frames['toolbar'].document.getElementsByTagName('IMG');
		var limite_acciones = acciones.length;
		if(elementos.length>0)
		{
			for(i=0;i<limite_acciones;i++)
			{
				if(acciones[i].name == 'acciones_browser'){
					acciones[i].style.visibility="visible";
				}
			}
		}
	}
}

function ventana_seleccionada()
{
	 if ( $('panel_iz').className=="panel_sel"){
		 return "area1";
	 }
	 else{
		 //borrar_seleccionado()
		 return "area2";
	 }
}

//SOLAPAS AREA INTERMEDIA
function cambiar_solapa(solapa)
{
	var padre=solapa.parentNode;
	var longitud_padre = padre.childNodes.length;
	for(i=0;i<longitud_padre;i++)
	{
		var solapa_id=padre.childNodes[i].id;
		var cad=new Array();
		cad=solapa_id.split("solapa_");
		var clase="";
		clase=cad[1];
		var panel=$(clase);

		if(padre.childNodes[i]==solapa){
			padre.childNodes[i].className="solapa_activa";
			panel.className="ficha_activa";
		}
		else{
			padre.childNodes[i].className="solapa";
			panel.className="ficha_oculta";
		}

	}
}

//BOTONES DEL BROWSER
function browser_boton_over(boton){
boton.className="browser_boton_over";
}

function browser_boton_out(boton){
boton.className="browser_boton";
}

//EVENTOS DE BOTONES AREA 1
function quitar_seleccion_btn()
{
 var contiene_tabla=$('area1');
 if (contiene_tabla.firstChild.firstChild!=null)
 {
	var cuerpo_tabla=contiene_tabla.firstChild.firstChild;
	var filas= cuerpo_tabla.childNodes.length;
	for(var i=1;i<filas;i++) //i=1, no incluimos la cabecera de tabla
	{
	   cuerpo_tabla.childNodes[i].className="doc";
	}
 }
}

function anyadir_todo_btn()
{
 //combinacion de seleccionar todo y añadir todo
 $('tabla_nodos').style.width='100%';
 $('area1').style.width='100%';
 seleccionar_todo_btn();
 add_seleccion_btn();
 quitar_seleccion_btn();
}

function seleccionar_todo_btn()
{
 var contiene_tabla=$('area1');
 if (contiene_tabla.firstChild.firstChild!=null)
 {
	var cuerpo_tabla=contiene_tabla.firstChild.firstChild;
	var filas= cuerpo_tabla.childNodes.length;
	for(var i=1;i<filas;i++) //i=1, no incluimos la cabecera de tabla
	{
	  cuerpo_tabla.childNodes[i].className="doc_sel";
	}
 }
}

function invertir_seleccion_btn()
{
 var contiene_tabla=$('area1');
 if (contiene_tabla.firstChild.firstChild!=null)
 {
	var cuerpo_tabla=contiene_tabla.firstChild.firstChild;
	var filas= cuerpo_tabla.childNodes.length;
	var clase;
	var clase_inversa;
	for(var i=1;i<filas;i++) //i=1, no incluimos la cabecera de tabla
	{
	   clase= cuerpo_tabla.childNodes[i].className;
	   
	   if( (clase=="doc_sel") || (clase=="doc_sel_ultimo") ) clase_inversa="doc";
	   else if (clase=="doc") clase_inversa="doc_sel";
	   cuerpo_tabla.childNodes[i].className=clase_inversa;
	}

 }
}

function _add_seleccion_btn()
{
 var tabla= document.getElementsByTagName("tabla_nodos");
 var cuerpo_tabla=tabla.getElementsByTagName("TBODY")[0];
 if (cuerpo_tabla!=null)
 {
	var filas=tabla.getElementsByTagName("TR");
	var limite_filas = filas.length;
	var clase;
	for(var i=1;i<limite_filas;i++) //i=1, no incluimos la cabecera de tabla
	{
	   clase= filas[i].className;
	   if( (clase=="doc_sel") || (clase=="doc_sel_ultimo") ){
			add_doc(filas[i],1);
	  }
	}

 }

// habilitar_botones(); //deshabilita botones de acciones
}

//EVENTOS DE ICONO AREA 1

function asociar_clic_filas(tabla)
{
	//se asocia a las filas de la tabla del area 1 el evento onclic
	var cuerpo_tabla=tabla.firstChild;
	if(cuerpo_tabla!=null)
	{
		var num_filas = cuerpo_tabla.childNodes.length;
		for(var i=1;i<num_filas;i++) //i=1, no incluimos la cabecera de tabla
		{
		   cuerpo_tabla.childNodes[i].onclick=capturar_multisel;
		}
	}

}
function EventoClick(e){

   switch (event.type) {
     case "click":
		 seleccion_doc(e);
		 //parent.parent.frames['toolbar'].borraBotones();
		 //Obtener_Menu("area2",1);
	 break;
     case "dblclick":
		 delete_doc(e);
 	 break;
	}
	 
}
function _add_doc(icono,botones)
{
	var area_destino=$('tabla_area2');
	var icono_temp = icono.cloneNode(true);
	var nodeID=icono_temp.getElementsByTagName("INPUT")[0].value;

	if(!existe_doc(nodeID))
	{
		//Al reducir el tamaño del TD se acorta el nombre, se guarda el title para mantener el nomhre completo
		icono_temp.getElementsByTagName("td")[1].innerText = icono_temp.getElementsByTagName("td")[1].title;
		for(var i=0;i<icono_temp.getElementsByTagName("td").length;i++){
			icono_temp.getElementsByTagName("td")[i].style.width='';			
		}
		icono_temp.setAttribute("ondblclick","delete_doc(this)");
		icono_temp.setAttribute("onclick","seleccion_doc(this)");


		icono_temp.className="doc";

		//eliminacion celdas solo se guardan el icono y el nombre
		for(var i=2;i<=cont_items;i++){
			icono_temp.removeChild(icono_temp.lastChild);
		}
		//comprobamos si existe TBODY
		var tbody=area_destino.getElementsByTagName("TBODY")[0];

		if( (tbody!=null)&&(tbody.tagName=="TBODY"))
		{
			tbody.appendChild(icono_temp);
		}
		else
		{
			var tbody=document.createElement("TBODY");
			tbody.appendChild(icono_temp);
			area_destino.appendChild(tbody);
		}
		var tr = tbody.getElementsByTagName("TR");
		tr[tr.length-1].onmousedown='';
		
	}
 $('area1').style.width='98%';
  $('area1').style.margin='1%';
}

function capturar_multisel(e)
{

	//captura de las teclas ctrl y mayus
	var evento = window.event ? window.event : e;
	var evento_elemento=window.event ? evento.srcElement : e.target;
	var fila;
	if (evento_elemento.tagName=="TD") fila=evento_elemento.parentNode;
	else if(evento_elemento.tagName=="TR") fila=evento_elemento;
	else if(evento_elemento.tagName=="IMG") fila=evento_elemento.parentNode.parentNode;
	var clase;
	if(evento.shiftKey)
	{
		seleccion_shift(fila);
	}	
	else if(evento.ctrlKey)
	{
		seleccion_ctrl(fila);
		clase = fila.className;
		if(clase=="doc") fila.className="doc_sel_ultimo";
		else if ( (clase=="doc_sel")||(clase=="doc_sel_ultimo") ) fila.className="doc";
	}
	else
	{
		 clase = fila.className;
		 quitar_seleccion(fila);
 		 if(clase=="doc") fila.className="doc_sel_ultimo";
		 else if  (clase=="doc_sel") fila.className="doc_sel_ultimo";
		 else if (clase=="doc_sel_ultimo")  fila.className="doc";
	}

}

function quitar_seleccion(fila)
{
	var cuerpo_tabla;
	
	cuerpo_tabla=fila.parentNode;

	if(cuerpo_tabla!=null)
	{
		var filas= cuerpo_tabla.childNodes.length;
		var primera_fila;
		if (cuerpo_tabla.childNodes[0].className=="filacampos")
		primera_fila=1;//i=1, no incluimos la cabecera de tabla
		else primera_fila=0;
		for(var i=primera_fila;i<filas;i++) 
		{
		   cuerpo_tabla.childNodes[i].className="doc";
		}

	}
}

function seleccion_shift(fila)
{
	var cuerpo_tabla=fila.parentNode;
	if(cuerpo_tabla!=null)
	{
		var sel_ultimo = buscar_sel_ultimo(fila);
		var sel_ahora= buscar_sel(fila);

		if(sel_ultimo != -1)
		{
			quitar_seleccion(fila);
			var fila_menor=Math.min(sel_ahora,sel_ultimo);
			var fila_mayor=Math.max(sel_ahora,sel_ultimo);
			
			for(var i=fila_menor;i<=fila_mayor;i++) //i=1, no incluimos la cabecera de tabla
			{
			   cuerpo_tabla.childNodes[i].className="doc_sel";
			}
			cuerpo_tabla.childNodes[sel_ultimo].className="doc_sel_ultimo";
		}
	}
}

function seleccion_ctrl(fila)
{
	//cambia la fila a doc_sel_ultimo como fila a doc_sel
	var cuerpo_tabla=fila.parentNode;
	var clase;
	if(cuerpo_tabla!=null)
	{
		var sel_ultimo = buscar_sel_ultimo(fila);
		if(sel_ultimo != -1)
		{
			cuerpo_tabla.childNodes[sel_ultimo].className="doc_sel";
		}
	}
}

function buscar_sel(fila)
{
	//busca la fila cuyo className="doc_sel_ultimo"
	var cuerpo_tabla=fila.parentNode;
	var fila_temp;
	if(cuerpo_tabla!=null)
	{
		var filas= cuerpo_tabla.childNodes.length;
		for(var i=1;i<filas ;i++) //i=1, no incluimos la cabecera de tabla
		{
		   fila_temp=cuerpo_tabla.childNodes[i];
		   if (fila_temp==fila) return i;
		}
	}
	return -1;
}


function buscar_sel_ultimo(fila)
{
	//busca la fila cuyo className="doc_sel_ultimo"
	var cuerpo_tabla=fila.parentNode;
	var clase;
	if(cuerpo_tabla!=null)
	{
		var filas = cuerpo_tabla.childNodes.length;
		for(var i=1;i<filas ;i++) //i=1, no incluimos la cabecera de tabla
		{
		   clase=cuerpo_tabla.childNodes[i].className;
		   if (clase=="doc_sel_ultimo") return i;
		}
	}
	return -1;
}

//EVENTOS DE ICONO AREA 2

function delete_doc(e)
{
	//var myEventObj = window.event ? window.event.srcElement : e.target;
	var myEventObj = e;
	
	var padre;
	var hijo;
	//parent.parent.frames['toolbar'].borraBotones();
	if(myEventObj.tagName=="TD")
	{
	 padre=myEventObj.parentNode.parentNode;
	 hijo=myEventObj.parentNode;
	} 
	else if (myEventObj.tagName=="TR")
	{
	 padre=myEventObj.parentNode;
	 hijo=myEventObj;
	}
	else if (myEventObj.tagName=="IMG")
	{
	 padre=myEventObj.parentNode.parentNode.parentNode;
	 hijo=myEventObj.parentNode.parentNode;
	}
	padre.removeChild(hijo);
	//setTimeout('Obtener_Menu("area2","add");',119);
	Obtener_Menu("area2",0);
	
	
}
function seleccion_doc(e)
{
	//var myEventObj = window.event ? window.event.srcElement : e.target;
	var myEventObj = e;
	var padre;
	var hijo;
	if (myEventObj.tagName=="TD") hijo=myEventObj.parentNode;
	else if(myEventObj.tagName=="TR") hijo=myEventObj;
	else if(myEventObj.tagName=="IMG") hijo=myEventObj.parentNode.parentNode;
	var clase=hijo.className;
	quitar_seleccion(hijo);
	if(clase=="doc"){ 
		hijo.className="doc_sel";
	}
	else if(clase=="doc_sel"){ 
		hijo.className="doc";
	}
	var inputs=hijo.getElementsByTagName("INPUT");
	id_nodo_sel = inputs[0].value;
	nombre_nodo_sel = hijo.innerText;
}

function borrar_seleccion()
{
	//borra todos los elementos del area 2
	var tabla=$('tabla_area2');
	var cuerpo_tabla=tabla.getElementsByTagName("TBODY")[0];
	if(cuerpo_tabla!=null)
	{
		var filas=tabla.getElementsByTagName("TR");
		var limite_filas= filas.length-1;
		for(var i=limite_filas;i>=0;i--)
		{
			filas[i].parentNode.removeChild(filas[i]);
		}
	}	
	Obtener_Menu("area2",'add');
}

function borrar_seleccionado()
{
	//borra todos los elementos seleccionados del area 2
	var tabla=$('tabla_area2');
	var cuerpo_tabla=tabla.getElementsByTagName("TBODY")[0];
	if(cuerpo_tabla!=null)
	{
		var filas= cuerpo_tabla.childNodes.length;
		for(var i=0;i<filas ;i++)
		{
		 if(cuerpo_tabla.childNodes[i]!=null)
		 {
		 	  if( (cuerpo_tabla.childNodes[i].className=="doc_sel")
		   || (cuerpo_tabla.childNodes[i].className=="doc_sel_ultimo") )
		   		cuerpo_tabla.removeChild(cuerpo_tabla.childNodes[i]);
		   }
		}
	}	
	Obtener_Menu("area2",'add');
	habilitar_botones(); //deshabilita botones de acciones
}

function existe_doc(nodeID_in)
{
	 //comprobar si el nodo existe ya en area 2
	 var tabla=$('tabla_area2');
	 var filas=tabla.getElementsByTagName("INPUT");
	 var limite_nodos = filas.length;
	 var nodeID;

	 for(var i=0;i<limite_nodos;i++)
	 {
		if ( ( filas[i].name=="nodeID_sel") &&(filas[i].value==nodeID_in) ){
		 return true;
		}
	 }
	 return false;
}

//PAGINADOR

function _paginador(numnodos,minimo,num_elementos)
{
	var html="";
	var n_paginas;
	var nodoID=valor_nodeID();
	var userID=valor_userID();
	var ximnews=valor_ximnews();
	if(numnodos>num_elementos)
	{
		html="<table id='paginador'><tr>";
		n_paginas=parseInt(numnodos/num_elementos);

		if ((n_paginas!=0)&&(numnodos%num_elementos)>0) {
			n_paginas++;	
		}
		
		var puntero=0;
		
		//escritura del enlace "anterior"
		
		if(minimo>0)
		{
			html=html+"<td class='pag'><a onclick='recopilar_filtros(";
			html=html+parseInt(minimo-num_elementos);
			html=html+",0)'>Anterior</a></td>";
		}
		
		//escritura de las paginas

		for(i=0;i<n_paginas;i++)
		{
			numero=n_paginas+1;
			
			min_envio=puntero;
			
			if ((puntero+num_elementos)>=numnodos) {
				max_envio=numnodos-1;
			}
			else {
				max_envio=puntero+num_elementos-1;	
			}
	
			//HTML
			if(min_envio==minimo)
			{
				html=html+"<td class='pag_sel'>"+parseInt(i+1)+"</a></td>";
			}
			else
			{
				html=html+"<td class='pag'><a onclick='recopilar_filtros(";
				html=html+min_envio;
				html=html+",0)'>"+parseInt(i+1)+"</a></td>";
			}
			puntero=max_envio+1;

		}
		
		//escritura del enlace "siguiente"
		
		if(minimo<numnodos-num_elementos)
		{
			var siguiente=parseInt(minimo)+parseInt(num_elementos);
			html=html+"<td class='pag'><a onclick='recopilar_filtros(";
			html=html+siguiente;
			html=html+",0)'>Siguiente</a></td>";
		}
		
		html=html+"</tr></table>";
	}
	
	return html;
}

function oculta_capas(capa,fecha,combo,caja,desde,hasta,categoria,busqueda){
	//$(capa).style.visibility= "hidden";
	if ($(fecha)){
		$(fecha).style.visibility= "hidden";
	    $(combo).style.visibility= "hidden";
		$(caja).style.visibility= "hidden";
		$(hasta).style.visibility= "hidden";
		$(categoria).style.visibility="hidden";
		$(busqueda).style.visibility="hidden";
		$(desde).style.visibility="hidden";
	}
}

function muestra_capas(capa){

	$(capa).style.visibility= "visible";

}
//posiciona la ventana cuando la tredimensionamos
function posiciona_ventana(){

	if ($('browser_intermedio')){
			$('browser_intermedio').style.left = "0px";
			$('browser_intermedio').style.top = "35px";
	}
	
	if ($('area31')){	
		$('area31').style.width = document.body.clientWidth;
	}

	$('browser_der').style.width="100%";
	$('actioncontainer').style.width = document.body.clientWidth;
	$('browser_iz').style.width= document.body.clientWidth/2;
	$('browser_cuerpo').style.width = document.body.clientWidth;

}

//Activa el checkbox si el usuario introduce algo en la caja de texto
function activa_check(IdPropiedad, IdName){
	if (IdName.value != ""){
		$(IdPropiedad).checked = true;
	}
	else{
		$(IdPropiedad).checked = false;
	}
}

