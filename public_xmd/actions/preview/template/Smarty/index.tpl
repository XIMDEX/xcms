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
<div class="action_header">
	<h2>{t}Document preview{/t} {$name}</h2>
	<fieldset class="action-controls">
		<input type="hidden" name="node_id" class="node_id" value="{$id_node}" />
		{*  If it is a document, it shows combo with channels *}
		{if ($nameNodeType != 'TextFile' and $nameNodeType != 'ImageFile' and $nameNodeType !='BinaryFile' and $nameNodeType != 'NodeHt')}
			<select id="channellist{$id_node}" name="channellist" class="prevdoc-channel-selector" style="width: auto;">
				{foreach from=$channels item=_channel}
					<option value='{$_channel.Id}'>{$_channel.Name}</option>
				{/foreach}
			</select>
		{/if}
		<a id="prevdoc-button" href="{url}/?action=rendernode&nodeid={$id_node}&token={$token}{/url}"
				class="btn main_action ui-state-default ui-corner-all button submit-button ladda-button"><span 
				class="ladda-label">{t}View in a new window{/t}</span></a>
	</fieldset>
</div>
<div class="content_container prevdoc">
	<iframe id="preview{$id_node}" src="{url}/?action=rendernode&nodeid={$id_node}&channel=10001&token={$token}{/url}" height="100%" width="100%">
		<p>Your browser does not support iframes.</p>
	</iframe>
</div>