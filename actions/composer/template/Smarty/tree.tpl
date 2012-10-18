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
<script type="text/javascript" src="{$_URL_ROOT}{$base_action}resources/js/tree.js"></script>
<script type="text/javascript" src="{$_URL_ROOT}{$base_action}resources/js/xtree.js"></script>
<script type="text/javascript" src="js/xmlextras.js"></script>
<script type="text/javascript" src="{$_URL_ROOT}{$base_action}resources/js/xloadtree.js"></script>
<link type="text/css" rel="stylesheet" href="style/style.css" />
<link type="text/css" rel="stylesheet" href="{$_URL_ROOT}{$base_action}resources/css/xtree.css" />
<link type="text/css" rel="stylesheet" href="{$_URL_ROOT}{$base_action}resources/css/tree.css" />


</head>
<body scroll="no" onresize="cambia_altura();">
<TABLE BORDER="0" WIDTH="100%" HEIGHT="100%" CELLPADDING="1" CELLSPACING="0">
	<TR>
		<TD HEIGHT="10" ALIGN="center" VALIGN="bottom"  nowrap id='td_container_paginador'>
		<a href="#" onclick="compruebaSel();" class="treebutton" >{t}Reload node{/t}</a>
		<form name="Formrecarga" id='Formrecarga'>{t}Pager{/t}: <input type="Radio" value="25" name="Frecarga" id="Frecarga"  class='paginador_tree'>25
		<input type="Radio" value="50" name="Frecarga" class='paginador_tree' checked>50
		<input type="Radio" value="75" name="Frecarga" class='paginador_tree'>75
		<input type="Radio" value="100" name="Frecarga"  class='paginador_tree'>100
		</form>
		</TD>
	</TR>
	<TR>
		<TD VALIGN="top">
		
		<DIV id="treeDiv">

		<img src="images/pix_t.gif" alt="" border="0" height="4">

<script type="text/javascript">
var tree = new WebFXLoadTree("{$nodeName}", "{$composer_index}?method=treedata&nodeid={$nodeid}", "javascript: SetSelectedNode('{$nodeid}');", "classic","images/icons/{$nodeicon}","images/icons/{$nodeicon}","{$nodeid}");
{literal}
//alert(tree);
/// XP Look
webFXTreeConfig.rootIcon		= "images/tree/folder.png";
webFXTreeConfig.openRootIcon	= "images/tree/openfolder.png";
webFXTreeConfig.folderIcon		= "images/tree/folder.png";
webFXTreeConfig.openFolderIcon	= "images/tree/openfolder.png";
webFXTreeConfig.fileIcon		= "images/tree/file.png";
webFXTreeConfig.lMinusIcon		= "images/tree/Lminus.png";
webFXTreeConfig.lPlusIcon		= "images/tree/Lplus.png";
webFXTreeConfig.tMinusIcon		= "images/tree/Tminus.png";
webFXTreeConfig.tPlusIcon		= "images/tree/Tplus.png";
webFXTreeConfig.iIcon			= "images/tree/I.png";
webFXTreeConfig.lIcon			= "images/tree/L.png";
webFXTreeConfig.tIcon			= "images/tree/T.png";

document.write(tree);
tree.expand();

</script>
		</DIV>
		<script type="text/javascript">
			//treeDiv
			
			function cambia_altura(){
				if (navegador == "firefox15")
				{
					document.getElementById("treeDiv").style.height= (window.innerHeight - 32) + "px";
					document.getElementById("treeDiv").style.width= (window.innerWidth - 8) + "px";
				}
			}
			cambia_altura();
		</script>
		</script>
		</TD>
	</TR>
</TABLE>
{/literal}
</body>
</html>
