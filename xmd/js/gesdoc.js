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


function obtener_referenciadores(event, objeto)
{

var eTarget = event.target || event.srcElement;
	
var e = eTarget.srcElement.eobj;
	var args = new Array();

	var editnode = e.root.getXmlNode();
	var nodohijo = e.parent.getXmlNode();
		
		
	args["xml"] = editnode.xml;
	args["xmlhijo"] = nodohijo.xml;

	arr = showModalDialog( ximdexurlroot + "/actions/xmleditor/inc/referenciadores.html",
                             args,
                             "font-family:Verdana; dialogWidth:250px; dialogHeight:200px;");
  	if (arr != null) 
	{
		objeto.value = arr["ref"];
	}
	else{return;}
		
	

}


function edita_formulario(objeto){
		var args = new Array();   
		var arr = null;
		if (objeto.value == ""){
			arr = showModalDialog( ximdexurlroot + "/actions/xmleditor/inc/ximlet.php",
	                             args,
	                             "font-family:Verdana; dialogWidth:600px; dialogHeight:420px;");
				  	if (arr != null) {
						objeto.value = limpia_id(arr["ID"]);
						return;
						}
					else{return;}
		}
		else{
			doc = edxid.getXmlNode().xml;
			args["xml_content"] = doc;
			args["elnodo"] = objeto.value;
			arr = showModalDialog( "edit_forms.php?nodeid=" + objeto.value, args, "font-family:Verdana; dialogWidth:590px; dialogHeight:550px;over-flow: auto;");
			//my_var = window.open("edit_forms.php?nodeid=" + objeto.value, args)
			if (arr != null){
				//Do something when form was successfully loaded
				save_form(arr["texto"]);
				return;
				}
			else{
				return;
			}
		}
}

function save_form(texto) {
  document.forms.saveform.contenido.value = texto;
  document.forms.saveform.version.value = 'save';
  if(document.forms.saveform.contenido.value)
  		{

		document.forms.saveform.submit();
		}
  /// If it does not work, we can put here a settimeout, to give a time to save it a then open it again.
  edxid.xmlurl = echodocmapper;
  return;
 }


/*
Function to insert a image in the text field.
It recives the 'object' which corresponds with the field who called the function.

Returns the objeto.value= value that recives the modal window

*/


function obtener_imagen( objeto ){
		var args = new Array();
		var arr = null;
	   	arr = null;
		args["nodeid"] = Xnodeparent;
		
if (navegador == "ie")
	{	
		if (objeto.value != "") args["url"] = objeto.value;
		arr = showModalDialog( ximdexurlroot + "/actions/xmleditor/inc/images.php", args, "font-family:Verdana; dialogWidth:600px; dialogHeight:420px;");
		if (arr != null) {
			objeto.value = arr["ID"]
			objeto.text = arr["imagen"]
			}
		else{return;}
	}
if (navegador == "firefox15")
	{
	//arr = window.open(ximdexurlroot + "/actions/xmleditor/inc/images.html?id" + objeto.value , args,  "toolbar=no,menubar=no,personalbar=no,width=600,height=400, scrollbars=no,resizable=yes,modal =yes,dependable=yes");

		if (objeto.value != "") params["url"] = objeto.value;
		else params["nodeid"] = Xnodeparent;

		document.getElementById('Vmodal').src = ximdexurlroot + "/actions/xmleditor/inc/images.php";
		document.getElementById('Vmodal').style.width = "600px";
		document.getElementById('Vmodal').style.height = "400px";
		document.getElementById('Vmodal').style.top = (window.innerHeight / 2) - 200;
		document.getElementById('Vmodal').style.left = (window.innerWidth / 2) - 300;
		document.getElementById('Vmodal').style.visibility = "visible";
		
		campo_activo = objeto;
	
	}
	  	
			
		}

function limpia_id(el_id){
	
t_id = "";
for (n=19; n < el_id.length - 4; n++){
	t_id = t_id + el_id.charAt(n);
}
	return t_id;
}
		
function obtener_ximlet( objeto ){
		var el_id = limpia_id(objeto.value);
		var args = new Array();
		var arr = null;
	   	var inic = "";
		args["nodeid"] = Xnodeparent;
		if (navegador == "ie")
			{
	
					if (objeto.value != ""){
						args["url"] = limpia_id(objeto.value);
						arr = showModalDialog( ximdexurlroot + "/actions/xmleditor/inc/eleccion.php",
				                             args,
				                             "font-family:Verdana; dialogWidth:300px; dialogHeight:120px;");
						}
					else{
						arr = showModalDialog( ximdexurlroot + "/actions/xmleditor/inc/ximlet.php",
				                             args,
				                             "font-family:Verdana; dialogWidth:600px; dialogHeight:420px;");
							  	if (arr != null) {
									// siguientes tres lineas aniadidas 11/04/05 por idemxime para mandar solo el numero de nodo o la expresion de interpretacion de ximlet. Afectado el archivo inc/ximlet.html linea 93
									update_ximlet(objeto, arr["ID"]);
									return;
									}
								else{return;}
						}
					if (arr != null){
						if (arr["valor"] == "cambiar" || inic == "cambiar"){
						arr = showModalDialog( ximdexurlroot + "/actions/xmleditor/inc/ximlet.php",
				                             args,
				                             "font-family:Verdana; dialogWidth:600px; dialogHeight:420px;");
							  	if (arr != null) {
									// Modified to send just the node number or expression of interpretation of ximlet. Affects file inc/ximlet.html linea 93
									update_ximlet(objeto, arr["ID"]);
									return;
									}
			
								else{return;}
								}
						else{
								prevwinId = parent.addtabpage("Editar XML", ximdexurlroot + "/xmd/loadaction.php?nodeid="+ el_id + "&actionid=6065");
						}
					}
					else{return;}
			
			}
		if (navegador == "firefox15")
		{
		
		if(el_id=="") pregunta = false;
		else
		{
		
			if (confirm ('¿Desea editar el documento ximlet?'))
				{
				pregunta = true;
				}
			else
				{
				pregunta = false;
				}
		}
			if (pregunta)
				{
					prevwinId = parent.addtabpage("Editar XML", ximdexurlroot + "/xmd/loadaction.php?nodeid="+ el_id + "&actionid=6065");
				}
			else
				{
				if (objeto.value != "") params["url"] = objeto.value;
				else params["nodeid"] = Xnodeparent;
				
					document.getElementById('Vmodal').src = ximdexurlroot + "/actions/xmleditor/inc/ximlet.php";
					document.getElementById('Vmodal').style.width = "600px";
					document.getElementById('Vmodal').style.height = "400px";
					document.getElementById('Vmodal').style.top = (window.innerHeight / 2) - 200;
					document.getElementById('Vmodal').style.left = (window.innerWidth / 2) - 300;
					document.getElementById('Vmodal').style.visibility = "visible";
		
					campo_activo = objeto;
				}
		
		}
		
			
}

function update_ximlet(object, value) {
	if (object.type == 'image') {
		object.value = "@@@GMximdex.ximlet(" + value + ")@@@";
	} else {
		object.value = value;
	}
}

function obtener_enlace( objeto ){
		var args = new Array();
		arr = null;
		if (navegador == "ie")
		args["nodeid"] = Xnodeparent;
	{
if (objeto.value != "") args["url"] = objeto.value;
		args["nodeid"] = Xnodeparent;
		arr = showModalDialog( ximdexurlroot + "/actions/xmleditor/inc/enlace.php",
	                             args,
	                             "font-family:Verdana; dialogWidth:630px; dialogHeight:420px;");
	  	if (arr != null) {
			objeto.value = arr["ID"]
			}
		else{return;}}
			
			
		if (navegador == "firefox15")
			{
			//arr = window.open(ximdexurlroot + "/actions/xmleditor/inc/images.html?id" + objeto.value , args,  "toolbar=no,menubar=no,personalbar=no,width=600,height=400, scrollbars=no,resizable=yes,modal =yes,dependable=yes");
			
				document.getElementById('Vmodal').src = ximdexurlroot + "/actions/xmleditor/inc/enlace.php";
				document.getElementById('Vmodal').style.width = "600px";
				document.getElementById('Vmodal').style.height = "400px";
				document.getElementById('Vmodal').style.top = (window.innerHeight / 2) - 200;
				document.getElementById('Vmodal').style.left = (window.innerWidth / 2) - 300;
				document.getElementById('Vmodal').style.visibility = "visible";
				
				campo_activo = objeto;
			
			}
					
		}

function obtener_common( objeto ){
		var args = new Array();
		var arr = null;
	   	arr = null;
		args["nodeid"] = Xnodeparent;
		if (navegador == "ie")
			{
			if (objeto.value != "") args["url"] = objeto.value;
		arr = showModalDialog( ximdexurlroot + "/actions/xmleditor/inc/common.php",
	                             args,
	                             "font-family:Verdana; dialogWidth:630px; dialogHeight:420px;");
	  	if (arr != null) {
			objeto.value = arr["ID"]
			}
		else{return;}
			}
			if (navegador == "firefox15")
			{
			//arr = window.open(ximdexurlroot + "/actions/xmleditor/inc/images.html?id" + objeto.value , args,  "toolbar=no,menubar=no,personalbar=no,width=600,height=400, scrollbars=no,resizable=yes,modal =yes,dependable=yes");
			
				document.getElementById('Vmodal').src = ximdexurlroot + "/actions/xmleditor/inc/common.php";
				document.getElementById('Vmodal').style.width = "600px";
				document.getElementById('Vmodal').style.height = "400px";
				document.getElementById('Vmodal').style.top = (window.innerHeight / 2) - 200;
				document.getElementById('Vmodal').style.left = (window.innerWidth / 2) - 300;
				document.getElementById('Vmodal').style.visibility = "visible";
				
				campo_activo = objeto;
			
			}
			
		}
		
function obtener_import( objeto ){
		var args = new Array();
		var arr = null;
	   	arr = null;
		args["nodeid"] = Xnodeparent;
		if (objeto.value != "") args["url"] = objeto.value;
		var  pregunta = true;
		if (objeto.value == "") {
			pregunta = false;
		}
		else {
			if (confirm ('¿Desea editar el archivo HTML?'))
				{
				pregunta = true;
				}
			else
				{
				pregunta = false;
				}
		}
		if (!pregunta)
				{
				if (navegador == "ie")
					{
					arr = showModalDialog( ximdexurlroot + "/actions/xmleditor/inc/import.php",
				                             args,
				                             "font-family:Verdana; dialogWidth:630px; dialogHeight:420px;");
				  	if (arr != null) {
						objeto.value = arr["ID"]
						}
					else{return;}
					}
				
				if (navegador == "firefox15")
					{
						//arr = window.open(ximdexurlroot + "/actions/xmleditor/inc/images.html?id" + objeto.value , args,  "toolbar=no,menubar=no,personalbar=no,width=600,height=400, scrollbars=no,resizable=yes,modal =yes,dependable=yes");
						
							document.getElementById('Vmodal').src = ximdexurlroot + "/actions/xmleditor/inc/import.php";
							document.getElementById('Vmodal').style.width = "600px";
							document.getElementById('Vmodal').style.height = "400px";
							document.getElementById('Vmodal').style.top = (window.innerHeight / 2) - 200;
							document.getElementById('Vmodal').style.left = (window.innerWidth / 2) - 300;
							document.getElementById('Vmodal').style.visibility = "visible";
							
							campo_activo = objeto;
						
						}
				
				}
		else{
			prevwinId = parent.addtabpage("Editar HTML", ximdexurlroot + "/xmd/loadaction.php?nodeid="+ objeto.value +"&actionid=6229");
			}
			
		}
		
	/* Function to save the file 
		doc: string which contains all xml of the file
	*/
	function save()
	{
		var xmlDoc = document.getElementById('edxid').getXmlNode();
		var doc = serializa_me(xmlDoc);

		//var doc = serializa_me(document.getElementById('edxid').getXmlNode());
		document.forms.saveform.contenido.value = doc;
		document.forms.saveform.version.value = 'save';
		if(doc && doc == document.forms.saveform.contenido.value)
		{	
			document.forms.saveform.submit();
			
		}
		return;
	}
	
	function isHTA()
	{
	if (top.ximdex){
		if(top.ximdex.applicationName)
			return true;
		else
			return false;
		}
	}
	
	
	function preview()
	{
		/*if(!document.getElementById('tabview').checked || isHTA())
			preview_tab()
		else
			preview_new();*/
		
		preview_tab();
	}
		
	function preview_new()
	{
		var doc = serializa_me(document.getElementById('edxid').getXmlNode());
		document.forms.prevform.contenido.value = doc;
		document.forms.prevform.channel.value = document.getElementById('channellist').options[document.getElementById('channellist').selectedIndex].value;
		if(doc && doc == document.forms.prevform.contenido.value)
		{
			document.forms.prevform.submit();
		}
		return;
	}
	
	function preview_tab()
	{

		var doc = serializa_me(document.getElementById('edxid').getXmlNode());
		doc = escape(doc);

		parent.$('#bw1').browserwindow('openAction', {
			command: 'prevdoc',
			name: 'Previo',
			params: 'content=' + doc
		}, [Xnodeid]);

		return;

		var doc = serializa_me(document.getElementById('edxid').getXmlNode());
		var channel = document.getElementById('channellist').options[document.getElementById('channellist').selectedIndex].value;
		
		if(doc && channel)
		{
			prevwinId = parent.addtabpage("Previo");
			prevwin=parent.boxes[prevwinId].contentWindow; 
			
			prevwin.document.open();
			prevwin.document.write ('<html><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><body>');
			prevwin.document.write ('<form action="' + ximdexurlroot + '/xmd/loadaction.php?action=prevdoc" method="post" id="prevform" name="prevform">');
			prevwin.document.write ('<input type="hidden" name="nodeid" id="nodeid" value="' + echonodeid + '">');
			prevwin.document.write ('<input type="hidden" name="contenido" id="contenido">');
			prevwin.document.write ('<input type="hidden" name="channel" id="channel">');
			prevwin.document.write ('<input type="hidden" name="visualmode" id="visualmode">');
			
			prevwin.document.write ('<pre style="font-family:verdana; font-size: 10px; color:#002157">Generando previsualización...</pre>');
			prevwin.document.write ("</form></body></html>");
			prevwin.document.forms.prevform.elements.contenido.value = doc;
			prevwin.document.forms.prevform.elements.visualmode.value = 'tab';
			prevwin.document.forms.prevform.elements.channel.value = channel;
			
			prevwin.document.forms.prevform.submit();
			prevwin.document.close();
		}
		return;
	}

/// Edition of a document in a new tab passing a ID. It can be called from a PVD
	
function editar_documento(el_doc)
	{
	
		var doc = edxid.getXmlNode().xml;
		var channel = document.getElementById('channellist').options[document.getElementById('channellist').selectedIndex].value;
		
		if(doc && channel)
		{
			
			prevwinId = parent.addtabpage("Editar Noticia", ximdexurlroot + "/xmd/loadaction.php?nodeid="+ el_doc +"&actionid=6065");
		}
		return;
	}


	// End of edition
	// Start some editor elements
	function inicializa()
	{
		if(isHTA())
		{
			var tabCheckBox = document.getElementById('tabview');
			tabCheckBox.checked  = false;
			tabCheckBox.disabled = true;
		}
		if (navegador == "firefox15")
			{
			document.getElementById("edxid").style.height= window.innerHeight + "px";
			document.getElementById("mastercontenedor").style.height= window.innerHeight + "px";
			//window.parent.
			document.getElementById('toFirefox').value = "true";
			}
		else
			{
			document.getElementById("edxid").style.height= "100%";
			document.getElementById("mastercontenedor").style.height= "100%";
			}
	}
	
	function aplica(objeto, destino){
		if (navegador == "ie") edxid.applyTag( destino );
		else{

			objeto_global = objeto;

			document.getElementById ('edxid').applyTag( destino )
			//Checking if global variable "toFirefox" is active. If it is on we need open the modal window to active the values
			if (document.getElementById('toFirefox').value == "true")
				{
					// ventana modal
					abre_modal_firefox (objeto, destino);
					
				}
			else
				{
					document.getElementById('toFirefox').value = "true";
					params = new Array();					
				}
			
			}
		}
	
	// variables and functions for FireFox
	
	//var objeto_global;

	// open windows of element edition 
	function abre_modal_firefox (objeto, destino){
		if (document.getSelection() == "" && destino != "salto_parrafo")
			{
				alert('Debe seleccionar texto para la insertar la etiqueta ' + destino + ' en el texto');
				return;
			}
		switch (destino){
			case "enlace":
					document.getElementById('Vmodal').src = ximdexurlroot + "/actions/xmleditor/inc/archivos.php";
					document.getElementById('Vmodal').style.width = "630px";
					document.getElementById('Vmodal').style.height = "350px";
					document.getElementById('Vmodal').style.top = (window.innerHeight / 2) - 175;
					document.getElementById('Vmodal').style.left = (window.innerWidth / 2) - 315;
					document.getElementById('Vmodal').style.visibility = "visible";
					
				break;
			case "estilo":
					document.getElementById('Vmodal').src = ximdexurlroot + "/actions/xmleditor/inc/estilo.php";
					document.getElementById('Vmodal').style.width = "380px";
					document.getElementById('Vmodal').style.height = "130px";
					document.getElementById('Vmodal').style.top = (window.innerHeight / 2) - 95;
					document.getElementById('Vmodal').style.left = (window.innerWidth / 2) - 190;
					document.getElementById('Vmodal').style.visibility = "visible";
				break;
			case "salto_parrafo":
				break;
		}
	}

	function aplica_firefox(objeto, destino){

		document.getElementById ('edxid').applyTag( destino );
	}
	
	function recorta(){
		//n_archivo.value.length
		nuevo_valor = "";
		nuevo_valor2 = "";
		for (i=document.getElementById('n_archivo2').value.length-1;i>=0;i--) {
			if (document.getElementById('n_archivo2').value.charAt(i) != "/") nuevo_valor = nuevo_valor +  document.getElementById('n_archivo2').value.charAt(i);
			else break;
		}
		for (i=nuevo_valor.length-1;i>=0;i--) {
			nuevo_valor2 = nuevo_valor2 +  nuevo_valor.charAt(i);
		}
		document.getElementById('n_archivo').value = nuevo_valor2;

		my_winH = document.body.offsetHeight;
		document.getElementById('edxid').style.height = my_winH - 52;
		
	}
	
function recoloca(){
my_winH = document.body.offsetHeight;
document.getElementById('edxid').style.height = my_winH - 52;
my_winW = document.body.offsetWidth;
if (my_winW == 559){
	alert("Seguir reduciendo el espacio de la zona de trabajo provocará que el editor no se visualize correctamente");
}
}
var plantilla_vista ="";
var proyecto ="";
function crea_globales(){

	doc = document.getElementById('edxid').getXmlNode().xml;
	var xDoc = new ActiveXObject("Msxml2.DOMDocument");
	xDoc.async = true;
	xDoc.resolveExternals = true;
	xDoc.loadXML(doc);
	var root = xDoc.documentElement;
	var cs = root.childNodes;
	plantilla_vista = root.getAttribute("tipo_documento");
	proyecto = root.getAttribute("proyecto");	
}
