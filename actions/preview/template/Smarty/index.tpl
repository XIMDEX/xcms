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

<fieldset>
<legend><span>{t}Document preview{/t}</span></legend>
			<table align=center class="versions">
			<!--	<tr>
					<td>{t}Version{/t}</td>
					<td>{t}Date{/t}</td>
					<td>{t}User{/t}</td>
					<td>{t}New window{/t}</td>
					<td></td>
					<td>{$titulo_canal}</td>
					<td colspan="2"></td>

				</tr>-->

				<tr class="prevdoc">
					<td align="center" valign="middle"><strong>Version {$version}.{$subversion}</strong></td>
					<td align="left" nowrap>{$date}</td>
					<td align="left">{$user_name}</td>
					<td align="left"><input type="checkbox" checked align="middle" onclick="if(this.checked) alert('{t}As long as this box is ticked, preview will be opened in new windows.{/t}');" name="tabview" id="tabview" class="prevdoc_check"></td>
					{if ($nameNodeType != 'NodeHt')}
						<td align="left"><a href="{$_URL_ROOT}/xmd/loadaction.php?action={if ("BinaryFile" == $nameNodeType || "ImageFile" == $nameNodeType)}filepreview{else}prevdoc{/if}&nodeid={$id_node}{* &version={$version}&subversion={$subversion}*}{if ("BinaryFile" == $nameNodeType || "ImageFile" == $nameNodetype || "TextFile" == $nameNodeType)}&channel=1{/if}" class="prevdoc-button ui-state-default ui-corner-all button submit-button"><span>Previo</span></a>
						</td>
					<input type="hidden" name="node_id" class="node_id" value="{$id_node}">
					{/if}

					<td align="left">
					{*  If it is a document, it shows combo with channels *}
					{if ($nameNodeType != 'TextFile' && $nameNodeType != 'ImageFile' && $nameNodeType !='BinaryFile'   && $nameNodeType != 'NodeHt')}
						<select id="channellist" name="channellist" class="normal" style="width: 75px;	vertical-align: middle;">
						 {foreach from=$channels item=_channel}
							<option value='{$_channel.Id}'>{$_channel.Name}</option>
						{/foreach}
						</select>
					{/if}
					</td>
				</tr>
			</table>
</fieldset>

