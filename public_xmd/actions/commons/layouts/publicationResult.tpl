{**
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
 *}

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="{url}/assets/style/style.css{/url}" >
<link rel="stylesheet" type="text/css" href="{url}/assets/style/ximdex.css{/url}" >
<link rel="stylesheet" type="text/css" href="{url}/assets/style/lightbox/lightbox.css{/url}" media="screen,projection">

<script type="text/javascript" src="{url}/assets/js/lib/prototype/prototype.js{/url}"></script>
<script type="text/javascript" src="{url}/assets/js/lib/lightbox/lightbox.js{/url}"></script>
<script type="text/javascript" src="{url}/assets/js/validator/validator.js{/url}"></script>

{foreach from=$js_files item=js_file}
<script type="text/javascript" src="{$js_file}"></script>
{/foreach}
{foreach from=$css_files item=css_file}
<link rel="stylesheet" type="text/css" href="{$css_file}" >
{/foreach}


</head>
<body onresize="{$on_resize_functions}" onload="{$on_load_functions}">

<table  class='tabla' width='560' align='center' cellpadding='2'>
<tr>
<td class='filacerrar' align='right'>
<a href='javascript:parent.deletetabpage(parent.selected);' class='filacerrar'>
	cerrar ventana <img src='{url}/assets/images/botones/cerrar.gif{/url}' alt='' border='0'>
	</a>
</td>
</tr>
<tr>
<td align='center' class='filaclara'>
<br /><br /><table class='tabla'>

{foreach from=$arrayOpciones key=opcion item=texto}
	
	<tr><td colspan='3' class=filaclara nowrap><strong>{t}Los The following nodes{/t} {$texto} </strong></td></tr>
	<tr><td class=filaclara><strong>{t}Node{/t}</strong></td>
	<td class=filaclara><strong>{t}Server{/t}</strong></td>
	<td class=filaclara><strong>{t}Channel{/t}</strong></td></tr>

	{foreach from=$arrayResult key=idOpcion item=dataDocs}

		{if $idOpcion == $opcion}

			{foreach from=$dataDocs key=idNode item=physicalServer}
				{foreach from=$physicalServer key=idPhysicalServer item=channel}
					{foreach from=$channel key=idChannel item=idServerFrame}

						<tr><td class='filaoscura'>{$idNode}</td>
						<td class='filaoscura'>{$idPhysicalServer}</td>
						<td class='filaoscura'>{$idChannel}</td><tr>

					{/foreach}
				{/foreach}
			{/foreach}

		{/if}

	{/foreach}

{/foreach}

</table>
</td>
</tr>
</table>

</body>
</html>
