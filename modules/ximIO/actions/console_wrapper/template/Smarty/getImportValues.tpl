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

<tr>
	<td>
		{include file="$_APP_ROOT/xmd/template/Smarty/helper/messages.tpl"}
		<form action="{$action}" method="post" id="console_wrapper">
			<input type="hidden" name="nodeid" value=""/>
			<input type="hidden" name="actionid" value="{$id_action}"/>
			<input type="hidden" name="file" value="{$backup}" />
			
			<div class="info_container"> 
				<label for="user">{t}User to export:{/t}</label>
				<input type="user" id="user" name="user" />
				<label for="password">{t}Password:{/t}</label>
				<input type="password" id="password" name="password" />
			</div>
			
			<div class="info_container"> 
				<label for="nodes">{t}Insert node ID on which you want to import:{/t}</label>
				<input type="text" id="node" name="node" />
			</div>
			
			<div class="info_container"> 
				<input type="checkbox" id="processFirstNode" name="processFirstNode" />
				<label for="processFirstNode">{t}Indicate if first node should be generated or not.{/t}</label>
				<p>
				<strong>{t}Explanation:{/t}</strong>{t}If we copy a ximdoc folder, destination node should be a ximdoc folder, and in this case we should not indicate than first node was generated. If we copy a ximnews folder, we should choose as container a server or section, and we will need create the ximnews section, therefore in this case we should tick the checkbox{/t}.
				</p>
			</div>
			
			<div class="info_container"> 
				<input type="checkbox" id="copyMode" name="copyMode" />
				<label for="copyMode">{t}Tick if source and destination ximdex are same{/t}</label>
			</div>
			
			<div class="info_container"> 
				<label>{t}Association of source channels with destination channels{/t}:</label>
				{foreach from=$imported_channels item=channel name=imported}
				<div>
					<label>{t}Source identifier{/t}:</label>{$channel.id}
					<label>{t}Source name{/t}:</label>{$channel.name}
					<label>{t}Associated with{/t}:</label>
					<select name="channel[{$channel.id}]">
						<option value="">Not associated</option>
					{foreach from=$channels item=actual_channel name=all_channels}
						<option value="{$actual_channel.IdChannel}">{$actual_channel.Name}</option>
					{/foreach}
					</select>
				</div>
				{/foreach}
			</div>

			<div class="info_container"> 
				<label>{t}Association of source languages with destination languages{/t}:</label>
				{foreach from=$imported_languages item=language name=imported}
				<div>
					<label>{t}Source identifier{/t}:</label>{$language.id}
					<label>{t}Source name{/t}:</label>{$language.name}
					<label>{t}Associated with{/t}:</label>
					<select name="language[{$language.id}]">
						<option value="">Not associated</option>
					{foreach from=$languages item=actual_language name=all_languages}
						<option value="{$actual_language.IdLanguage}">{$actual_language.Name}</option>
					{/foreach}
					</select>
				</div>
				{/foreach}
			</div>
			
			<div class="info_container"> 
				<label>{t}Association of source groups with destination groups{/t}:</label>
				{foreach from=$imported_groups item=group name=imported}
				<div>
					<label>{t}}Source identifier{/t}:</label>{$group.id}
					<label>{t}Source name{/t}:</label>{$group.name}
					<label>{t}Associated with{/t}:</label>
					<select name="group[{$group.id}]">
						<option value="">Not associated</option>
					{foreach from=$groups item=actual_group name=all_groups}
						<option value="{$actual_group.IdGroup}">{$actual_group.Name}</option>
					{/foreach}
					</select>
				</div>
				{/foreach}
			</div>
			
			{button label="Next" class='validate'}
		</form>
	</td>
</tr>

