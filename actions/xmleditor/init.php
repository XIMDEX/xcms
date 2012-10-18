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


XSession::check();

$nodeID = $_GET["nodeid"];

if($nodeID) {
	$userID = XSession::get('userID');
	$node		 = new Node($nodeID);
	$acquireLock = $node->Block($userID);
	$lockDate	 = $node->GetBlockTime();
	$path = $node->getPath();
	$pathEscaped = htmlentities($path);

	$template = new Node ($node->class->getTemplate());
	$templateName = htmlentities($template->get('Name'));

	if(!$acquireLock)
		$lockUser = $node->IsBlocked();
	else
		$lockUser = $userID;

	$user = new User($lockUser);
	$userName = $user->GetRealName();

	$strDoc = new StructuredDocument($nodeID);
	if($strDoc->GetSymLink())
		$isSymLink = true;
	else
		$isSymLink = false;

	if($acquireLock && !$isSymLink)
		{
?>
<HTML xmlns:edx>
<head>
<title>editor ximDEX v 2.0</title>
<meta HTTP-EQUIV="imagetoolbar" CONTENT="no" />
<script src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/js/ximdex_common.js" type="text/javascript"></script>
<style>
.edx { behavior:url(<?php echo Config::getValue('UrlRoot'); ?>/actions/xmleditor/edx.htc); }

.edx {
	-moz-binding: url(<?php echo Config::getValue('UrlRoot'); ?>/actions/xmleditor/bindings.xml#edx.htc);

}
span{
	cursor: text;
}
</style>


<script type="text/javascript">
var objeto_global;
var ev_global = new Object();
var Xnodeid = "<?php echo $nodeID?>";
<?php $parentid = $node->GetParent();
  $nodeparent = New Node ($parentid);
  $grandparentid = $nodeparent->GetParent();

  $project = $node->GetProject();
$nodeproj = New Node ($project);
  $nomproject = $nodeproj->GetNodeName();
 ?>
var Xnodeparent = "<?php echo $parentid?>";
var XnodeGparent = "<?php echo $grandparentid?>";

//variables to be used on gesdoc.js (v3.00)
var echodocmapper = "<?php echo Config::getValue('UrlRoot'). "/actions/xmleditor/docmapper.php?nodeid=".$nodeID;?>";
var echonodeid = "<?php echo $nodeID?>";
var ximdexurlroot = "<?php echo Config::getValue('UrlRoot'); ?>";

var params = new Array();
var paramsH = new Array();
var paramsE = new Array();
var campo_activo ;

</script>
<script type="text/javascript" src="<?php echo Config::getValue('UrlRoot'); ?>/extensions/jquery/jquery-1.4.2.min.js"> </script>
<script type="text/javascript" src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/js/genericaseditor.js"> </script>
<script type="text/javascript" src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/js/gesdoc.js"> </script>


<link rel="STYLESHEET" type="text/css" href="<?php echo Config::getValue('UrlRoot'); ?>/actions/xmleditor/inc/estilo_editor.css"/>
<link rel="STYLESHEET" type="text/css" href="<?php echo Config::getValue('UrlRoot'); ?>/xmd/style/estilo_ximDEX.css"/>
 <link rel="STYLESHEET" type="text/css" href="<?php echo Config::getValue('UrlRoot'); ?>/actions/xmleditor/inc/estilo_proyecto1.css"/>
 <link rel="STYLESHEET" type="text/css" href="<?php echo Config::getValue('UrlRoot'); ?>/actions/xmleditor/inc/estilo_proyecto2.css"/>
  <link rel="STYLESHEET" type="text/css" href="<?php echo Config::getValue('UrlRoot'); ?>/actions/xmleditor/inc/estilo_proyecto3.css"/>
   <link rel="STYLESHEET" type="text/css" href="<?php echo Config::getValue('UrlRoot'); ?>/actions/xmleditor/inc/estilo_proyecto4.css"/>

<?php
	$idSection = $node->GetSection();

	$sectionNode = new Node($idSection);
	$idCssFolder = $sectionNode->GetChildByName('css');

	$cssFolder = new Node($idCssFolder);
	$cssFiles = $cssFolder->GetChildren();

	if (sizeof($cssFiles) > 0) {
		foreach ($cssFiles as $idCss) {
			$cssNode = new Node($idCss);
			$css = Config::GetValue('UrlRoot') . Config::GetValue('NodeRoot') .
				$cssNode->GetRelativePath($project);
			echo '<link rel="stylesheet" type="text/css" href="' . $css . '"/>';
		}
	}

?>


</head>
<body id=bodyid leftmargin=0 rightmargin=0 topmargin=0 bottommargin=0 style="overflow-y:hidden;overflow-x:hidden;background-color:#ffffff;" onresize="recoloca();" onload="recorta();inicializa();crea_globales();">
<div>
<div>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="22">
	<tr>
		<td background="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/fondo_barra_peq.gif" class="normal">plantilla: <?php echo $templateName; ?><input type="Hidden" name="n_archivo2" id="n_archivo2" value="<?php echo $path;?>"><a href="#" title="<?php echo $pathEscaped;?>"><img src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/ruta.gif" title="<?php echo $pathEscaped;?>" alt="<?php echo $pathEscaped;?>" border="0" align="middle"></a><strong>Archivo:</strong> <input type="Text" class="cajaxg" id="n_archivo" name="n_archivo" title="<?php echo $pathEscaped;?>" alt="<?php echo $pathEscaped;?>" value="" style="width : 300px;"></td>
		<td height="22" rowspan="2" align="right" background="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/fondo_barra2.gif" valign="middle"><img border="0" src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/separa_g.gif"><img border="0" src="../xmd/images/icons-editor/ximEDITOR.gif" hspace="4" style="margin-bottom: 15px;"></td>
	</tr>
	<tr>	<td align="left" background="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/fondo_barra.gif" valign="middle" class="normal" width="100%">

	<?php
	    $nodeTypeId = $node->get('IdNodeType');
	    $nodeType = new NodeType($nodeTypeId);
	    $nodeTypeName = $nodeType->get('Name');

	    if ($nodeTypeName == "XimNewsNewLanguage") {
	?>
	    <input type="button" onclick="document.getElementById('publicar').value=1;save();"
		name="guardar2" value="Guardar y Publicar"/>
	<?php
	    }
	?>
			&nbsp;<a href="#" id=idsave disabled=true onclick="save();" title="Guardar"><img src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/save.gif" border="0" align="middle"></a>&nbsp;<img src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/separa.gif" border="0" align="top">
&nbsp;<!--input type="checkbox" checked align="middle" onclick="if(this.checked) alert(_('While this box is been ticked, preview will be opened on new windows.'));" name="tabview" id="tabview"--><select id="channellist" name="channellist" class="normal" style="width: 75px;	vertical-align: middle;">
<?php
$doc = new StructuredDocument($nodeID);
$channelList = $doc->GetChannels();
$nod = new Node();
foreach($channelList as $channel)
	{
	$nod->SetID($channel);
	echo "<option value='".$channel."'>".$nod->GetNodeName()."</option>";
	}
?>
</select><strong>&raquo;</strong><a href="javascript:void(null);" onclick="preview();" title="Preview"><img border="0" src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/preview.gif" title="<?php echo _('Preview the document'); ?>" align="middle"></a>&nbsp;
<a href="javascript:void(null);" onclick="showXML();" class="enlace" title="mostra c&oacute;digo XML"><img border="0" src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/show_xml.gif" align="middle" hspace="4" alt="mostrar c&oacute;digo XML"></a>&nbsp;
&nbsp;<img src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/separa.gif" border="0" align="top">&nbsp;
<a href="#" id=idundo disabled=true onclick="document.getElementById('edxid').undo();" title="Deshacer"><img style="visibility: hidden;" src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/undo.gif" border="0" align="middle"></a>&nbsp;
<a href="#" id=idredo disabled=true onclick="document.getElementById('edxid').redo();" title="Rehacer"><img style="visibility: hidden;" src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/redo.gif" border="0" align="middle"></a>&nbsp;
&nbsp;<img src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/separa.gif" border="0" align="top">&nbsp;
<a href="#" id=idmovedown disabled=true onclick="movedown();" title="Bajar el bloque seleccionado"><img style="visibility: hidden;" src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/movedown.gif" border="0" align="middle"></a>&nbsp;
<a href="#" id=idmoveup disabled=true onclick="moveup();" title="Subir el bloque seleccionado"><img style="visibility: hidden;" src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/moveup.gif" border="0" align="middle"></a>&nbsp;
&nbsp;<img src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/separa.gif" border="0" align="top">&nbsp;
<a href="#" id=idlink disabled=true onclick="aplica(this, 'enlace');" title="Insertar enlace"><img src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/link.gif" style="visibility: hidden;" border="0" align="middle"></a>&nbsp;
<a href="#" id=idstile disabled=true onclick="aplica(this, 'estilo');" title="Insertar estilo"><img src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/estilo.gif" style="visibility: hidden;" border="0" align="middle"></a>&nbsp;
<a href="#" id=idintro disabled=true onclick="aplica(this, 'salto_parrafo');" title="Insertar salto de p&aacute;rrafo"><img src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/intro.gif" style="visibility: hidden;" border="0" align="middle"></a>&nbsp;
</td>
</tr>
</table>
</div>
<iframe application="yes" id="idblocker" style="width: 98%; height: 13px; margin-top: 3px; border: 1px solid black; visibility: hidden;" scrolling="no" src="<?php echo Config::getValue('UrlRoot'); ?>/actions/xmleditor/blocknode.php?nodeid=<?php echo $nodeID;?>"></iframe>
<iframe application="yes" id="saveiframe" name="saveiframe" style="width: 98%; height: 13px; margin-top: 3px; border: 1px solid black; visibility: hidden;" scrolling="no" src=""></iframe>


<form target="saveiframe" action="<?php echo Config::getValue('UrlRoot'); ?>/actions/xmleditor/savedoc.php" method="post" id="saveform" name="saveform">
	<input type="hidden" name="nodeid" id="nodeid" value="<?php echo $nodeID?>">
	<input type="hidden" name="contenido" id="contenido">
	<input type="hidden" name="version" id="version">
	<input type="hidden" id="publicar" name="publicar" value="0">
</form>
<form target="blank" action="<?php echo Config::getValue('UrlRoot'); ?>/xmd/loadaction.php?action=prevdoc" method="post" id="prevform" name="prevform">
	<input type="hidden" name="nodeid" id="nodeid" value="<?php echo $nodeID?>">
	<input type="hidden" name="contenido" id="contenido">
	<input type="hidden" name="channel" id="channel">
</form>


<div id="mastercontenedor" style="position:relative;left: 0px; top: -45px;width:100%; background-color:#ffffff; z-index: 10;" cellpadding=0 cellspacing=0 border=1>
	<div style="overflow-y:auto;" class="edx" id="edxid" onEdxDocumentChange="docChange();" onEdxSelectionChange="selChange();" onclick="window.document.getElementById('PVcontenedor').style.visibility = 'hidden';" edxtemplate="root" xmlurl="<?php echo Config::getValue('UrlRoot'); ?>/actions/xmleditor/docmapper.php?nodeid=<?php echo $nodeID; ?>" oncontextmenu="return false;"><br /><br /><img src="<?php echo Config::getValue('UrlRoot'); ?>/xmd/images/icons-editor/cargando.gif" alt="" border="0" /></div>
</div>

<div id="PVcontenedor" style="position: absolute; left: -1000px; top: 0px; width: 270px; z-index: 200; height: 300px;visibility: hidden;"></div>

<iframe id="Vmodal" src="" style="position: absolute; left: -1000px; top: 150px; width: 400px; z-index: 200; height: 300px;visibility: hidden;"></iframe>
<input type="hidden" name="toFirefox" id="toFirefox" value="true" />
<script type="text/javascript">

if (navegador == "firefox15")
	{
	document.getElementById('edxid').style.height = (window.innerHeight - 250) + "px";

	}

</script>
</body>

</html>
<?php
		}
	else
		{
		setlocale (LC_TIME, XSession::get("locale"));
		if(!$isSymLink)
			{
			echo '<script>alert("No se pudo abrir el documento.\\nBloqueado por '.$userName.' desde el '.strftime("%A a las %H:%M:%S", $lockDate).'.");parent.deletetabpage(parent.selected);</script>';
			echo _("Document is blocked by other user.");
			}
		else
			{
			echo '<script>alert(_("Document could not be opened. It is a symbolic link."));parent.deletetabpage(parent.selected);</script>';
			echo _("Document is a symbolic link.");
			}
		}
	}
else
	{
	echo _("Error: Document was not found.");
	}
?>
