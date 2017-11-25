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
	%=js_widgets%
	%=css_widgets%
	<title>{t}Pool Preview{/t}</title>
		{literal}
	<script type="text/javascript">	
		$(document).ready(function(){
			initPoolPreview();
		});
	</script>
</script> 
	{/literal}
</head>
<body>
	<div class="leftPanels">
		<input type="button" value="expandir todo" onclick="tooglePanels('true')">
		<input type="button" value="colapsar todo" onclick="tooglePanels('false')">
		<div class="previewActual panel">
			<h3>Information</h3>
			<div class="panelContentPoolPreview">
				<input type="hidden" name="idnodePreview">
				<input type="hidden" name="idversionPreview">
				<input type="hidden" name="idsubversionPreview">
				<div class="infoPreview">
				</div>
			</div>
		</div>
		<div class="navigate panel">
			<h3>Navigate</h3>
			<div class="panelContentPoolPreview">
				<div class="mysuggester">
					<suggester handler="my_suggester" label="Seleccione nodo"/>
				</div>
				<div class="mytree">
					<treeview handler="my_treeview" />
				</div>
			</div>
		</div>
	</div>
	<div class="poolPreviewContent">
		<div class="headerPoolPreview">
			<div class="headerChannels">
				<label class="aligned">Channels</label>
				<select name="channel" class="channel">
					{foreach from=$channels key=id_channel item=channel_name}
					<option value="{$id_channel}">{$channel_name}</option>
					{/foreach}
				</select>
			</div>
			<div class="headerLabels">
				<label>Labels</label>
				<select class="labelsDropDown"></select>
			</div>
		</div>
		<div>
			<div class="poolPreview">
				<iframe class="preview_loader"></iframe>
			</div>
		</div>
		<div class="footerPoolPreview">
			<div class="version-slider"></div>
				<div class="version-scroll">
				  <div class="version-holder"></div>
				</div>
		</div>
	</div>	
	<div class="rightPanels" align="right">
		<div class="linksBy panel">
			<h3>Linked by</h3>
			<div class="panelContentPoolPreview">
				<div class="ul-linksBy"></div>
			</div>
		</div>
		<div class="linksTo panel">
			<h3>Links To</h3>
			<div class="panelContentPoolPreview">
				<div class="ul-linksTo"></div>
			</div>
		</div>
		<div class="documentsByTags panel">
			<h3>Navigate by tags</h3>
			<div class="panelContentPoolPreview">
				<div>
					{* div init in poolPreview, load a select with all the labels *}
					<div class="labelsForPoolPreview"></div>
					{* div init in poolPreview.js when the user select a label*}
					<div class="ul-DocumentsBylabels"></div>
				</div>
			</div>
		</div>
	</div>
	<div id="actionManagerList" style="display:none">
		<p>hello</p>
	</div>
	<div id="testtt" style="display:none">
		<p>test</p>
	</div>
</body>
</html>
