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



ModulesManager::file('/inc/utils.inc');
ModulesManager::file('/inc/helper/String.class.php');

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

if ($_POST["nodeid"])
	{
	$nodeID		 = $_POST["nodeid"];
	$docContent	 = $_POST["contenido"];
	$release	 = $_POST["version"];
	$node		 = new Node($nodeID);
	$userID		 = XSession::get('userID');
	$acquireLock = $node->Block($userID);
	$lockDate	 = $node->GetBlockTime();

	if(!$acquireLock)
		$lockUser = $node->IsBlocked();
	else
		$lockUser = $userID;

	$user = new User($lockUser);
	$userName = $user->GetRealName();

	?>
	<html>

	<head>
	<META HTTP-EQUIV="pragma" CONTENT="no-cache">
	<?php
//	gPrintHeaderContent(); No es un acción visible
	?>
	<script language="javascript" type="text/javascript">

		function redirectToAddToColector() {

			parent.parent.$('#bw1').browserwindow('openAction', {
				command: 'addtocolector',
				name: 'addtocolector',
				params: 'mod=ximNEWS&edit=1'
			}, [<?php echo $nodeID; ?>]);

			return;
		}
	</script>
	</head>

	<body>
	<table border="0" width="100%" cellpadding="1" cellspacing="0">
		<tr>
			<td width="10%" nowrap>
			<b>Intentando guardar:</b>
	<?php
	if($acquireLock)
		{
	setlocale (LC_TIME, XSession::get("locale"));
	$docContent = String::stripslashes($docContent);

		/// Quitamos la version de XML
		$docContent = preg_replace("/^\s*<\?\s*xml\s+version=\"1.0\"(\s+encoding=\"[a-sA-Z0-9-_]+\"){0,1}\s*\?>\s*/", "", $docContent);
		/// Quitamos la plantilla EDX
		$docContent = preg_replace("/^\s*<\?\s*edxview\s+.*?\/templatemapper.php\?nodeid=[0-9]*\s*\?>\s*/", "", $docContent);
		/// Qitamos la apertura <docxap>
		$docContent = preg_replace("/^\s*<\s*docxap\s*[^>]*>\s*/", "", $docContent);
		/// Quitamos las etiquetas titulo_pagina
		$docContent = preg_replace("/^\s*<\s*titulo_pagina\s*>[^<]*<\/\s*titulo_pagina\s*>/", "", $docContent);
		//$docContent = preg_replace("/^\s*<\s*titulo_pagina\s*>[^<]*<\/\s*titulo_pagina\s*>/", "", $docContent);
		$docContent = preg_replace("/\s*<\/docxap>\s*$/", "", $docContent);
		$docContent = preg_replace("/^\s*/", "", $docContent);

//		$xmlDoc = new StructuredDocument($nodeID);
		$xmlDoc = new Node($nodeID);
//		$nodeTypeId = $xmlDoc->get('IdNodeType');
//		$nodeType = new NodeType($nodeTypeId);
		$nodeTypeName = $xmlDoc->nodeType->get('Name');

		if($release == 'save'){
			$xmlDoc->SetContent($docContent, true);

			if ($nodeTypeName == "XimNewsNewLanguage") {
				// Persistence in database
				if ($xmlDoc->get('IdNode') > 0) {
				    $xmlDoc->class->updateNew();
				}
			}
		}

		if ($release == 'preview')
			{
			$xmlDoc->Preview($docContent);
			}
		if ($nodeTypeName == "XimNewsNewLanguage" && $_POST['publicar'] == 1) {
			setlocale (LC_TIME, XSession::get("locale"));
			 echo '<script>alert("Documento guardado correctamente.");redirectToAddToColector();</script><font color="green"><b>Guardado OK</b></font></td>';
		} else {
			setlocale (LC_TIME, XSession::get("locale"));
			echo '<script>alert("Documento guardado correctamente.");</script>';
		}
		}
	else
		{
		setlocale (LC_TIME, XSession::get("locale"));
		echo '<script>alert("Se produjo un error al guardar el documento.\\nBloqueado por '
			. $userName.' desde el '
			. strftime("%A a las %H:%M:%S", $lockDate)
			. '.");</script><font color="red">No Guardado:</font> bloqueado por <font color="red">'
			. $userName.'</font> desde el '.strftime("%A a las %H:%M:%S", $lockDate).'</td>';
		}
	?>

		</tr>
	</table>
	</body>

	</html>
	<?php
	gPrintBodyEnd();
	}
	?>
