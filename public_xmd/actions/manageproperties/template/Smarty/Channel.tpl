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

{if isset($properties.Channel) and count($properties.Channel) > 0}
	{assign var="channels" value=$properties.Channel}
	<div class="row tarjeta propertyform">
		<h2 class="h2_general">{t}Channels{/t}</h2>
		<div class="manageproperties">
			{if ($inProject eq false)}
				<div>
					<input type="radio" name="inherited_channels" class="channels_inherited" value="inherited" id="channels_inherited"
							{if $Channel_inherited == 'inherited'}checked="checked"{/if} />
					<label for="channels_inherited">{t}Use inherited channels{/t}:</label>
					<ul class="inheritlist">
						{foreach from=$channels item=channel}
							{if $channel.Inherited}
								<li>{$channel.Name}</li>
							{/if}
						{/foreach}
					</ul>
				</div>
				<div>
					<input type="radio" name="inherited_channels" class="channels_overwritten" value="overwrite" id="channels_overwritten"
							{if $Channel_inherited == 'overwrite'}checked="checked"{/if} />
					<label for="channels_overwritten">{t}Overwrite inherited channels{/t}:</label>
				</div>
			{else}
				<input type="hidden" name="inherited_channels" value="overwrite" />
				<label for="channels_inherited">{t}Available project channels{/t}:</label>
			{/if}
				{if ($channels)}
				<div class="overwrited_properties">
					{foreach from=$channels item=channel}
						<span name="check_channels" class="slide-element channelsmp{if $Channel_inherited == 'inherited'} disabled{/if}">
							<input type="checkbox" class="channels input-slide" name="Channel[]" value="{$channel.Id}" 
									{if $channel.Checked == 1}checked="checked"{/if} id="{$channel.Name}_{$id_node}" />
							<label for="{$channel.Name}_{$id_node}" class="label-slide">{$channel.Name}</label>
						</span>
					{/foreach}
				</div>
			{/if}
			<hr />
			<div>
				<input type="checkbox" class="channels_recursive" name="Channel_recursive" id="Channel_recursive" value="1" />
				<label for="Channel_recursive">{t}Associate channels recursively{/t}</label>
			</div>
		</div>
	</div>
{/if}