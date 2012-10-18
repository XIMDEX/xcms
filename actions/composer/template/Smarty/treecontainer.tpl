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


<html>
<head>
<script src="js/ximdex_common.js" type="text/javascript"></script>
<script type="text/javascript" src="{$_URL_ROOT}{$base_action}resources/js/treecontainer.js"></script>
	<link rel="STYLESHEET" type="text/css" href="style/estilo_entorno_ximDEX.css">
</head>
<body onload="setTimeout('addtabpage(\'{t}Ximdex tree{/t}\', \'{$composer_index}?method=tree\' ,1)',1000);">
{if ($debug)}
<div id="debug_filter" style="display: none; margin: 0;">
<label>{t}Filter{/t}:<input type="text" name='debugfilter' id='debugfilter' size='20' value='' /></label>
</div>
{/if}
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="bottom" class="tabrow" style="border-bottom: 0px ridge; overflow:hidden;">
			<iframe id="tabrow" src="{$composer_index}?method=tabrow" style="width:100%; height: 16; background-color: transparent;" frameborder="0" scrolling="no"></iframe>
		</td>
		<td width="20" valign="bottom" align="center"><div class="tab" nowrap><img src="" onmouseout="scrolling= false;" onmouseover="scrolling= true; scrollright();"> <img src="" onmouseout="scrolling= false;" onmouseover="scrolling= true; scrollleft();"></div></td>
	</tr>

	<tr>
		<td colspan="2" class="content" id="container"><img src="" width=0 height=0 border=0></td>
	</tr>
{literal}
</table>
{/literal}
</body>
</html>
