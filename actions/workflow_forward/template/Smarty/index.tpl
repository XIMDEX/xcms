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

<form method="post" name="workflow_forward" action="{$action_url}">

	<input type="hidden" name="nodeid" value="{$id_node}" />
	<input type="hidden" name="default_message" value="{$defaultMessage}">
	<input type="hidden" name="groupid" value="" />
	<input type="hidden" name="state" value="{$state}" />
	<input type="hidden" name="stateid" value="{$stateid}" />

	<fieldset class="publish_date">
		<legend><span>{t}Publication period{/t}</span></legend>
			<ol>
				<li>
					{if $gap_info|@count gt 0}
						<select id="gap_info" class="cajaxg gap_info">
							<option value="">{t}Select one of the currently available windows...{/t}</option>
							{foreach from=$gap_info key=index item=gap}
								<option value="{$gap.BEGIN_DATE}-{$gap.END_DATE}">
									{$gap.BEGIN_DATE} - {if ($gap.END_DATE) neq ''}{$gap.END_DATE}{else}{t}Undetermined{/t}{/if}
								</option>
							{/foreach}
						</select>
					{else}
						{t}At this moment, there is not any available window.{/t}
					{/if}
				</li>
				<li>
				<div class="xim-calendar-container">
				<calendar
					timestamp="{$timestamp_from}"
					date_field_name="date"
					hour_field_name="hour"
					min_field_name="min"
					sec_field_name="sec"
					format="d-m-Y H:i:s"
					type="from"
					cname="fechainicio"
				/>


				<calendar
					date_field_name="date"
					hour_field_name="hour"
					min_field_name="min"
					sec_field_name="sec"
					format="d-m-Y H:i:s"
					type="to"
					cname="fechafin"
				/>
				</div>
				</li>
				{if $has_unlimited_life_time}
				<li>
					<p>{t}Warning: The last publication window has not defined any end-date.{/t}</p>
					<input type="checkbox" name="markend" id="markend" /><label for="markend">{t}Do you want to set the begin-date of the new window as the end-date of the previous window?{/t}</label>
				</li>
				{/if}
				<li>
				{if $show_rep_option}
					{if $synchronizer_to_use == "default"}
					<input type="checkbox" name="republicar" id="republicar" /> <label for="republicar">{t}¿Desea republicar los documentos enlazados a este?{/t}</label>
					{elseif $synchronizer_to_use == "ximSYNC"}
	
						{if $nodetypename eq 'XmlDocument' && $ximpublish_tools_enabled}	
	
							{if $advanced_publication eq '1'}
							<hr/>
								<input onclick="show_div_levels();" type="checkbox" name="all_levels" id="all_levels" value="1" checked />
								<label>{t}Publish all the levels{/t}</label><br/>
	
								<div id="div_deeplevel">
									<label>{t}Publish until a certain depth level:{/t}</label>
									<input id="deeplevel" size="5" type="text" name="deeplevel" value="0" />
								</div>
							{/if}
	
							{if $structural_publication eq '1'}
							<hr/>
								<input type="checkbox" name="no_structure" id="no_structure" />
								<label>{t}Publish just the document (without structure: css, images, scripts){/t}</label><br/>
							{/if}
						{/if}
	
					{/if}
				{/if}
				</li>
				<li>{t}Next state{/t}: <strong>{$state}</strong></li> 
			</ol>

	</fieldset>
	
	<fieldset class="notifications">
		<legend><span>{t}Notifications{/t}</span></legend>
		<ol>
			<li>
				<label for="sendNotifications" class="aligned">{t}Send notifications{/t}</label>
				<input type="checkbox" name="sendNotifications" id="sendNotifications" class="send-notifications" value="1" {if $required == 1}checked="checked"{/if} />
			</li>
			<li class="conditioned {if $required != 1}hidden{/if}">
				<label for="groups" class="aligned">{t}Group{/t}</label>
				<select id="groups" name="groups" class="cajaxg group_info">
					{counter assign=index start=1}
					{foreach from=$group_state_info key=index item=group}
						<option value="{$group.IdGroup}|{$group.IdState}"
							{if $index == 0}selected="selected"{/if}>
							{$group.groupName} ({$group.stateName})
						</option>
					{counter assign=index}
					{/foreach}
				</select>
			</li>
			<li class="conditioned {if $required != 1}hidden{/if}">
				<label class="aligned">{t}Users{/t}</label>
				<div class="user-list-container">
				<ol class="user-list">
					{counter assign=index start=1}
					{foreach from=$notificableUsers item=notificable_user_info}
						<li class="user-info">
							<input type="checkbox" name="users[]" class="validable notificable check_group__notificable" id="user_{$notificable_user_info.idUser}" value="{$notificable_user_info.idUser}" {if $index == 1}checked="checked"{/if} />
							<label for="user_{$notificable_user_info.idUser}">{$notificable_user_info.userName}</label>
						</li>
					{counter assign=index}
					{/foreach}
				</ol>
				</div>
			</li>
			<li class="conditioned {if $required != 1}hidden{/if}">
				<label for="texttosend" class="aligned">{t}Comments{/t}:</label>
				<textarea class="validable not_empty comments" name="texttosend" id="texttosend" rows="4" cols="65" wrap="soft" tabindex="7">{$defaultMessage}</textarea>
			</li>
		</ol>
	
	</fieldset>

	<fieldset class="buttons-form">
		{button class="close-button" label="Cancel"}
		{button class="validate publicate-button" label="Publish"}
	</fieldset>

</form>
