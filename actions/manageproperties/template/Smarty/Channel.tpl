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

{assign var="channels" value=$properties.Channel}
<fieldset>
	<div class="manageproperties">
		<h3 class="icon icon-channel">{t}Channels{/t}</h3>
		<div class="row-item">

		<div class="hidden">
			<input type="radio" name="inherited_channels" class="channels_overwritten" value="overwrite" checked />
			<label>{t}Overwrite inherited channels{/t}</label>
		</div>
	{if ($channels)}
		{foreach from=$channels item=channel}
		<span class="slide-element">
			<input type="checkbox" class="channels input-slide" name="Channel[]" value="{$channel.IdChannel}" 
			{if $channel.Checked == 1}
				checked="checked"
			{/if}
			 id="{$channel.Name}_{$id_node}"/>
			<label for="{$channel.Name}_{$id_node}" class="label-slide">{$channel.Name}</label>
		</span>
		{/foreach}
	{/if}
	</div>
</div>

</fieldset>
