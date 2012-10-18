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


/*
# v1.00 21.04.2005

*/


function revisamodulos(plantilla, x)
{
	switch (plantilla)
	{
		/*case "ximPORTA": 
			
			var xmlDoc2 = new ActiveXObject("Msxml2.DOMDocument");
			xmlDoc2.async = false;
			xmlDoc2.resolveExternals = false; 
			
			var x = func_ximPORTA(plantilla,x); 
			
			xmlDoc2 = x;
			var y = xmlDoc2; 
			return y;
			break;*/
		case "tabla": 
			var x = func_tabla(plantilla, x); 
			return x;
			break;
		case "calendario": 
			var x = func_calendario(plantilla, x); 
			return x;
			break;
		case "menu_superior-opcion":
			var x = func_menu_superior_opcion(plantilla, x);
			return x;
			break;
		default: 
			return x;
	}
}

function func_menu_superior_opcion(plantilla, x)
{
	var xmlDoc2 = new ActiveXObject("Msxml2.DOMDocument");
	var root2;
	var newNode2;
	xmlDoc2.async = false;
	xmlDoc2.resolveExternals = false; 
	var args = new Array();   
	var arr = null;
	//alert(x.xml);
	num = Math.floor(Math.random()*100);
	var mi_identificador = "ident" + num;
	x.childNodes.item(0).setAttribute("identificador",mi_identificador);	

	return x;
}


function func_ximPORTA(plantilla,x)
{
		
	var xmlDoc2 = new ActiveXObject("Msxml2.DOMDocument");
	xmlDoc2.async = false;
	xmlDoc2.resolveExternals = false; 

	var args = new Array();   
	var arr = null;
	args["nodeid"] = Xnodeid;
	arr = showModalDialog( "ximPORTA/ximPORTA.html",
                             args,
                             "font-family:Verdana; dialogWidth:620px; dialogHeight:450px;scroll=no;");
	if (arr != null){
		xmlDoc2.loadXML('<ximPORTA>' + arr["xml_content"] + '</ximPORTA>');
		x = xmlDoc2;
		return x;
		
	}
	else return x;

}

function func_tabla(plantilla, x)
{
	var x;
	if (navegador=='ie')
	{
		var xmlDoc2 = new ActiveXObject("Msxml2.DOMDocument");
		var root2;
		var newNode2;
		xmlDoc2.async = false;
		xmlDoc2.resolveExternals = false; 
		var args = new Array();   

		arr = showModalDialog( "../actions/xmleditor/inc/wtabla.php",
				args,
				"font-family:Verdana; dialogWidth:480px; dialogHeight:430px;scroll=auto;");
	} else {

		var xmlDoc2 = document.implementation.createDocument("","",null);

		arr = window.open("../actions/xmleditor/inc/wtabla.php", args,  "toolbar=no,menubar=no,personalbar=no,width=10,height=10, scrollbars=no,resizable=yes,modal=yes,dependable=yes");
	}

	if (arr != null){
		xmlDoc2.loadXML(arr["xml_content"]);
		x = xmlDoc2;
		return x;
	}

	if (navegador == "firefox15")
	{
		if (document.getElementById('toFirefox').value  == "true")
		{
					
			document.getElementById('Vmodal').src = "../actions/xmleditor/inc/wtabla4gecko.php";
			document.getElementById('Vmodal').style.width = "500px";
			document.getElementById('Vmodal').style.height = "350px";
			document.getElementById('Vmodal').style.top = (window.innerHeight / 2) - 200;
			document.getElementById('Vmodal').style.left = (window.innerWidth / 2) - 300;
			document.getElementById('Vmodal').style.visibility = "visible";
			
			campo_activo = objeto;
			
			
			
						
		}
		else
		{
			//alert('entra');
		}
	
	}
	
}

function func_calendario(plantilla)
{
	var x;
		
			
	var xmlDoc2 = new ActiveXObject("Msxml2.DOMDocument");
	var root2;
	var newNode2;
	xmlDoc2.async = false;
	xmlDoc2.resolveExternals = false; 
	var args = new Array();   
	var arr = null;
	arr = showModalDialog( "../actions/xmleditor/inc/wcalendario.php",
                             args,
                             "font-family:Verdana; dialogWidth:530px; dialogHeight:475px;scroll=no;");						 
	if (arr != null){
		xmlDoc2.loadXML(arr["xml_content"]);
		x = xmlDoc2;
		return x;
		}
}
