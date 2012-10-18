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



if(!defined('XIMDEX_ROOT_PATH')) {
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__).'/../../'));
}
include_once(XIMDEX_ROOT_PATH.'/inc/utils.inc');

XSession::check();

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                                                     // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");	// HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");										// HTTP/1.0


////
//// Inicio del flujo de la acción.
////

if (isset($_GET["nodeid"]))
	{
	$nodeID		= $_GET["nodeid"];
	$node		 = new Node($nodeID);
	$userID = XSession::get("userID");
	$acquireLock = $node->Block($userID);
	$lockDate	 = $node->GetBlockTime();

	if(!$acquireLock)
		$lockUser = $node->IsBlocked();
	else
		$lockUser = $userID;

	$user = new User($lockUser);
	$userName = $user->GetRealName();

	$config = new Config();
?>
<html>

<head>
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<META http-equiv=Refresh content="<?php echo ($config->GetValue('BlockExpireTime')/2);?>">
<?php
//gPrintHeaderContent(); It is not necessary because it is not an action
?>
</head>

<body>
<table border="0" width="100%" cellpadding="1" cellspacing="0">
	<tr>
		<td width="10%" nowrap>
		<b>Estado:</b> Bloqueado por
<?php
setlocale (LC_TIME, XSession::get("locale"));
if($acquireLock)
	echo '<font color="green">'.$userName.'</font> desde el '.strftime("%A a las %H:%M:%S", $lockDate).'&nbsp;&nbsp;</td>';
else
	{
	echo '<font color="red">'.$userName.'</font> desde el '.strftime("%A a las %H:%M:%S", $lockDate).'&nbsp;&nbsp;</td>';
	echo '<td><marquee>Próximo intento a las '.strftime("%H:%M:%S", (time()+($config->GetValue('BlockExpireTime')/2))).'</marquee>'.'</td>';
	}
?>

	</tr>
</table>
<?php
	gPrintBodyEnd();
	}
?>
