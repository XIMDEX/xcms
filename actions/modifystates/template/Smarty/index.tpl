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

<form method="post" id="msc_form" action="{$action_create}">
	<fieldset>
		<legend><span>{t}Add status{/t}</span></legend>
			<ol>
				<li>
					<label for="name" class="aligned">{t}Name{/t}</label>
					<input type="textbox" name="name" id="name" class="caja validable not_empty">
				</li>
				<li>
					<label for="description" class="aligned">{t}Description{/t}</label>
					<input type="textbox" name="description" id="description" class="cajag">
				</li>
				<li>
					<label for="transition" class="aligned">{t}Insert between{/t}</label>
					<input type="hidden" name="transition" value="no_transitions" />
					{if empty($status_transitions)}
					{t}There are not transitions{/t}
					{else}
						<select name="transition" id="transition" class="cajag validable not_empty">
							<option value="">{t}Select a transition{/t}</option>
							{foreach from=$status_transitions key=id_status item=status_transition}
							<option value="{$id_status}">{$status_transition}</option>
							{/foreach}
						</select>
					{/if}
				</li>
			</ol>
	</fieldset>
	<fieldset class="buttons-form">
	{button class="validate" label="Add status" }<!--message="Would you like to add a new state to workflow?"-->
	</fieldset>
</form>
<form method="post" id="msu_form" action="{$action_update}">
	<fieldset>
		<legend><span>{t}Workflow applications{/t}</span></legend>
		<ol>
			<li>
				<label for="id_nodetype" class="aligned">{t}Node type (optional){/t}</label>
				<select name="id_nodetype" id="id_nodetype" class="cajag" style="text-color: #555555;"{if $is_workflow_master == true} disabled="disabled"{/if}>
					<option value="">{t}Select a node type{/t}</option>
					{foreach from=$nodetype_list key=id item=nodetype}
					<option value="{$id}"{if $id == $id_nodetype} selected="selected"{/if}>{$nodetype}</option>
					{/foreach}
				</select>
			</li>
			<li>
				<input type="checkbox" name="is_workflow_master" id="is_workflow_master"{if $is_workflow_master == true} checked="checked" disabled="disabled"{/if} />
				<!--<label for="is_workflow_master">-->{t}This workflow will behave as default item{/t}
			</li>
			<li>
				<strong>{t}Existing status{/t}</strong>
			</li>
			<li>

				<ul class="sortable">
				{counter start=0 print=false}
				{foreach from=$all_status_info key=id_status item=status_info name=status}
					<li {if $smarty.foreach.status.first}class="nosortable first"{/if}{if $smarty.foreach.status.last}class="nosortable last"{/if}>


						<label for="id_{$id_status}" class="aligned">{t}Status{/t} {counter}</label>

						<input type="text" name="name[{$id_status}]" id="id_{$id_status}" class="caja validable not_empty" value="{$status_info.NAME}">

						<input type="text" class="cajaxg" name="description[{$id_status}]" value="{$status_info.DESCRIPTION}">
						<input type="hidden" name="id_status" value="{$id_status}">
						<img src="{$_URL_ROOT}/xmd/images/show.png" class="modifyrolesstate" />
						{if !$smarty.foreach.status.first and !$smarty.foreach.status.last}
								<img alt="<t>Flecha</t>" title="Cambiar orden" src="{$_URL_ROOT}/xmd/images/action/move.png" class="sortable_element"/>
						{/if}
					</li>
				{/foreach}
				</ul>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons-form">
		<input type="hidden" name="idNode" value="{$idNode}">
		<input type="hidden" name="url_to_nodelist" value="{$url_to_nodelist}">
		{button type="reset" label="Reset" class="form_reset"}
		{button label="Save changes" class="validate" }<!--message="Would you like to update workflow status?"-->
		{button label="Check dependencies" class="open_report" }
	</fieldset>
</form>

