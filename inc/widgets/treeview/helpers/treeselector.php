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

if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../../"));

ModulesManager::file('/inc/utils.inc');

XSession::check();
$urlroot = Config::getValue('UrlRoot');

/* Verifica variables $_GET
 * emartos 04/05/2007
 */
$rootNode = new Node();
$targetNodeID = isset ($_GET['targetid']) ? $_GET['targetid'] : null;
$contentType = isset ($_GET['contenttype']) ? $_GET['contenttype'] : null;
$rootID = isset ($_GET['nodeid']) ? $_GET['nodeid'] : null;
$filterType = isset ($_GET['filtertype']) ? $_GET['filtertype'] : null;
$nodeType = isset ($_GET['nodetype']) ? $_GET['nodetype'] : null;

if(!isset ($rootID))
	{
	$config = new Config();
	$rootID = $config->GetValue("ProjectsNode");
	}
$rootNode->SetID($rootID);
?>
<html>
<head>

<script type="text/javascript">
var xmdRoot = "<?php echo $urlroot; ?>/xmd/"
</script>

<script type="text/javascript" src="<?php echo $urlroot; ?>/inc/widgets/treeview/js/xtree.js"></script>
<script type="text/javascript" src="<?php echo $urlroot; ?>/inc/widgets/treeview/js/xmlextras.js"></script>
<script type="text/javascript" src="<?php echo $urlroot; ?>/inc/widgets/treeview/js/xloadtree.js"></script>
<script src="<?php echo $urlroot; ?>/xmd/js/ximdex_common.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="<?php echo $urlroot; ?>/xmd/style/style.css" />
<link type="text/css" rel="stylesheet" href="<?php echo $urlroot; ?>/inc/widgets/treeview/css/xtree.css" />

<style type="text/css">

body {
	background:	white;
	color:		black;
}

</style>
</head>
<body scroll="no">
<TABLE BORDER="0" WIDTH="100%" HEIGHT="100%" CELLPADDING="1" CELLSPACING="0">
	<TR>
		<TD HEIGHT="10" ALIGN="center" VALIGN="top" STYLE="padding-right: 2px;">
		<a href="#" onclick="if(tree.getSelected()) { tree.getSelected().reload(); if(!tree.getSelected().open) tree.getSelected().expand();}" class="treebutton"><?php _("Reload node"); ?></a>
		<form name="Formrecarga" style="margin: 0px;">Paginador: <input type="Radio" value="25" name="Frecarga" id="Frecarga" style="width:10px; vertical-align: middle;" checked> 25
		<input type="Radio" value="50" name="Frecarga" style="width:10px; vertical-align: middle;" > 50
		<input type="Radio" value="75" name="Frecarga" style="width:10px; vertical-align: middle;"> 75
		<input type="Radio" value="100" name="Frecarga" style="width:10px; vertical-align: middle;"> 100
		</form>
		</TD>

	</TR>
	<TR>
		<TD VALIGN="top">
		<div id="treeDiv" style="width: 100%; height: 100%; overflow: auto; padding-left: 5px; border: 0px solid black;">
		<br />
<script type="text/javascript">

var nodes = new Array();
function SetSelectedNode(nodeID)
	{
	parent.parent.frames['toolbar'].SetSelectedNode(nodeID);
	}

/// XP Look
webFXTreeConfig.rootIcon		= xmdRoot+"/inc/widgets/treeview/images/folder.png";
webFXTreeConfig.openRootIcon	= xmdRoot+"/inc/widgets/treeview/images/openfolder.png";
webFXTreeConfig.folderIcon		= xmdRoot+"/inc/widgets/treeview/images/folder.png";
webFXTreeConfig.openFolderIcon	= xmdRoot+"/inc/widgets/treeview/images/openfolder.png";
webFXTreeConfig.fileIcon		= xmdRoot+"/inc/widgets/treeview/images/file.png";
webFXTreeConfig.lMinusIcon		= xmdRoot+"/inc/widgets/treeview/images/Lminus.png";
webFXTreeConfig.lPlusIcon		= xmdRoot+"/inc/widgets/treeview/images/Lplus.png";
webFXTreeConfig.tMinusIcon		= xmdRoot+"/inc/widgets/treeview/images/Tminus.png";
webFXTreeConfig.tPlusIcon		= xmdRoot+"/inc/widgets/treeview/images/Tplus.png";
webFXTreeConfig.iIcon			= xmdRoot+"/inc/widgets/treeview/images/I.png";
webFXTreeConfig.lIcon			= xmdRoot+"/inc/widgets/treeview/images/L.png";
webFXTreeConfig.tIcon			= xmdRoot+"/inc/widgets/treeview/images/T.png";

function busca_numMaxFiles()
	{
	if (self.document.getElementById('Frecarga')){
			 for (m = 0; m < Formrecarga.Frecarga.length; m++){
					if (Formrecarga.Frecarga[m].checked){
					return eval(Formrecarga.Frecarga[m].value);
					}
				}
			 }
	}

var numMaxFiles_origen = busca_numMaxFiles();
<?php
	$targetPath = sprintf("treeselectordata.php?nodeid=%s&contenttype=%s&targetid=%s&filtertype=%s&nodetype=%s",
					urlencode($rootNode->get('IdNode')),
					urlencode($contentType),
					urlencode($targetNodeID),
					urlencode($filterType),
					urlencode($nodeType)
	);%
?>
var tree = new WebFXLoadTree("<?php echo $rootNode->GetNodeName()?>", "<?php echo $targetPath; ?>", "javascript: parent.setInfo('<?php echo $rootNode->GetPath()?>','<?php echo $rootNode->GetID()?>')", "classic", "../../xmd/images/icons/<?php echo urlencode($rootNode->nodeType->GetIcon()); ?>","../../xmd/images/icons/<?php echo $rootNode->nodeType->GetIcon()?>");

document.write(tree);
//tree.expand();
</script>
		</div>
		<script type="text/javascript">
		if (navegador == "firefox15")
				{
					document.getElementById("treeDiv").style.height= (window.innerHeight - 32) + "px";
					document.getElementById("treeDiv").style.width= (window.innerWidth - 8) + "px";
				}
		</script>
		</TD>
	</TR>
</TABLE>
</body>
</html>
