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


			<form method="post" id="modify_role_form" action="{$action_url}&amp;id_pipeline={$selected_pipeline}">

			<fieldset>
			<legend><span>{t}Role data{/t}</span></legend>
			<ol>
			<li><label class="aligned">{t}Name{/t}</label>{$name}</li>
			<li><label for="description" class="aligned">{t}Descripción{/t}</label>
			<input type="text" name="description" id="description" value="{$description}" class="cajaxg validable not_empty"></li>
			<li><label class="aligned">{t}Workflow{/t}</label>
				<select name="id_workflow" id="id_workflow" disabled>
					{foreach from=$pipelines key=id_pipeline item=name }
					<option value="{$id_pipeline}"{if $id_pipeline == $selected_pipeline} selected="selected"{/if}>{$name}</option>
					{/foreach}
				</select>
			</li>
			</ol>
			</fieldset>
			


						<fieldset>

						<legend><span>{t}Modify role permits{/t}</span></legend>
						
						<fieldset class="buttons-form fixed">
				{* button label="Reset" class="form_reset" *}
				{button label="Modify" class="validate button-modify" }<!--message="Are you sure you want to modify this role?"-->
				{button label="Select all" class="button-select-all"}
				{button label="Select none" class="button-deselect-all"}
			</fieldset> 
<label class="aligned">	{t}Generic permits{/t}</label>
			<div class="right-block">
			<ol>
			{foreach from=$permissions key=index item=permissionData}
							<li>
									<input type="checkbox" name="permissions[{$permissionData.IdPermission}]" id="p_{$permissionData.IdPermission}"{if $permissionData.HasPermission} checked="checked"{/if} >
									<label for="p_{$permissionData.IdPermission}">{$permissionData.Description}</label>


								</li>
							{/foreach}
							</ol>
							</div>

						<table>
							<tr>
								<th colspan="5">Action permits</th>
								</tr>
							<tr>
								<th  align="center">{t}Node type{/t}</td>
								<th align="center">{t}Action{/t}</td>
								{foreach from=$workflow_states item=workflow_state}
								<th align="center">{$workflow_state.Name}</td>
								{/foreach}
								<th align="center">{t}Without state{/t}</td>
							</tr>
							{foreach name="outer_nodetypes" from=$nodetypes key=index item=nodetype}
							{assign var=displayed_nodetype value=1}
							{if (count($nodetype.actions)) > 0}
							{foreach name="medium_actions" from=$nodetype.actions key=action_key item=action}
							<tr>
								{if $displayed_nodetype == 1}
								{assign var=displayed_nodetype value=0}
								<td rowspan="{$smarty.foreach.medium_actions.total}" class="{if $index is not even}evenrow{else}oddrow{/if}">{$nodetype.Description} {* $nodetype.IdNodeType *}</td>
								{/if}
								<td class="{if $index is not even}evenrow{else}oddrow{/if}" align="center">{$action.Name}</td>
								{foreach from=$workflow_states item=workflow_state}
								{literal}<td class="{/literal}{if $index is not even}evenrow{else}oddrow{/if}{literal}" align="center">{/literal}
									{if (array_key_exists('states', $action))}
									{literal}<input type="checkbox" name="action_workflow[{/literal}{$action.IdAction}][{$workflow_state.IdState}]"
									{if $action.states[$workflow_state.IdState]} checked="checked"{/if} {* $action.IdAction *}
									{literal}>{/literal}
									{/if}&nbsp;
								</td>
								{/foreach}
								<td class="{if $index is not even}evenrow{else}oddrow{/if}" align="center">{if (array_key_exists('state', $action))}<input type="checkbox" name="action_workflow[{$action.IdAction}][NO_STATE]"{if $action.state} checked="checked"{/if}>{/if}</td>
							</tr>
							{/foreach}
							{/if}
							{/foreach}
						</table>


			</fieldset>
			<fieldset class="buttons-form">
				{* button label="Reset" class="form_reset" *}
				{button label="Modify" class="validate button-modify" }<!--message="Are you sure you want to modify this role?"-->
				{button label="Select all" class="button-select-all"}
				{button label="Select none" class="button-deselect-all"}
			</fieldset>
			</form>
