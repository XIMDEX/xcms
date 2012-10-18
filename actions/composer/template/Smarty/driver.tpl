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
<script type="text/javascript">
	var url_root = '{$_URL_ROOT}';
</script>

<script type="text/javascript" src="js/xmlextras.js"></script>
<script type="text/javascript" src="{$_URL_ROOT}{$base_action}resources/js/driver.js"></script> 
- <script type="text/javascript" src="{$_URL_ROOT}{$base_action}resources/js/menu_debug.js"></script>
<script type="text/javascript" src="js/ximdex_common.js"></script>
<script src="js/buttons.js" type="text/javascript"></script>
<script type="text/javascript" src="js/poslib.js"></script>
<script type="text/javascript" src="js/scrollbutton.js"></script>
<!-- New drop-down menu system compatible with crossbrowser -->
<!-- Menu script itself. You should not modify this file -->
<script language="JavaScript" src="js/menu.js"></script>
<!-- Items structure. menu hierarchy and links are stored there -->
<script language="JavaScript" src="{$composer_index}?method=menuItems"></script>
<!-- Files with geometry and styles structures -->
<script language="JavaScript" src="js/menu_tpl.js"></script>

<link rel="stylesheet" type="text/css" href="js/skins/officexp/officexp2.css">
<link rel="stylesheet" type="text/css"  href='style/menu.css'>

{literal}

</head>
<body onload="LoadPage();">
</script>

<script language="JavaScript">
	<!--//
	// Note where menu initialization block is located in HTML document.
	// Don't try to position menu locating menu initialization block in
	// some table cell or other HTML element. Always put it before </body>

	// each menu gets two parameters (see demo files)
	// 1. items structure
	// 2. geometry structure
//new menu (MENU_ITEMS, MENU_POS);
	
	// make sure files containing definitions for these variables are linked to the document
	// if you got some javascript error like "MENU_POS is not defined", then you've made syntax
	// error in menu_tpl.js file or that file isn't linked properly.
	
	// also take a look at stylesheets loaded in header in order to set styles
	//-->
</script>
{/literal}

<div style="" id="menusup" ><script>new menu (MENU_ITEMS, MENU_POS);</script></div>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="28" style="margin: 0px;">
	<tr>
		<td width="160" height="28" align="center" bgcolor="#ffffff"><img src="images/logo_ximdex.gif" height="32" border="0" alt="ximDEX 2.5" title="ximDEX 2.5"></td>

		<td align="left" valign="top">
		<fieldset id='fieldset_botonera'>
			<div  id="botonera">
			<table cellpadding="0" cellspacing="0" border="0" style="margin: 0px;">
				<tr id="selectedtoolbar">
					<td class="coolButton" valign="middle" nowrap><img src="images/pix_t.gif" width="0" height="32" border="0" align="absmiddle"></td>
				</tr>
			</table>
			</div>
			<!--<div class="coolBar"><span class="coolButton" onclick="addworkspace();" nowrap><img src="images/notepad.png" align="absmiddle"></span><span class="coolButton" onclick="addworkspace();" nowrap><img src="images/find.small.png" align="absmiddle"></span></div>-->
		</fieldset>
		</td>

		<td width="25">
		<fieldset id='fieldset_coolbar' >
			<!-- Commented code to suppress the parent action bar -->
			<!--<div style="width: 100%; border-bottom: 2px groove; background-color: buttonface;">
			<table width="100%" cellpadding=0 cellspacing=0 border=0 class="coolBar" nowrap>
				<tr>
					<td class="coolButton" onmouseover="SetInfo(_('Parent node'),' ruta:/'+nodes[1]['path']);" onmouseout="HideInfo();" valign="middle" nowrap><img src="images/pix_t.gif" width="0" height="16" align="absmiddle"><img src="images/icons/info.png" align="absmiddle"></td>
				</tr>
			</table>
			</div>-->
			<div border=2>
			<table width="100%" cellpadding=0 cellspacing=0 border=0 class="coolBar" nowrap>
				<tr>
					<td class="coolButton" onmouseover="SetInfo(_('Selected node'),' ruta:/'+nodes[0]['path']);" onmouseout="//HideInfo();" valign="middle" nowrap><img src="images/pix_t.gif" width="0" height="32" align="absmiddle"><img src="images/icons/info.png" align="absmiddle"></td>
				</tr>
			</table>
			</div>
		</fieldset>
		</td>
	</tr>
</table>

<div id='coolbar_container' >

	<table width="100%" cellpadding=0 cellspacing=0 border=0 class="coolBar" nowrap>
		<tr>
			<td width="15" align="right" valign="middle" id='coolbar_accion'>
				&nbsp;{t}Action{/t}:
			</td>

			<td width="110" valign="middle" style="overflow: hidden; ">
				<input id="selectaction" type="text" onkeydown="this.blur()"  value="">
			</td>

			<td width="20" align="right" valign="middle" id='seleccion'>
				&nbsp;&nbsp;{t}Selection{/t}:
			</td>

			<td  valign="middle" style="overflow: hidden; ">
				<input id="selectnode" type="text" onkeydown="this.blur();"  value="">
			</td>

			<td width="100" align=right>

				<table cellpadding=0 cellspacing=0 border=0 class="coolBar" nowrap>
					<tr>
						<td class="coolButton" onclick="MailModule();" onmouseover="SetInfo('{t}Messages{/t}','')" onmouseout="HideInfo();" valign="middle" nowrap><img src="images/icons/envelope.gif" al	ign="absmiddle" title="{t}Message manager{/t}"></td>
						<td class="coolButton" onclick="addworkspace('{t}Change password{/t}','./chkpass.php');" onmouseover="SetInfo('{t}Change password{/t}','')" onmouseout="HideInfo();" valign="middle" nowrap><img src="images/icons/key.gif" align="absmiddle" title="{t}Change personal password{/t}"></td>
						<td class="coolButton" onclick="ToggleTree();" id="treebutton" onmouseover="SetInfo('{t}Ximdex tree{/t}','')" onmouseout="HideInfo();" valign="middle" nowrap><img src="images/icons/tree_ximdex.gif" align="absmiddle" title="{t}Ximdex tree{/t}"></td>
						<td class="coolButton" onclick="ToggleStatus();" id="statusbutton" onmouseover="SetInfo('{t}Status bar{/t}','')" onmouseout="HideInfo();" valign="middle" nowrap><img src="images/icons/hand.gif" align="absmiddle" title="{t}Status bar{/t}"></td>
						<td class="coolButton" onclick="ayuda();" onmouseover="SetInfo('{t}Help{/t}','')" onmouseout="HideInfo();" valign="middle" nowrap><img src="images/icons/help.gif" align="absmiddle" title="{t}Help{/t}"></td>
						<td class="coolButton" onclick="LogOut();" onmouseover="SetInfo('{t}Close session{/t}','')" onmouseout="HideInfo();" valign="middle" nowrap><img src="images/icons/logout.gif" align="absmiddle" title="{t}Close Session{/t}"></td>
					</tr>
				</table>

			</td>
		</tr>
	</table>
</div>

</body>
</html>
