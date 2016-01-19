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

<form method="post" id="modify_role_form" class="modify_role_form" action="{$action_url}&amp;id_pipeline={$selected_pipeline}">
    <div class="action_header">
	   <h2>{t}Role data{/t}</h2>

        <fieldset class="buttons-form">
            {* button label="Reset" class="form_reset btn" *}

            {button label="Modify" onclick="window.com.ximdex.emptyActionsCache();" class="validate button-modify btn main_action"}

            {*message="Are you sure you want to modify this role?"*}

            {button label="Select all" class="button-select-all btn"}
            {button label="Select none" class="button-deselect-all btn"}
		</fieldset>
	</div>

	<div ng-cloak class="action_content">
        <fieldset>
            <accordion close-others="true" ng-init="firstOpen=true; firstDisabled=false;">
                <!-- datos generales -->
                <accordion-group heading="Datos generales" is-open="firstOpen" is-disabled="firstDisabled">
                    <div class="form-group">
                        <label for="name">{t}Name{/t}</label>

                        <input type="text" class="form-control" name="name" id="name" value="{$name}" placeholder="{$name}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="description">{t}Description{/t}</label>

                        <input type="text" class="form-control" name="description" id="description" value="{$description}" placeholder="{$description}">
                    </div>

                    <div class="form-group">
                        <label for="id_workflow">{t}Workflow{/t}</label>
                            <select name="id_workflow" id="id_workflow" class="form-control" disabled>
                            {foreach from=$pipelines key=id_pipeline item=name}
                                <option value="{$id_pipeline}" {if $id_pipeline == $selected_pipeline} selected="selected"{/if}>
                                    {$name}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                </accordion-group>
                <!-- / datos generales -->

                <!-- permisos genéricos -->
                <accordion-group heading="{t}Generic permits{/t}">
                    {foreach from=$permissions key=index item=permissionData}
                        <div class="checkbox">
                            <label for="p_{$permissionData.IdPermission}">
                                <input type="checkbox" name="permissions[{$permissionData.IdPermission}]" id="p_{$permissionData.IdPermission}"{if $permissionData.HasPermission} checked="checked"{/if}>&nbsp;{$permissionData.Description}
                            </label>
                        </div>
                    {/foreach}
                </accordion-group>
                <!-- / permisos genéricos -->

                <!-- others -->
                {foreach name="outer_nodetypes" from=$nodetypes key=index item=nodetype}
                    {assign var=displayed_nodetype value=1}

                    {if (isset($nodetype.actions) && (count($nodetype.actions)) > 0)}
                        <accordion-group heading="{$nodetype.Description}" >
                            <table class="table">
                                <tr>
                                    <th></th>

                                    {foreach from=$workflow_states item=workflow_state}
                                        <th>{$workflow_state.Name}</th>
                                    {/foreach}

                                    <th>{t}Without state{/t}</th>
                                </tr>

                                {foreach name="medium_actions" from=$nodetype.actions key=action_key item=action}
                                    <tr>
                                        {if $displayed_nodetype == 1}
                                            {assign var=displayed_nodetype value=0}
                                        {/if}

                                        <td class="{if $index is not even}evenrow{else}oddrow{/if}">
                                            {$action.Name}
                                        </td>

                                        {foreach from=$workflow_states item=workflow_state}
                                            <td class="{if $index is not even}evenrow{else}oddrow{/if}">
                                                {if (array_key_exists('states', $action))}
                                                    <input type="checkbox" name="action_workflow[{$action.IdAction}][{$workflow_state.IdState}]" {if $action.states[$workflow_state.IdState]} checked="checked"{/if} {* $action.IdAction *}/>
                                                {/if}
                                            </td>
                                        {/foreach}

                                        <td class="{if $index is not even}evenrow{else}oddrow{/if}" align="center">
                                            {if (array_key_exists('state', $action))}
                                                <input type="checkbox" name="action_workflow[{$action.IdAction}][NO_STATE]"{if $action.state} checked="checked"{/if}>
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                            </table>
                        </accordion-group>
                    {/if}
                {/foreach}
                <!-- / others -->
            </accordion>
        </fieldset>
	</div>
</form>