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








ModulesManager::file('/inc/utils.php');

function PrintImagesBrowser($nodeID)
{
	$src = preg_replace("/modules/ximNEWS/actions/.*/init.php", "/xmd/images", Config::getValue('UrlRoot').ModulesManager::path('ximNEWS').'/actions/browserimages/init.php');
	$cierreVentana = "<img src='" . $src . "/botones/cerrar.gif' alt='' border='0'>";
	$crear = "<img src='" . $src . "/botones/crear.gif' alt='Crear' border='0'>";

	?>

	<script type="text/javascript" src="..<?php echo ModulesManager::path('ximNEWS'); ?>/js/browser_images.js"></script>
	<form method=POST name="cdx_form">
	<INPUT id="nodoIDF" TYPE=hidden NAME='nodeid' VALUE='<?php  echo $nodeID;?>' class=ecajag>
	<br/>
	<table class="tabla" width="97%" align="center" cellpadding="3">
		<tr>
			<td class="filacerrar" align="right"><a href="javascript:parent.deletetabpage(parent.selected);" class="filacerrar"><?php echo _('close window'); echo $cierreVentana; ?></a></td>
		</tr>
		<tr>
			<td align="center" class="filaclara"><br>
				<table id="receptor" align=center class=tabla width='97%'>
					<tr>
						<td class="cabeceratabla" colspan="3"><?php echo _('Image browser');?> &raquo;</td>
					</tr>
					<tr><td class="filaoscura" colspan="3">&nbsp;<?php _('Select the way you want to explore the image batch:'); ?><span id="typeView"><input type="radio" name="grupoforma" value="lista" onclick="changeViewR(this);" checked /><?php echo _('List'); ?>
								<input type="radio" name="grupoforma" value="miniaturas" onclick="changeViewR(this);" /><?php echo _('Miniatures'); ?></span></td>
					</tr>
					<tr>
						<td colspan="3" class="filaclara">
							<table width="100%" height="100%">
								<tr>
									<td><?php echo _('Select the image batch:'); ?></td>
									<td><div id="tit_area2"><?php echo _('Select the image you want to preview:'); ?></div></td>
									<td><div id="tit_area3"><?php echo _('Preview'); ?></div></td>
								</tr>
								<tr>
									<td width="25%"><div id="area1" style="overflow:auto;height:220px;" />
									</td>
									<td width="50%"><div id="area2" style="overflow:auto;height:220px;" />
									</td>
									<td width="25%""><div id="area3" style="overflow:auto;height:220px;" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="filaoscura" colspan="3">&nbsp;<?php echo _('Select the zoom factor in which you want to view the images'); ?>	<span id="selectScale">
							<input type="radio" name="grupozoom" value="0.1" onclick="changeScaleR(this);" />10%
							<input type="radio" name="grupozoom" value="0.25" onclick="changeScaleR(this);" />25%
							<input type="radio" name="grupozoom" value="0.5" onclick="changeScaleR(this);" checked />50%
							<input type="radio" name="grupozoom" value="0.75" onclick="changeScaleR(this);" />75%</span>
						</td>
					</tr>
          			</table>
			</td>
		</tr>
	</table>
	</form>
	<script language="JavaScript" type="text/javascript">
	<!--
	show_LoteImagenes();
	//-->
	</script>

	<?php
}


////
//// Action flow init.
////

if( $_POST["containerid"] && $_POST["createnews"] ) {

}
else {
    // When entering to the page, we receive by get and we paint the form
    //gPrintHeader();
    //gPrintBodyBegin();

	$nodes = $_GET["nodes"];
	$nodeid = $nodes[0];

    if ($nodeid) {
	//$nodeID = $_GET["nodeid"];
	PrintImagesBrowser($nodeid);
    }
    else
	gPrintMsg(_("ERROR in parameters"));
    //gPrintBodyEnd();
}
?>