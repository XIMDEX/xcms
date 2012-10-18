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
<HTML>
<HEAD>
<TITLE>Propiedades Etiqueta Enlace</TITLE>

<STYLE TYPE="text/css">
 BODY   {margin-left:10;}
 
 TABLE  {font-family:Verdana; font-size:12}
 P      {text-align:center}
</STYLE>
<script src="../../../xmd/js/ximdex_common.js" type="text/javascript"></script>

<SCRIPT LANGUAGE=JavaScript>
<!--
function IsDigit()
{
  return ((event.keyCode >= 48) && (event.keyCode <= 57))
}
function bloqueaimg(){
	inserta.enlace.disabled = false;
	inserta.texto.disabled = false;
	inserta.enl_arc.disabled = false;
	inserta.miclase.disabled = false;
	
	inserta.enlacei.disabled = true;
	
	inserta.alin.disabled = true;
	inserta.imagenes.disabled = true;
	inserta.archivo.disabled = true;
	inserta.ancho.disabled = true;
	inserta.alto.disabled = true;
	inserta.textalt.disabled = true;
	inserta.enlaceimagen.checked = false;
	
	inserta.enlacetexto.checked = true;
}
function bloquetext(){
	inserta.enlace.disabled = true;
	inserta.texto.disabled = true;
	inserta.enl_arc.disabled = true;
	inserta.miclase.disabled = true;
	
	inserta.enlacei.disabled = false;
	//inserta.referencia2.disabled = false;
	inserta.alin.disabled = false;
	inserta.imagenes.disabled = false;
	inserta.archivo.disabled = false;
	inserta.ancho.disabled = false;
	inserta.alto.disabled = false;
	inserta.textalt.disabled = false;
	inserta.enlaceimagen.checked = true;
	inserta.enl_arc.checked = false;
	inserta.enlacetexto.checked = false;
}
function muestrasel(){
inserta.miclase.value = "enlacenegrita";
}
function cambia(myclase){
inserta.miclase.value = myclase;
}
function abre_estilo() {
  var CWIN = false;

	var miURL;

	var altura=(screen.height/2)-224;

	var anchura=(screen.width/2)-385;

	

		url= "../ayuda/estilos.html";

		myname = "estilos";

		win="fullscreen=0,directories=0,resizable=0,width=690,height=400,location=0,status=0,scrollbars=1,toolbar=0,menubar=0,titlebar=0,";

		myventana = window.open(url,myname,win);

		myventana.moveTo  ( Math.ceil( altura ) , Math.ceil( anchura ) );

}

function cerrar_ventana(){
	if (confirm("¿Desea eliminar el estilo?"))
		{
		var arr = new Array();
		arr["borrar"] = "si";
		if (navegador == "ie")
				{
				window.returnValue = arr;
				window.close();
				}
			else
				{
					window.parent.params = arr;
					window.parent.document.getElementById('toFirefox').value = "false";
					window.parent.document.getElementById('Vmodal').style.visibility = "hidden";
					window.parent.aplica_firefox(window.parent.objeto_global, "estilo")
				}
		}
}

function init(){
if (navegador == "firefox15")
	{
	 document.getElementById('clase').value = window.parent.params["clase"];
	 if (window.parent.params["clase"] == '') return;
	 document.getElementById('eliminar').style.visibility = "visible";
	 }
}

// -->
</SCRIPT>

<SCRIPT LANGUAGE=JavaScript>
<!--
function useFile()
{
  	var arr = new Array();
	arr["clase"] = inserta.clase.value;
	arr["borrar"] = "no";
if (navegador == "ie")
	{
	window.returnValue = arr;
	window.close();
	}
else
	{
		window.parent.params = arr;
		window.parent.document.getElementById('toFirefox').value = "false";
		window.parent.document.getElementById('Vmodal').style.visibility = "hidden";
		window.parent.aplica_firefox(window.parent.objeto_global, "estilo")
	}
}
// -->

</SCRIPT>
<script LANGUAGE=Javascript FOR=window EVENT=onload>
<!--
  for ( elem in window.dialogArguments )
  {
    switch( elem )
    {
    
    case "clase":
      inserta.clase.value = window.dialogArguments["clase"];
	  document.getElementById('eliminar').style.visibility = "visible";
      break;
    
    }
  }
// -->
</script>


<link rel="STYLESHEET" type="text/css" href="estilo_popup.css">
</HEAD>

<BODY topmargin="10" rightmargin="10" onload="init();">
<form name="inserta">
<table  class="tabla" width="100%" align="center" cellpadding="2">
	<tr>
		<td class="filacerrar" align="right"><a href="#" onclick="javascript:if(navegador == 'firefox15'){window.parent.toFirefox = true;window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';} else{self.close();}" class="filacerrar">cerrar ventana <img src="../../../xmd/images/botones/cerrar.gif" alt="" border="0"></a></td>
	</tr>
	<tr>
		<td align="center" class="filaclara">
			<TABLE CELLSPACING=5 class="tabla" width="300">
			<TR>
			 	<td colspan="4">
					<table class="tabla" width="100%">
						<tr>
							<td class="cabeceratabla" colspan="2">Propiedades Etiqueta Estilo</td>
						</tr>  
						
			          <TD class="filaoscuranegrita" nowrap>Clase</td>
			          <td>
			            <select name="clase" id="clase" class="cajag">
						
<?php


 ModulesManager::file("/inc/fsutils/FsUtils.class.php");

$estilosXml = FSUtils::file_get_contents("../xml/estilos.xml");

$domDoc = new DOMDocument();
$domDoc->validateOnParse = true;
$domDoc->preserveWhiteSpace = false;
$domDoc->loadXML($estilosXml);
$matriz = $domDoc->getElementsByTagname('ventana');

$num_ventanas = count($matriz);

for ($i=0; $i<$num_ventanas; $i++)
{
	$nombreventana = $matriz->item($i)->getAttribute("nombre");
	
	if ($nombreventana=='estilo.php')
	{
		
		$estilos2 = $matriz->item($i)->getElementsByTagname('elemento');
		$num_estilos = $estilos2->length;
		echo "n= ".$num_estilos;
		for ($j=0; $j<$num_estilos; $j++)
		{
			$mivar = $estilos2->item($j)->nodeName;
			$nombre = $estilos2->item($j)->getAttribute("nombre");
			$vernombre = $estilos2->item($j)->getAttribute("vernombre");
			
			echo '<option value="'.$nombre.'">'.$vernombre.'</option>';
		}
		
	}	
	
}

?>
						
						</select>
			          </td>
			        </tr></form>
					</table>
				</td>
				<tr>
					<td class="filaclara" align="center">
			<INPUT ID="useButton" TYPE="button" CLASS="botong" VALUE="Cancelar" onClick="if(navegador == 'firefox15'){window.parent.toFirefox = true;window.parent.document.getElementById('Vmodal').style.visibility = 'hidden';} else{self.close();}"/>&nbsp;<INPUT ID="useButton" TYPE="button" CLASS="botong" VALUE="Insertar estilo" onClick="useFile()"/>&nbsp;
			<INPUT id="eliminar" TYPE="button" CLASS="botong" VALUE="Eliminar estilo" style="visibility:hidden;" onClick="cerrar_ventana();"/>
			</td>
				</tr>
			</tr>
			
			</TABLE>
		</td>
	</tr>
</table>
</BODY>
</HTML>
