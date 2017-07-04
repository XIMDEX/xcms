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

<form method="post" id="groups_form" action="{$action_url}">
    <input type="hidden" name="id_node" value="{$id_node}">

    <div class="action_header">
        <h5 class="direction_header"> Name Node: {$name}</h5>
        <h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
        <hr>
    </div>

    <div class="action_content">
        <div class="row tarjeta">
            <div class="small-12 columns title_tarjeta">
                <h2 class="h2_general">{t}Add group to section{/t}</h2>
            </div>

            {if (!$new_groups)}
                <div class="small-12 columns">
                    <div class="alert alert-info">
                        <strong>Info!</strong> {t}No groups to add have been found{/t}.
                    </div></div>

            {else}
            <ol>
                <li>
                    <div class="small-12 columns">
                    <label for="id_group" class="label_title label_general">{t}Available groups{/t}</label>
                        <div class="input-select">
                    <select name='id_group' id="id_group" class='cajag' onchange='comprobar_vacio();'>
                        <option value="">{t}Select group{/t}</option>
                        {foreach from=$new_groups key=id_group item=group}
                        <option value="{$id_group}">{$group}</option>
                        {/foreach}
                    </select>
                        </div></div>
                </li>
                <li>
                    <div class="small-12 columns">
                        <input name='is_recursive' type='checkbox' checked value='newrec' id="is_recursive" class="hidden-focus">
                    <label for="is_recursive" class="input-form checkbox-label">{t}Recursive{/t}</label>

                    </div>
                </li>
                {*
                <li>
                    <label for="id_role" class="aligned">{t}Force role{/t}</label>
                    <select name='id_role' class='caja' id="id_role">
                        <option value=''>{t}None{/t}</option>
                        {foreach from=$roles item=rol_info}
                        <option value="{$rol_info.IdRole}">{$rol_info.Name}</option>
                        {/foreach}
                    </select>
                </li>
                *}
            </ol>
            {/if}

        {if ($new_groups)}
            <div class="small-12 columns">
        <fieldset class="buttons-form">
            {button label="Add group" onclick="call_submit('addgroupnode');" class="validate btn main_action" }{*message="Are you sure you want to perform this association?"*}
        </fieldset>
            </div>
        {/if}
        </div>

        <div class="row tarjeta">
            <div class="small-12 columns title_tarjeta">
                <h2 class="h2_general">{t}Delete group of a section{/t}</h2>
            </div>

            {if (!$all_groups)}
                <div class="small-12 columns">
                    <div class="alert alert-info">
                        <strong>Info!</strong> {t}No associated groups have been found{/t}.
                    </div></div>
            {else}
            <table class="table table-striped">
                <tr>
                    <th>{t}Delete{/t}</th>
                    <th>{t}Group{/t}</th>
                    {*
                    <th>{t}Force role{/t}</th>*}
                    <th>{t}Recursive{/t}</th>
                </tr>
                {foreach from=$all_groups item=groupInfo}
                <tr>
                    <td class='filaclara'>
                        <input name='id_group_checked[]' type='checkbox' value='{$groupInfo.IdGroup}'>
                    </td>
                    <td class='filaclara'>
                        {$groupInfo.Name}
                    </td>
                    <td class='filaclara'>
                        <input type='hidden' name='idGroups[]' value='{$groupInfo.IdGroup}'>
                        <input type='hidden' name='idRoleOld[]' value='{$groupInfo.IdRoleOnNode}'> {*
                        <select name='idRole[]' class='caja'>
                            <option value=''>{t}None{/t}</option>
                            {foreach from=$roles item=rol_info}
                            <option value="{$rol_info.IdRole}" {if $groupInfo.IdRoleOnNode==$ rol_info.IdRole} selected="selected" {/if}>{$rol_info.Name}</option>
                            {/foreach}
                        </select>
                        *}
                        <input name='recursive[]' type='checkbox' checked value='{$groupInfo.IdGroup}' id="recursive">
                    </td>

                </tr>

                {/foreach}
            </table>
            {/if}



        {if ($all_groups)}
            <div class="small-12 columns">
        <fieldset class="buttons-form">
            {button label="Delete subscriptions" class="validate btn main_action" onclick="call_submit('deletegroupnode')" message="Are you sure you want to delete selected subscriptions?"} {*{button class="validate btn main_action" label="Save changes" onclick="call_submit('updategroupnode');"
            message="Are you sure you want to update selected subscriptions?" }*}
        </fieldset></div>
        {/if}</div></div>
</form>
