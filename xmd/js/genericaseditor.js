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


/*  General functions of editor manager. Called from ../init.php */
/* 25.01.2007, idemxime, Added function validafecha to use on the editor. Format dd/mm/aaaa o dd/m/aaaa */

function validafecha(obj)
{
		//alert(obj.value);
		var Cadena = obj.value;
		var Fecha= new String(Cadena)	// Creates a string
		var RealFecha= new Date()	// Catches today date
		// String year
		var Ano= new String(Fecha.substring(Fecha.lastIndexOf("/")+1,Fecha.length))
		// String month
		var Mes= new String(Fecha.substring(Fecha.indexOf("/")+1,Fecha.lastIndexOf("/")))
		// String day
		var Dia= new String(Fecha.substring(0,Fecha.indexOf("/")))
	
		// Validating year
		if (isNaN(Ano) || Ano.length<4 || parseFloat(Ano)<1900){
	        	alert('Año inválido')
			return false
		}
		// Validating month
		if (isNaN(Mes) || parseFloat(Mes)<1 || parseFloat(Mes)>12){
			alert('Mes inválido')
			return false
		}
		// Validating day 
		if (isNaN(Dia) || parseInt(Dia, 10)<1 || parseInt(Dia, 10)>31){
			alert('Día inválido')
			return false
		}
		if (Mes==4 || Mes==6 || Mes==9 || Mes==11 || Mes==2) {
			if (Mes==2 && Dia > 28 || Dia>30) {
				alert('Día inválido')
				return false
			}
		}
		
		

}


function showHTML()
{
	winWithData( bodyid.innerHTML );
}

function showXML()
{

	winWithData( serializa_me(document.getElementById('edxid').getXmlNode()) );
}

function loaddoc( sUrl )
{
	if (sUrl == "about:blank") return;
	enableButton( idshowicons, false );
	enableButton( idhideicons, true );
	edxid.xmlurl = sUrl;
}

function winWithData(str1) {

	var sWidth = 800
	var sHeight = 560
	var screenStartTop = (screen.availHeight - sHeight) / 2 - 50
	var screenStartLeft = (screen.availWidth - sWidth) / 2
	var positionStr = "left="+screenStartLeft+",top="+screenStartTop

	xwin = window.open("", "xmlWin", "toolbar=0,location=0,directories=0,status=1,menubar=1,scrollbars=1,resizable=1,copyhistory=0,"+positionStr+",width="+sWidth+",height="+sHeight+",dependent=1");
	xwin.document.write ("<html><body><textarea rows=34 cols=92>");
	xwin.document.write (str1);
	xwin.document.write ("</textarea></body></html>");
	xwin.document.close();
	xwin.focus();
}

function moveup()
{
	if( document.getElementById('edxid').canMoveUp() )
		document.getElementById('edxid').moveUp();
	enableButton( document.getElementById('idmoveup'), document.getElementById('edxid').canMoveUp() );
	enableButton( document.getElementById('idmovedown'), document.getElementById('edxid').canMoveDown() );
}

function movedown()
{
	if( document.getElementById('edxid').canMoveDown() )
		document.getElementById('edxid').moveDown();
	enableButton( document.getElementById('idmoveup'), document.getElementById('edxid').canMoveUp() );
	enableButton( document.getElementById('idmovedown'), document.getElementById('edxid').canMoveDown() );
}

function docChange()
{

	var changed = document.getElementById('edxid').canUndo();
	enableButton( document.getElementById('idundo'), document.getElementById('edxid').canUndo() );
	enableButton( document.getElementById('idredo'), document.getElementById('edxid').canRedo() );
	enableButton( document.getElementById('idsave'), changed );
}

function enableButton( id, bFlg )
{
//alert(document.getElementById('idundo').getAttribute('disabled'));

if( bFlg )
		{
		id.setAttribute('disabled', true)
		id.childNodes[0].style.visibility = "visible";
		}
	else
		{
		id.setAttribute('disabled', false)
		id.childNodes[0].style.visibility = "hidden";
		}


if( id.getAttribute('disabled') == bFlg )
		return;
//	if (id.disabled == "undefined") id.disabled = false;
	
	id.setAttribute('disabled', !bFlg);
	
	
	
}

function selChange()
{

//e.root.onSelectionChange(event);
//document.getElementById('edxid').
	enableButton( document.getElementById('idmoveup'), document.getElementById('edxid').canMoveUp() );
	enableButton( document.getElementById('idmovedown'), document.getElementById('edxid').canMoveDown() );
	
	enableButton( document.getElementById('idlink'), document.getElementById('edxid').canApplyTag( 'enlace' ) );
	enableButton( document.getElementById('idstile'), document.getElementById('edxid').canApplyTag( 'estilo' ) );
	enableButton( document.getElementById('idintro'), document.getElementById('edxid').canApplyTag( 'salto_parrafo' ) );
}

function enableIcons( bFlg )
{
	document.getElementById('edxid').enableIcons( bFlg );
	if( bFlg )
	{
		enableButton( idshowicons, false );
		enableButton( idhideicons, true );
	}
	else
	{
		enableButton( idshowicons, true  );
		enableButton( idhideicons, false );
	}
}

