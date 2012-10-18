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
  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  
  # Framework: ximDEX
  #
  # Modulo: ximPORTA
  # Autor: Diego Gómez. (Javascript)
  # Tipo: módulo ajdunto a ximEDITOR.
  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  
  # Tarea   :
  			- limpia el código generado por MS. Word 
  			- elimina nodos vacíos (sin texto)
			- sustituye las comillas de Word “ ” por comilla normal
  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  

*/


var v_lim = new Array();

v_lim[0] = '<o:p>';
v_lim[1] = 'class=MsoNormal';
v_lim[2] = '<?xml:namespace prefix = o ns = "urn:schemas-microsoft-com:office:office" />';
v_lim[3] = '&nbsp;';
v_lim[4] = 'lang=EN-GB';
v_lim[5] = '§';
v_lim[6] = '</o:p>';
v_lim[7] = '<HR>';
v_lim[8] = '<P><BR></P>';
v_lim[9] = '<P></P>';
v_lim[10] = '<P align=justify></P>';
v_lim[11] = '<B></B>';
v_lim[12] = '<I></I>';
v_lim[13] = '<U></U>';
v_lim[14] = '<BR><BR>';
v_lim[15] = '<B></B>';


function limpia(temp_var){
	for (i=0; i<v_lim.length; i++){
	my_var = false;
	
		temp_var = limpia_cadena (temp_var, v_lim[i], my_var);
		}
	if (temp_var.indexOf("“") > 0 || temp_var.indexOf("”") > 0){
		my_var = true;
		temp_var = limpia_cadena( temp_var, "“", my_var);
		temp_var = limpia_cadena( temp_var, "”", my_var);
		}
	
	return temp_var;
}

function limpia_cadena(item,delimiter, my_var) {
	tempArray=new Array(1);
	var Count=0;
	var tempString=new String(item);
	
  while (tempString.indexOf(delimiter)>0) {
    tempArray[Count]=tempString.substr(0,tempString.indexOf(delimiter));
	tempString=tempString.substr(tempString.indexOf(delimiter)+delimiter.length, tempString.length-tempString.indexOf(delimiter)); 
    Count=Count+1;
  }
  tempArray[Count]=tempString;
  devuelve = "";
	for(n=0; n<=Count; n++){
		if (!my_var) devuelve = devuelve + tempArray[n];
		else {devuelve = devuelve + "'" + tempArray[n];};
	}
  return devuelve;
}

function s_comilla(cadena){
		my_var = false;
		if (cadena.indexOf("“") > 0 || cadena.indexOf("”") > 0 || cadena.indexOf('"') > 0) my_var = true;
		cadena = limpia_cadena( cadena, "“", my_var);
		cadena = limpia_cadena( cadena, "”", my_var);
		cadena = limpia_cadena( cadena, '"', my_var);

	return cadena;
}
