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


var m_tipo;
var m_avanzada = "";
var servicios;
var servicios_o;
var l_servicios;

function coloca_serv(){
if(xmlSer){
	if (!servicios){setTimeout("coloca_serv()", 600);}
	else clasifica_servicio(xmlSer);
	}
}
// función para recuperar el servicio del archivo
function clasifica_servicio(xmlSer){
rootS = xmlSer.documentElement;
for (el = 0; el< rootS.childNodes.length; el++){
	if (rootS.childNodes.item(el).tagName == "formulario"){
		num_nodo = el;
		servicio = rootS.childNodes.item(el);
		if (!servicio.childNodes.item(0).childNodes.item(0).childNodes.item(0)) return;
		if (servicio.childNodes.item(0).childNodes.item(0).childNodes.item(0).childNodes) cs_e = servicio.childNodes.item(0).childNodes.item(0).childNodes.item(0).childNodes;
		
		for (l = 0; l < cs_e.length; l++){
			if (cs_e(l).tagName == "elemento"){
		
					Enodo = cs_e[l];
					nueva_opcion = new Option();
					nueva_opcion.value = Enodo.getAttribute("nombre_campo");
					nueva_opcion.text =  Enodo.getAttribute("etiqueta");
					form_final.campo.add(nueva_opcion);
					
					nueva_opcion = new Option();
					nueva_opcion.value = Enodo.getAttribute("descripcion_campo");
					nueva_opcion.text = Enodo.getAttribute("descripcion_campo");
					form_final.descripcion_campo.add(nueva_opcion);
					
					nueva_opcion = new Option();
					nueva_opcion.value = Enodo.getAttribute("clase");
					nueva_opcion.text = Enodo.getAttribute("clase");
					form_final.estilo.add(nueva_opcion);
					
					nueva_opcion = new Option();
					nueva_opcion.value = Enodo.getAttribute("tipo");
					nueva_opcion.text = Enodo.getAttribute("tipo");
					form_final.tipo.add(nueva_opcion);
					
					nueva_opcion = new Option();
					nueva_opcion.value = Enodo.getAttribute("validacion");
					nueva_opcion.text = Enodo.getAttribute("validacion");
					form_final.validacion.add(nueva_opcion);
					
					nueva_opcion = new Option();
					nueva_opcion.value = Enodo.getAttribute("error");
					nueva_opcion.text = Enodo.getAttribute("error");
					form_final.error.add(nueva_opcion);
					
					nueva_opcion = new Option();
					nueva_opcion.value = Enodo.getAttribute("dinamico_variable");
					nueva_opcion.text = Enodo.getAttribute("requerido");
					form_final.requerido.add(nueva_opcion);
					
					// valores ocultos -> dinamico_variable
					nueva_opcion = new Option();
					nueva_opcion.value =  Enodo.getAttribute("dinamico_propiedad");
					nueva_opcion.text = Enodo.getAttribute("dinamico_etiqueta");
					form_final.dinamico.add(nueva_opcion);
					
					nueva_opcion = new Option();
					nueva_opcion.value = Enodo.getAttribute("filtro");
					nueva_opcion.text = Enodo.getAttribute("avanzada");
					form_final.dinamico2.add(nueva_opcion);
					
					nueva_opcion = new Option();
					if (Enodo.getAttribute("elemento_valor")){
						nueva_opcion.value = Enodo.getAttribute("elemento_valor");
						nueva_opcion.text =Enodo.getAttribute("elemento_valor");
					}
					else{
						nueva_opcion.value = "";
						nueva_opcion.text = "";
					}
					form_final.elemento_valor.add(nueva_opcion);
				}
			else{
				posiciones[l] = cs_e(l).xml;
				ultimo_servicio = l;
				}
			}
			formulario.tipo_formulario.value = rootS.childNodes.item(el).childNodes.item(0).getAttribute("tipo_formulario");
			formulario.tipo_formulario.disabled = true;
			cambia_tipo(formulario.tipo_formulario);
		}
	}
}


function ejecuta_lectura(xmlDoc){
	
}

function inserta_servicio(xmlDoc){

	debug = 0;
	if( xmlDoc == null || xmlDoc.documentElement == null)
		alert('error al leer el archivo:');
	else
		{
		root = xmlDoc.documentElement;
		cs = root.childNodes;
		m_tipo = root.getAttribute("tipo");
		m_avanzada = root.getAttribute("avanzada");
		formulario.nombre_servicio.value = cs[0].childNodes(0).nodeValue;
		formulario.nombre_manejador.value = cs[1].childNodes(0).nodeValue;
		formulario.nombre_formulario.value = cs[2].childNodes(0).nodeValue;
		formulario.clase_formulario.value = cs[3].childNodes(0).nodeValue;
		if (cs[4].childNodes.item(0).childNodes){
			if (cs[4].childNodes.item(0).childNodes.item(0)) formulario.resultados_dato_pagina.value = cs[4].childNodes.item(0).childNodes.item(0).nodeValue;
			if (cs[4].childNodes.item(0).getAttribute("funcionario")) { formulario.resultados_dato_enlacef.value = cs[4].childNodes.item(0).getAttribute("funcionario");}
			else {formulario.resultados_dato_enlacef.value = "";}
			if (cs[4].childNodes.item(0).getAttribute("laboral")){ formulario.resultados_dato_enlacel.value = cs[4].childNodes.item(0).getAttribute("laboral");}
			else {formulario.resultados_dato_enlacel.value = "";}
			}
		if (cs[4].childNodes.item(1).childNodes){ formulario.resultados_identificador.value = cs[4].childNodes.item(1).text;}
		//formulario.resultados_pagina.value = cs[2].childNodes.item(2).childNodes(0).nodeValue;
		if (cs[5].childNodes) formulario.servicio_detalle_variable.value = cs[5].text;
		

		servicios = cs[6].childNodes;
		servicios_o = servicios;
		}
}

function cambia_tipo(objeto){
	limpia_combo(elform.elselect);
	tipo = objeto.value;
	if (tipo == "") return;
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

function pasa_todos(objeto){
	valor = objeto.length
	for (x= 0; x < valor; x++){

		objeto.options[x].selected = true;
		pasa_valores(objeto, x);
		inserta_valores();
	}
}

function pasa_valores(objeto, objeto2){

	if (!objeto2)
	{
		pos_seleccionado = -1;
		for (i=0;i<=objeto.length-1;i++){
			if (objeto.options[i].selected)
					pos_seleccionado = i;
			}
		if (pos_seleccionado == -1){
			alert("debe seleccionar un valor");
			return;
		}
	}
	else pos_seleccionado = objeto2;
	modifica_valores.campo.value = objeto.options[pos_seleccionado].text;
	modifica_valores.nombre_campo.value = objeto.options[pos_seleccionado].text;
	for (n=0; n < l_servicios; n++){
		servicio = servicios.item(n).childNodes;
		if (servicio[0].childNodes(0).nodeValue == objeto.options[pos_seleccionado].text){
		my_v = n;
			/// elementos para la entrada
			if (servicios.item(n).getAttribute("entrada") == "si" && formulario.tipo_formulario.value == "entrada"){
				modifica_valores.dinamico_filtrado.value = servicios.item(n).getAttribute("filtro")
				if (servicios.item(n).getAttribute("avanzada")) modifica_valores.avanzada.value = servicios.item(n).getAttribute("avanzada");
				else modifica_valores.avanzada.value = "";
				
				limpia_combo(modifica_valores.validacion);
				limpia_combo(modifica_valores.tipo_entrada);
				modifica_valores.error_servidor.value = servicios.item(my_v).getAttribute("errorservidor");
				modifica_valores.requerido.value = servicios.item(my_v).getAttribute("requerido");
				modifica_valores.tipo_entrada.disabled = false;
				modifica_valores.estilo_campo.disabled = false;
				modifica_valores.validacion.disabled = false;
				modifica_valores.error_servidor.disabled = true;
				modifica_valores.requerido.disabled = true;
				for (i = 0; i<servicio.length; i++){
					switch (servicio[i].tagName)
						{
						case "elemento_nombre":
							if (servicio[i].getAttribute("descripcion")) modifica_valores.descripcion_campo.value = servicio[i].getAttribute("descripcion");
							else modifica_valores.descripcion_campo.value = "";
							if (servicio[i].getAttribute("valor")) modifica_valores.elemento_valor.value = servicio[i].getAttribute("elemento_valor");
							else modifica_valores.descripcion_campo.value = "";
							break;
						case "elemento_validaciones":
							for (l=0; l < servicio[i].childNodes.length; l++){
								
								nueva_opcion = new Option();
								nueva_opcion.value = servicio[i].childNodes.item(l).childNodes(0).nodeValue;
								nueva_opcion.text = servicio[i].childNodes.item(l).childNodes(0).nodeValue;
								if (formulario.tipo_formulario.value == "entrada") modifica_valores.validacion.add(nueva_opcion);
							}
							break;
						case "elemento_tipo_entradas":
							for (l=0; l < servicio[i].childNodes.length; l++){
								
								nueva_opcion = new Option();
								nueva_opcion.value = servicio[i].childNodes.item(l).childNodes(0).nodeValue;
								nueva_opcion.text = servicio[i].childNodes.item(l).childNodes(0).nodeValue;
								if (formulario.tipo_formulario.value == "entrada") modifica_valores.tipo_entrada.add(nueva_opcion);
							}
							break;
						case "elemento_dinamico":
								if (formulario.tipo_formulario.value == "entrada"){
									if(servicio[i].childNodes.item(0).childNodes(0)) modifica_valores.dinamico_variable.value = servicio[i].childNodes.item(0).childNodes(0).nodeValue;
									if(servicio[i].childNodes.item(1).childNodes(0)) modifica_valores.dinamico_propiedad.value = servicio[i].childNodes.item(1).childNodes(0).nodeValue;
									if(servicio[i].childNodes.item(2).childNodes(0)) modifica_valores.dinamico_etiqueta.value = servicio[i].childNodes.item(2).childNodes(0).nodeValue;
									}
							break;
						}
					}
				
				return;
				}
			else if (servicios.item(n).getAttribute("lista") == "si" && formulario.tipo_formulario.value == "lista"){
				if (servicios.item(n).getAttribute("avanzada")) modifica_valores.avanzada.value = servicios.item(n).getAttribute("avanzada");
				else modifica_valores.avanzada.value = "";
				modifica_valores.tipo_entrada.disabled = true;
				modifica_valores.estilo_campo.disabled = true;
				modifica_valores.validacion.disabled = true;
				modifica_valores.error_servidor.disabled = true;
				modifica_valores.requerido.disabled = true;
				return;
				}
			else if (servicios.item(n).getAttribute("detalle") == "si" && formulario.tipo_formulario.value == "detalle"){
				if (servicios.item(n).getAttribute("avanzada")) modifica_valores.avanzada.value = servicios.item(n).getAttribute("avanzada");
				else modifica_valores.avanzada.value = "";
				for (i = 0; i<servicio.length; i++){
					switch (servicio[i].tagName)
						{
						case "elemento_tipo_entradas":
							for (l=0; l < servicio[i].childNodes.length; l++){
								if (servicio[i].childNodes.item(l).childNodes(0).nodeValue == "casilla"){
								nueva_opcion = new Option();
								nueva_opcion.value = servicio[i].childNodes.item(l).childNodes(0).nodeValue;
								nueva_opcion.text = servicio[i].childNodes.item(l).childNodes(0).nodeValue;
								modifica_valores.tipo_entrada.add(nueva_opcion);
								}
							}
							break;
						}
				}
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
		nueva_opcion.value = modifica_valores.descripcion_campo.value;
		nueva_opcion.text = modifica_valores.descripcion_campo.value;
		form_final.descripcion_campo.add(nueva_opcion);
		
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
		
		// valores ocultos -> dinamico_variable
		nueva_opcion = new Option();
		nueva_opcion.value = modifica_valores.dinamico_propiedad.value;
		nueva_opcion.text = modifica_valores.dinamico_etiqueta.value;
		form_final.dinamico.add(nueva_opcion);
		
		nueva_opcion = new Option();
		nueva_opcion.value = modifica_valores.dinamico_filtrado.value;
		nueva_opcion.text = modifica_valores.avanzada.value;
		form_final.dinamico2.add(nueva_opcion);
		
		modifica_valores.campo.value = "";
		modifica_valores.nombre_campo.value = "";
		modifica_valores.avanzada.value = "";
		if (modifica_valores.dinamico_variable) modifica_valores.dinamico_variable.value = "";
		limpia_combo(modifica_valores.tipo_entrada);
		limpia_combo(modifica_valores.validacion);
		modifica_valores.dinamico = "";
		modifica_valores.descripcion_campo.value = "";
		
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
		form_final.dinamico.options[i].selected = true;
		form_final.dinamico2.options[i].selected = true;
		form_final.descripcion_campo.options[i].selected = true;
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
	modifica_valores.avanzada.value = form_final.dinamico2.options[ultima_posicion].text;
	modifica_valores.descripcion_campo.value = form_final.descripcion_campo.options[ultima_posicion].text;
	form_final.campo.options[ultima_posicion] = null;
	form_final.tipo.options[ultima_posicion] = null;
	form_final.estilo.options[ultima_posicion] = null;
	form_final.validacion.options[ultima_posicion] = null;
	form_final.error.options[ultima_posicion] = null;
	form_final.requerido.options[ultima_posicion] = null;
	form_final.dinamico.options[ultima_posicion] = null;
	form_final.dinamico2.options[ultima_posicion] = null;
}
function borra_valor(){
	form_final.campo.options[ultima_posicion] = null;
	form_final.tipo.options[ultima_posicion] = null;
	form_final.estilo.options[ultima_posicion] = null;
	form_final.validacion.options[ultima_posicion] = null;
	form_final.error.options[ultima_posicion] = null;
	form_final.requerido.options[ultima_posicion] = null;
	form_final.dinamico.options[ultima_posicion] = null;
	form_final.dinamico2.options[ultima_posicion] = null;
	form_final.descripcion_campo.options[ultima_posicion] = null;
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
				moveup(form_final.dinamico, pos_seleccionado);
				moveup(form_final.dinamico2, pos_seleccionado);
				moveup(form_final.descripcion_campo, pos_seleccionado);
				}
			if (modo=="baja"){
				movedown(form_final.campo, pos_seleccionado);
				movedown(form_final.tipo, pos_seleccionado);
				movedown(form_final.estilo, pos_seleccionado);
				movedown(form_final.validacion, pos_seleccionado);
				movedown(form_final.error, pos_seleccionado);
				movedown(form_final.requerido, pos_seleccionado);
				movedown(form_final.dinamico, pos_seleccionado);
				movedown(form_final.dinamico2, pos_seleccionado);
				movedown(form_final.descripcion_campo, pos_seleccionado);
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
		limpia_combo(form_final.dinamico);
		limpia_combo(form_final.dinamico2);
		limpia_combo(form_final.descripcion_campo);
		}
	}
	
function convierte_formfinal(){
//	alert(xmlSer);
	var xmlDoc2 = new ActiveXObject("Msxml2.DOMDocument");
	xmlDoc2.async = true;
	xmlDoc2.resolveExternals = true;

	xmlDoc2.appendChild(xmlDoc2.createElement("formulario"));
	root = xmlDoc2.documentElement;
	//nombre del servicio
	newNode = xmlDoc2.createNode(1, "servicio", "");
	newNode.setAttribute("tipo_servicio", m_tipo);
	if (m_avanzada) newNode.setAttribute("avanzada", m_avanzada);
	newNode.setAttribute("nombre", formulario.nombre_servicio.value);
	newNode.setAttribute("manejador", formulario.nombre_manejador.value);
	newNode.setAttribute("resultados_dato_enlace", formulario.resultados_dato_pagina.value);
	newNode.setAttribute("resultados_dato_enlace_f", formulario.resultados_dato_enlacef.value);
	newNode.setAttribute("resultados_dato_enlace_l", formulario.resultados_dato_enlacel.value);
	newNode.setAttribute("resultados_identificador", formulario.resultados_identificador.value);
	newNode.setAttribute("resultados_pagina", formulario.resultados_pagina.value);
	newNode.setAttribute("servicio_detalle_variable", formulario.servicio_detalle_variable.value);
	newNode.setAttribute("tipo_formulario", formulario.tipo_formulario.value);
	newNode.setAttribute("clase_formulario", formulario.clase_formulario.value);
	newNode.setAttribute("nombre_formulario", formulario.nombre_formulario.value);
	root.appendChild(newNode);

	newNode = xmlDoc2.createNode(1, "elementos", "");
	root.childNodes(root.childNodes.length-1).appendChild(newNode);
		
	newNode = xmlDoc2.createNode(1, "cuerpo_elementos", "");
	root.childNodes(root.childNodes.length-1).childNodes(0).appendChild(newNode);
	
	cuerpo = root.childNodes(root.childNodes.length-1).childNodes(0).childNodes;
	longitud = form_final.campo.length;
	for (n = 0; n < longitud; n++){
		if (posiciones[n]){
			var temporal = new ActiveXObject("Msxml2.DOMDocument");
			temporal.async = true;
			temporal.resolveExternals = true;
			temporal.loadXML(posiciones[n]);
			cuerpo[0].appendChild(temporal.childNodes.item(0));
			}
			newNode = xmlDoc2.createNode(1, "elemento", "");
			if (n == 0 && formulario.tipo_formulario.value == "lista") newNode.setAttribute("enlazado", "si");
			newNode.setAttribute("nombre_campo", form_final.campo[n].value);
			newNode.setAttribute("etiqueta", form_final.campo[n].text);
			newNode.setAttribute("tipo", form_final.tipo[n].value);
			newNode.setAttribute("clase", form_final.estilo[n].value);
			newNode.setAttribute("validacion", form_final.validacion[n].value);
			newNode.setAttribute("error", form_final.error[n].value);
			newNode.setAttribute("validacion", form_final.validacion[n].value);
			newNode.setAttribute("requerido", form_final.requerido[n].text);
			newNode.setAttribute("dinamico_variable", form_final.requerido[n].value);
			newNode.setAttribute("dinamico_propiedad", form_final.dinamico[n].value);
			newNode.setAttribute("dinamico_etiqueta", form_final.dinamico[n].text);
			newNode.setAttribute("filtro", form_final.dinamico2[n].value);
			newNode.setAttribute("avanzada", form_final.dinamico2[n].text);
			if (form_final.descripcion_campo[n].text != "") newNode.setAttribute("descripcion_campo", form_final.descripcion_campo[n].text);
			else newNode.setAttribute("descripcion_campo", "n");
			
			cuerpo[0].appendChild(newNode);
	}
	if (n < ultimo_servicio){
		for (n = n; n <= ultimo_servicio; n++){
			if (posiciones[n]){
				var temporal = new ActiveXObject("Msxml2.DOMDocument");
				temporal.async = true;
				temporal.resolveExternals = true;
				temporal.loadXML(posiciones[n]);
				cuerpo[0].appendChild(temporal.childNodes.item(0));
				}
			} 
		}
	
	
	//root.appendChild(newNode);
	var arr = new Array();
				if(xmlSer) {
					xmlSer.childNodes.item(2).childNodes.item(num_nodo).removeChild(xmlSer.childNodes.item(2).childNodes.item(num_nodo).childNodes.item(0));
					xmlSer.childNodes.item(2).childNodes.item(num_nodo).removeChild(xmlSer.childNodes.item(2).childNodes.item(num_nodo).childNodes.item(0));
					xmlSer.childNodes.item(2).childNodes.item(num_nodo).appendChild(root.childNodes.item(0));
					newNode = xmlDoc2.createNode(1, "boton_editar_formulario", "");
					newNode.setAttribute("servicio_id", elnodo);
					xmlSer.childNodes.item(2).childNodes.item(num_nodo).appendChild(newNode);
					arr["texto"] = xmlSer.xml;
					}
				else arr["texto"] = xmlDoc2.xml;
				window.returnValue = arr;
				window.close();
}