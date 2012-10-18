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
{literal}
<script src="js/ximdex_common.js" type="text/javascript"></script>
	<SCRIPT LANGUAGE="JavaScript">

{/literal}
	var ximdex_name = '{$versionname}';
	var ximdex_id = '{$ximid}';

	</SCRIPT>

<script type="text/javascript" src="{$_URL_ROOT}{$base_action}resources/js/content.js"></script>
<link rel="stylesheet" type="text/css" href="{$_URL_ROOT}{$base_action}resources/css/content.css">
</head>
<body>
{literal}	

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="bottom" class="tabrow" id='td_iframe'>{/literal}
			<iframe id="tabrow" src="{$composer_index}?method=tabrow" style="width:100%; height: 16; background-color: transparent;" frameborder="0" scrolling="no"></iframe>{literal}
		</td>
		<td width="20" valign="bottom" align="center"><div class="tab" nowrap><img src="" onmouseout="scrolling= false;" onmouseover="scrolling= true; scrollright();"> <img src="" onmouseout="scrolling= false;" onmouseover="scrolling= true; scrollleft();"></div></td>
	</tr>

	<tr>
		<td colspan="2" class="content" id="container"></td>
	</tr>
</table>
{/literal}
</body>
</html>
