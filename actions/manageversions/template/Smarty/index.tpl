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

<form method="post" name="mv_form" id="mv_form" action="{$action_url}">
<fieldset>
	<legend><span>{t}Available versions{/t}</span></legend>
	
	<input type="hidden" name="nodeid" value="{$id_node}" />
	<input type="hidden" name="nodetypename" value="{$node_type_name}" />
	<input type="hidden" name="version" value="" />
	<input type="hidden" name="subversion" value="" />
	
			<table class="versions">
				
				{foreach from=$versionList key=version item=versionInfo}
				

				{foreach from=$versionInfo item=subVersionList}
				
				<tr class="version-info">
					<td>
						<strong>{t}Version{/t} {$version}.{$subVersionList.SubVersion}</strong>
						
						<input type="hidden" name="row-version" value="{$version}" />
						<input type="hidden" name="row-subversion" value="{$subVersionList.SubVersion}" />
					</td>
					<td>{$subVersionList.Date}</td>
					<td>{$subVersionList.Name}</td>
					<td>{$subVersionList.Comment}</td>
					<td>
						{button label="Preview" class="prevdoc-button"}
					</td>
					<td>
						<select class="channellist" class="channellist" name="channellist" class="normal" style="width: 75px;	vertical-align: middle;">
						{foreach from=$channels key=id_channel item=channel}
							<option value="{$id_channel}">{$channel}</option>
						{/foreach}
						</select>
					</td>
					<td>
					{if $subVersionList.isLastVersion == 'false'}
						{button label="Recover" class="validate recover-button" message="Are you sure you want to recover this version?"}
					{/if}
					</td>
					<td>
						{button label="Delete" class="validate delete-button" message="Are you sure you want to delete this version?"}
					</td>
				</tr>
				{/foreach}
				{/foreach}
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<div id="preview" style="border:1px solid; display: none; position: absolute; z-index: 0; background-color:#ffffff; width:340; border: ;">
					<table width="100%" style="border:1px solid black; font-size: xx-small; font-family: arial;">
						<tr><td> <a href="javascript: closePrev();"><img src="{$_URL_ROOT}/xmd/images/close.png" border="0" align="middle"></a>{t}Preview{/t}</td></tr>
						<tr><td><iframe id="previewer" name="previewer" application="yes" style="position: absolute; z-index: 0; width:350px; height: 225px; left: 0; top: 25;" frameborder="100" scrolling="yes"></iframe></td></tr>
					</table>
					
			
			</div>

</fieldset>
</form>
