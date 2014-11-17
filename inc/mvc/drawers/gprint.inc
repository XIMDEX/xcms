<?php 

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)			      *
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *  
 *                                                                            *
 *                                                                            *
 ******************************************************************************/











if (!defined("XIMDEX_ROOT_PATH")) {
	define("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__) . "/../../../"));
}

include_once(XIMDEX_ROOT_PATH . "/inc/model/action.php");
include_once (XIMDEX_ROOT_PATH . "/inc/model/node.inc");
require_once(XIMDEX_ROOT_PATH . "/inc/utils.php");

function gPrintHeader($scroll='no')
	{
?>
<html>
<head>
<?php gPrintHeaderContent();?>
</head>
	<body scroll=<?php echo $scroll?> onresize="resize_caja();" onload="resize_caja();">
<?php
	}
	
function gPrintReloadNode($nodeID)
	{
?>
<script type="text/javascript">
	parent.parent.frames['toolbar'].reloadNode(<?php  echo $nodeID; ?>);
</script>
<?php 
	}
	
function gPrintReloadParent($nodeID)
	{
$node = new Node($nodeID);
?>
<script type="text/javascript">
	parent.parent.frames['toolbar'].reloadNode(<?php  echo $node->GetParent(); ?>);
</script>
<?php 
	}
	
function gPrintSelectNode($nodeID)
	{
?>
<script type="text/javascript">
	parent.parent.frames['toolbar'].SetSelectedNode(<?php  echo $nodeID; ?>);
</script>
<?php 
	}

function gPrintHeaderContent() {

		$stylesPath = Config::GetValue('UrlRoot') .'/xmd/style';
?>
		<link rel="STYLESHEET" type="text/css" href="<?php echo "$stylesPath/style.css" ?>">
		<link rel="STYLESHEET" type="text/css" href="<?php echo "$stylesPath/ximdex.css" ?>">

		<script type="text/javascript">
				
	function resize_caja()
	{
		var nav;
		if (navigator.appName=="Microsoft Internet Explorer") nav=true; //if it is true, it is ie.
		
		if (nav)
		{
			if (document.getElementById('editor'))
			{
				my_winH = document.body.offsetHeight;
				document.getElementById('editor').style.height = my_winH - 52;
				my_winW = document.body.offsetWidth;
				document.getElementById('editor').style.width = my_winW - 52;
				
			
			}
			if (document.getElementById('actioncontainer'))
			{
				my_winW = document.body.offsetWidth;
				document.getElementById('actioncontainer').style.width = my_winW;
				my_winH = document.body.offsetHeight;
				document.getElementById('actioncontainer').style.height = my_winH - 60;
			}
			
		}
	
		if(!nav)
		{
			if(document.getElementById('editor'))
			{
				document.getElementById('editor').style.height= (window.innerHeight - 75) + "px";
				document.getElementById('editor').style.width= (window.innerWidth - 75) + "px";

			}
		
		
			if (document.getElementById('actioncontainer'))
			{
				document.getElementById('actioncontainer').style.height = (window.innerHeight - 60) + "px";
				document.getElementById('actioncontainer').style.width= (window.innerWidth - 60) + "px";

			}
			
		}	
	
	}

</script>


<?php 
	}

function gPrintHeaderJS($escripts)
	{
?>
	<html>
    <head>
<?php gPrintHeaderContent();?>
<?php 
	echo "\n" . $escripts . "\n";
?>
</head>
<?php 
	}


function gPrintBodyBegin()
	{
		
	$actionID = isset($_GET['actionid']) ? $_GET['actionid'] : null;
	$nodeID = isset($_GET['nodeid']) ? $_GET['nodeid'] : null;

	if($nodeID && $actionID)
		{
		$action = new Action($actionID);
		$node = new Node($nodeID);
		
		// If the performed action is "deletenode" and the node was already deleted, the node should be taken from session.
		// This in order to show the deleted node path.
		if( $action->Command == 'deletenode' && is_null( $node->nodeID ) ) {
			$node = unserialize( XSession::get( 'deletedNode' ) );
			// If node could not be obtained from session var, the node is created with the ID passed by GET, even if this does not exista
			if( !is_object($node) ) $node = new Node($nodeID);
			XSession::set( 'deletedNode', null );
		}

	
?>
	<table border="0" width="100%" height="100%" cellpadding="1" cellspacing="0">
		<tr>
			<td height="15" valign="bottom">
			<div class="tituloseccion">&nbsp;<?php echo $action->GetName();?></div>
			</td>
		</tr>
		<tr>
			<td height="40">
			<table class="actionbar">
				<tr>
					<td><b><?php echo _('Description'); ?>:</b>
					<?php echo $action->GetDescription();?>
					</td>
				</tr>
				<tr>
					<td><b><?php echo _('Node'); ?>:</b>
					<?php
					
					$path  = pathinfo( $node->GetPath() );
					
					//modificacion para romper la ruta cuando es muy larga, idemxime
					$ruta =  (!empty($path["dirname"]) ? $path['dirname'] . '/<b>' : '') . (!empty($path["basename"]) ? $path['basename'] : '') . "</b>";
					$ruta = str_replace("/", "/ ",$ruta);
					echo $ruta;
					?>
					</td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td id="actioncontainer" valign='top'>
			<div class="actioncontainer">
<?php 
		}
	else
		{
		// AHD: Label <body> is already written in the function gPrintHeader.
?>
		<!--body onresize="resize_caja();" onload="resize_caja();"-->
<?php 
		}
	}

function gPrintBodyEnd()
	{
	$actionID = Request::get("actionid");
	$nodeID = Request::get("nodeid");

	if($nodeID && $actionID)
		{
?>
			</div>
			</td>
		</tr>
	</table>
<?php 
		}
?>
	</body>
	</html>
<?php 
}

function gPrintMsg($msg) {
	echo "<br>" . $msg;
}

function gPrintMessageWindow ($title, $msg, $error = false) {

		gPrintHeader();
		gPrintBodyBegin();

?>
		<br>
	<table  class="tabla" width="560" align="center" cellpadding="2">
		<tr>
			<td class="filacerrar" align="right"><a href="javascript:parent.deletetabpage(parent.selected);" class="filacerrar"><?php echo _('Close window'); ?><img src="images/botones/cerrar.gif" alt="" border="0"></a></td>
		</tr>
		<tr>
			<td align="center" class="filaclara">
				<table align=center class=tabla width=420 cellpadding="1" cellspacing="1">
					<tr>
						<td class="cabeceratabla" colspan="2"><?php  echo $title ?> &raquo;</td>
					</tr>
<?php 
		gPrintMsg("<tr><td class='filaoscuranegritac'>".$msg."</td></tr>");
		if ($error) 
			{
?>
				<table align=center border="0" width=420 cellpadding="1" cellspacing="1">
					<tr>
	<td align="right"><a href="javascript:history.back();"><img src="images/botones/volver.gif" alt="Volver" border="0"></a></td>
					</tr>
				</table>
<?php  
		}
?>		
				</table>
<?php 	
		gPrintBodyEnd();
}

function gPrintMessageWindow2 ($title, $msg, $error = false) {

	  $src = Config::getValue('UrlRoot') . '/xmd/images';
	  error_log('gprint::gPrintMessageWindow2 - ' . $src);
	  
	  $cierreVentana = "<img src='" . $src . "/botones/cerrar.gif' alt='' border='0'>";
	  $volver = "<img src='" . $src . "/botones/volver.gif' alt='Volver' border='0'>";
		gPrintHeader();
		gPrintBodyBegin();

?>
		<br>
	<table  class="tabla" width="560" align="center" cellpadding="2">
		<tr>
			<td class="filacerrar" align="right"><a href="javascript:parent.deletetabpage(parent.selected);" class="filacerrar"><?php echo _('Close window'); echo $cierreVentana; ?></a></td>
		</tr>
		<tr>
			<td align="center" class="filaclara">
				<table align=center class=tabla width=420 cellpadding="1" cellspacing="1">
					<tr>
						<td class="cabeceratabla" colspan="2"><?php  echo $title ?> &raquo;</td>
					</tr>
<?php 
		gPrintMsg("<tr><td class='filaoscuranegritac'>".$msg."</td></tr>");
		if ($error) 
			{
?>
				<table align=center border="0" width=420 cellpadding="1" cellspacing="1">
					<tr>
	<td align="right"><a href="javascript:history.back();"><?php echo $volver; ?></a></td>
					</tr>
				</table>
<?php  
		}
?>		
				</table>
<?php 	
		gPrintBodyEnd();
}

?>
