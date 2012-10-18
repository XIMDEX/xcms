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

var lista = new Array();

Array.prototype.inArray = function (value){
    var i;
    for (i=0; i < this.length; i++) {
        if (this[i] === value) {
            return 1;
        }
    }
    return 0;
};
function valida_checkbox(Nombre){
	var elementos = document.getElementsByName(Nombre);
	var num_elementos = elementos.length;
	var sw;
	for (i=0;i<num_elementos;i++){
		if(elementos[i].checked)
		{
			sw = true;
			return sw;
		}
		else
		{
			sw = false;
		}
	}
return sw;
}
function valida(){
	if (cdx_form.colector.value == "") {alert(_("The collector Name should be specified")); cdx_form.colector.focus(); return;}
	idioma = valida_checkbox("langidlst[]");
	canal = valida_checkbox("channellst[]");
	if(canal==false || idioma==false)
			{
			alert(_("At least one language and one channel should be specified")); return;
			}
			
	if (cdx_form.tipo.value == "numero" && cdx_form.filter.value == "") {alert(_("A news limit should be specified")); cdx_form.tipo.focus(); return;}
	if (cdx_form.template.value == "-"){alert(_("A bulletin template should be specified"));cdx_form.template.focus(); return;}
	if (confirm(_("Do you want to create the news collector") +" '" + cdx_form.colector.value + "'?")) document.cdx_form.submit();
	else return;		
}

//deprecated function
function agruparNoticias(obj){
	valor = obj[obj.selectedIndex].value; 
	if(valor == "numero"){
		document.getElementById("hideText").style.visibility = "visible";
        	document.getElementById("hideText").focus();
	}
	else{
		document.getElementById("hideText").style.visibility = "hidden";
	}
}

function elegirLista(obj){
	valor = obj[obj.selectedIndex].innerHTML;
	if(valor != "Seleccionar"){
		re = lista.inArray(valor);
		if (re > 0){
			alert(_("Address previously selected"));	
		}
		else{
			texto = document.cdx_form.listaid.value;
			if(texto != ""){
				texto += ","; 
			}
			texto += valor;
			document.cdx_form.listaid.value = texto;
                	lista.push(valor);
		}
	}	
}
