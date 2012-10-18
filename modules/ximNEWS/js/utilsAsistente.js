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


var enlace_video = false;
var enlace_archivo = false;
var propiedad = "undefined";

//Function which shows date while pressing the calendar images
 function cambiarFechaNuevo(id,e){
	 if(navigator.appName!="Microsoft Internet Explorer")
	 {
		 //Event=Event.target
	 	 var posX = e.pageX+document.body.scrollLeft;
		 var posY = e.pageY+document.body.scrollTop;

	 }else{
		 //Event=event.srcElement
		 var posX = event.x+document.body.scrollLeft;
		 var posY = event.y+document.body.scrollTop;
	 }
	   var obj = $(id);
	   showCalendar(obj, obj, "dd/mm/yyyy","es",1,posX,posY);
 }

//Function which calculate the current date for fields: creation date, init date and end date
function FechaActual(){
	var fecha=new Date();
	var dia=fecha.getDate();
	var mes;
	var mesactual = fecha.getMonth()+1;
	var ano=fecha.getFullYear();
	if (dia<10){
		dia = "0" + fecha.getDate();
	}
	else{
		dia = fecha.getDate();
	}
	if (mesactual<10 ){
		mes = "0" + mesactual;
	}
	else{
		mes = mesactual;
	}
	var momentoActual = new Date();
	var hora = momentoActual.getHours()
	var minuto = momentoActual.getMinutes()
	var segundo = momentoActual.getSeconds() 
	var fechahoy = dia+"/"+mes+"/"+ano;
	
	if(document.form_vista_global.fechainicionoticia.value==''){
		document.form_vista_global.fechainicionoticia.value = fechahoy;
	}
	if(document.form_vista_global.fechafinnoticia.value==''){
		document.form_vista_global.fechafinnoticia.value = fechahoy;
	}
	if(document.form_vista_global.cadenaFecha.value==''){
		document.form_vista_global.cadenaFecha.value = fechahoy;
	}
	
	//document.form_vista_global.fechainicio.value = "Click Aqui... 00:00:00";
	//document.form_vista_global.fechafin.value = "Click Aqui... 00:00:00";
	document.form_vista_global.es_noticia_fecha.value = fechahoy;
	
}

function adjuntar_enlace(enlace){
	var salto_de_linea = "<br>";
	var div_enlaces = $("contenedor_enlaces_anadidos");
	var texto_div = div_enlaces.innerHTML;
	if (texto_div.length==0){
		salto_de_linea = "";
	}
	var texto_final = texto_div + salto_de_linea + enlace;
	div_enlaces.innerHTML = texto_final;
	var inputhidden = crear_input_hidden("enlace_noticia",enlace);
	div_enlaces.appendChild(inputhidden);

}

function crear_input_hidden(nombre,valor){
	var elemento = document.createElement("input");
	elemento.type="hidden";
	elemento.name = nombre;
	elemento.value = valor;
	return elemento;
	
}

var nodoimagen,nodoimagen_aux,nodoenlace,nodoenlace_aux;

function valor_nombre_enlaces(){

var inputs = $("modulo_enlaces").getElementsByTagName("INPUT");
	var num_inputs = inputs.length;
	var valor
	for (var i=0; i<num_inputs; i++){
		if(inputs[i].type != "hidden"){
			valor = inputs[i].value;
		}
		else{
			inputs[i].value = valor;
		}
	}
return;
}
//When an images or a link is added, the forms are set up with empty fields, such as author, link description, etc
function inicializarFormulario(idDiv,numeroid){
	var nav;
	if(navigator.appName=="Microsoft Internet Explorer" || navigator.appName=="ie")
	{
		nav=true;	
	}
	else
	{
		nav=false;
	}
	var masterLanguage = GetMasterLanguage();
	//Form fields init
	//var inputs = document.getElementById(idDiv).getElementsByTagName("INPUT");
	var inputs = idDiv.getElementsByTagName("INPUT");
	var num_inputs = inputs.length;

	if (idDiv.id.indexOf("enlace")>0){
		//var textarea = document.getElementById(idDiv).getElementsByTagName("TEXTAREA");
		var textarea = idDiv.getElementsByTagName("TEXTAREA");
		var num_textarea = textarea.length;
		for (var i=0; i<num_textarea; i++){
			textarea[i].value='';
			var padre = textarea[i].parentNode;
			var nombre = 'links['+masterLanguage+']['+indice_enlaces+'][description]';
			var text_area = document.createElement("TEXTAREA");
			text_area.setAttribute("name",nombre);
			if (nav){
				text_area.setAttribute("className","input_big");
			}
			else{
				text_area.setAttribute("class","input_big");
			}

			padre.insertBefore(text_area,textarea[i]);
			padre.removeChild(textarea[i+1]);
		}
		for (var i=0; i<num_inputs; i++){
			var padre = inputs[i].parentNode;
			var pos = inputs[i].name.indexOf("[name]");
			var input = document.createElement("INPUT");
			if (pos>1){
				var nombre = 'links['+masterLanguage+']['+indice_enlaces+'][name]';
				input.setAttribute("type","hidden");
			}
			else{
				input.setAttribute("type","text");
				var nombre = 'links['+masterLanguage+']['+indice_enlaces+'][url]';
			}
			
			input.setAttribute("name",nombre);
			if (nav){
				input.setAttribute("className","input_big_enlace");
			}
			else{
				input.setAttribute("class","input_big_enlace");
			}

			padre.insertBefore(input,inputs[i]);
			padre.removeChild(inputs[i+1]);
		}
		
	}
	for (var i=0; i<num_inputs; i++){

		if (inputs[i].type=='text'){
			var nombre = new String(inputs[i].name);
			var anterior = numeroid-1;
			if (nombre.indexOf(anterior)>=0){
				var padre = inputs[i].parentNode;
				var cadenaNueva = new String("["+numeroid+"]");
				var arr_nombre=nombre.split("[");
				var input = document.createElement("INPUT");
				var name = arr_nombre[0] + cadenaNueva;
				input.setAttribute("type","text");
				input.setAttribute("name",name);
				if (nav){
					input.setAttribute("className","input_big");
				}
				else{
					input.setAttribute("class","input_big");	
				}
				padre.insertBefore(input,inputs[i]);
				padre.removeChild(inputs[i+1]);
				
			}
			/*if (inputs[i].name != "copyright[]" ){
				inputs[i].value='';
			}
			else if(inputs[i].name == "copyright[]" && inputs[i].value != "")
			{
				inputs[i].value='';
			}*/
			inputs[i].value='';
		}
		else if(inputs[i].type=='file'){
			var nombre = new String(inputs[i].name);
			var padre = inputs[i].parentNode;
			var input = document.createElement("INPUT");
			var anterior = numeroid - 1;
			var nombreanterior = "["+anterior+"]";
			input.setAttribute("type","file");
			if (nombre.indexOf(nombreanterior)>=0){
				var indice =numeroid; 
			}
			else{
				var indice =""; 
			}
			input.setAttribute("name","a_enlaceid_noticia_imagen_asociada["+masterLanguage+"]["+indice+"]");
			if (nav){
				input.setAttribute("className","input_file");
			}
			else{
				input.setAttribute("class","input_file");	
			}
			padre.insertBefore(input,inputs[i]);
			padre.removeChild(inputs[i+1]);
			
		}
		else if(inputs[i].type=='radio'){
			inputs[i].checked = false;
			inputs[i].value = numeroid;
		}

	}
}
function insertAfter(parent, node, referenceNode) {
	document.getElementById(parent).insertBefore(node,referenceNode);
	var primer_nodo = document.getElementById(parent).firstChild;
	var ultimo_nodo = document.getElementById(parent).lastChild;
}


function mostrar_inputs_archivo(id){
	var inputs = $(id).getElementsByTagName("INPUT");
	var boton = $(id).getElementsByTagName("A");
	$("div_archivo").style.display="none";
	inputs[1].style.display="block";
	inputs[1].disabled=false;
	boton[2].style.visibility = "hidden";
}
function mostrar_inputs_video(id){
	var inputs = $(id).getElementsByTagName("INPUT");
	var boton = $(id).getElementsByTagName("A");
	$("div_video").style.display="none";
	inputs[1].style.display="block";
	inputs[1].disabled=false;
	boton[2].style.visibility = "hidden";
}
function eliminar_video(id,nombre){
	var masterLanguage = GetMasterLanguage();
	var texto_archivo = '<div class="titulo_modulo">' + _('News video') + '</div>';
	texto_archivo += '<div class="modulos"><div class="modulo_izq">';
	texto_archivo += '<div class="epigrafe_contenido">' + _('File:') + '</div>';
	texto_archivo += '<input name="'+nombre+'['+masterLanguage+'][]" type="file" value="Adjuntar imagen" class="input_file" />';
	texto_archivo += '</div';
	$(id).innerHTML =texto_archivo;

}
function eliminar_archivo(id,nombre){
	var masterLanguage = GetMasterLanguage();
	var texto_archivo = '<div class="titulo_modulo">' + _('News file') + '</div>';
	texto_archivo += '<div class="modulos"><div class="modulo_izq">';
	texto_archivo += '<div class="epigrafe_contenido">' + _('File:') + '</div>';
	texto_archivo += '<input name="'+nombre+'['+masterLanguage+'][]" type="file" value="Adjuntar imagen" class="input_file" />';
	texto_archivo += '</div';
	$(id).innerHTML =texto_archivo;

}
//Editing a new: when "change image" is pressed, the fields text and file are shown
function mostrar_inputs_imagenes(idDiv){
	var divs = $(idDiv).getElementsByClassName("campo_contenido_invisible");
	var num_divs = divs.length;

	for (var i=0; i<num_divs; i++)
	{
		if(divs[i].style.display=='' || divs[i].style.display=='none') 
		{
			divs[i].style.display="block";
		}
		else
		{ 
			divs[i].style.display="none";
		}
	}
	var divs = document.getElementById(idDiv).getElementsByClassName("campo_contenido");
	var num_divs = divs.length;
	for (var i=0; i<num_divs; i++){
		if(divs[i].style.display=='none')
		{
			 divs[i].style.display="block";
		}
		else
		{ 
			divs[i].style.display="none";
		}
	}
	var inputs = document.getElementById(idDiv).getElementsByTagName("INPUT");
	var num_inputs = inputs.length;
	for (var i=0; i<num_inputs; i++){
		if(inputs[i].type != 'radio'){
			if (inputs[i].disabled=true)
			{
				 inputs[i].disabled=false;
			}
			else
			{
				 inputs[i].disabled=true;
			}
		}
	}
}
var evento;

//Ajax call to obtain pvd information
function get_pvd_info(url, id_pvd)
{
	pars = 'id_template=' + id_pvd;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'get', 
		parameters: pars, 
		onComplete: updatePVD
	});

}
//Ajax answer to build the form depending on the document type
function updatePVD(xmlobj){
	var campo;
	var div;
	var div_literal;

	eval('result =' + xmlobj.responseText + ';');

	for (index in result) {

		switch (result[index]['TYPE']){
			case 'text':
				
				if (result[index]['NAME'] ==  'noticia_fecha') {
					FechaCreacion(result[index]['TYPE'],result[index]['NAME'],result[index]['LABEL'],div);
				} else {
					div = DivElementos();
					Titular(result[index]['TYPE'],result[index]['NAME'],result[index]['LABEL'],div,div_literal);
				}
			break;

            case 'textarea':
				div = DivElementos();
			 	AreaTexto(result[index]['TYPE'],result[index]['NAME'],result[index]['LABEL'],div);
	        break; 
			
			case 'attribute':

				switch (result[index]['NAME']){
					/*case 'noticia_fecha':
							FechaCreacion(result[index]['TYPE'],result[index]['NAME'],result[index]['LABEL'],div);
					break;*/
					
					case 'fecha_inicio':
						FechaInicioFin(result[index]['TYPE'],result[index]['NAME'],result[index]['LABEL'],div);
					break;
					
					case 'literal_fecha':
						//First, the literal part for date is generated, and then, the title is added
						//That's why a div is returned and title is passed as param
						div_literal = LiteralFecha(result[index]['TYPE'],result[index]['NAME'],result[index]['LABEL']);
					break;
					
					case 'title':
						div = DivElementos();
						Titular(result[index]['TYPE'],result[index]['NAME'],result[index]['LABEL'],div,div_literal);
					break; 
							//The next two attributes are set as visible in the init, because it's not working here
					case 'a_enlaceid_noticia_video_asociado':
						video_asociado(result[index]['NAME']);
					break; 

					case 'a_enlaceid_noticia_archivo_asociado':
						archivo_asociado(result[index]['NAME']);
					break;

					case 'a_enlaceid_noticia_imagen_asociada':
						modulo_imagenes();
					break;

					case 'cuerpo_imagenes':
						archivo_asociado(result[index]['NAME']);
					break; 
					
					case 'informacion_relacionada':
						archivo_asociado(result[index]['NAME']);
					break; 

				}	
			
			break;

			case 'informacion_relacionada':
				modulo_enlaces();
			break;

			case 'cuerpo_imagenes':
				modulo_imagenes();
			break;
		}
	}

	FechaActual();
}

function ImagenPropiedad(){
	var inputs = $("seccion_imagenes").getElementsByTagName("INPUT");
	var num_inputs = inputs.length;
	var k = 0;
	for (var i=0; i<num_inputs; i++){
	   if(inputs[i].type=='radio' && inputs[i].checked==true){
	   		var nombre = "'archivo"+inputs[i].value+"'";
			k = inputs[i].value;
		}
	}
	var inputfile = document.getElementsByName('archivo0');
}

