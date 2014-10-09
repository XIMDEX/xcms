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

<form method="post" action="{$action_url}" class="form_group_user">
    <div class="action_header">
        <h2>{t}Manage groups{/t}</h2>
        <fieldset class="buttons-form">

        </fieldset>
    </div>

    <div class="action_content">
        <h3>{t}Available groups{/t}</h3>
        <input type="hidden" name="nodeid" value="{$id_node}"/>
        <div class="associate-group">
            {if ($filtered_groups)}
                <div class="row-item col2-3">
			 		<span class="col1-2 icon icon-group label-select">
			 			<select name='newgroup' class='select-clean block'>
                            {foreach from=$filtered_groups item=group_info}
                                <option value="{$group_info.IdGroup}">
                                    {$group_info.Name}
                                </option>
                            {/foreach}
                        </select>
			 		</span>

					<span class="col1-2 icon icon-rol label-select">
						<select name='newrole' class='select-clean block'>
                            {foreach from=$all_roles item=rol_info}
                                <option value="{$rol_info.IdRole}">
                                    {$rol_info.Name|gettext}
                                </option>
                            {/foreach}
                        </select>
					</span>
                    <div class="buttons-form row-item-actions actions-outside col1-3">
                        {button label="Add group" title="Add group" class="validate icon add-btn  btn-unlabel-rounded btn addgroupuser"}{*message="You are adding a new subscription. Would you like to continue?"*}
                    </div>
                </div>


            {else}
                <p>{t}There are not{/t} {if ($user_groups_with_role)}{t}more{/t}{/if} {t}available groups to be associated with the user{/t}</p>
            {/if}
        </div>
        <h3>{$user_name} {t}belongs to the next groups{/t}:</h3>
        <input name="group" type="hidden" value="" />
        <input name="role" type="hidden" value="" />
        <input name="roleOld" type="hidden" value="" />
        {if ($user_groups_with_role )}
        <div class="change-group">

            {foreach from=$user_groups_with_role item=user_group_info}
                <div class="row-item icon">
                    <input name="info-IdGroup" type="hidden" value="{$user_group_info.IdGroup}"/>
                    <input name="info-IdRoleOld" type="hidden" value="{$user_group_info.IdRole}"/>
                    <span class="col1-3">
						{$user_group_info.Name}
					</span>
                    <span class="col1-3">
					<select name='idRole' class='select-clean block'>
                        {foreach from=$all_roles item=rol_info}
                            <option value="{$rol_info.IdRole}"{if $rol_info.IdRole == $user_group_info.IdRole} selected="selected"{/if}>
                                {$rol_info.Name|gettext}
                            </option>
                        {/foreach}
                    </select>
				</span>


                    <div class="buttons-form row-item-actions col1-3">
                        <span>
                            {button label="Update"
                            class="updategroupuser hidden validate recover-btn disabled-version icon btn-unlabel-rounded"
                            }
                        </span>
                        {if ($user_group_info.IdGroup != "101")} {* if not group general *}
                        <span>
                            {button
                            label="Delete associations"
                            class="deletegroupuser validate btn icon btn-unlabel-rounded delete-btn"
                            message="This subscription will be deleted. Are you sure you want to continue?"}
                        </span>
                        {/if}
                    </div>



                </div>

            {/foreach}
        </div>
    </div>

    {else}
    <p>{t}There are no groups associated with user yet{/t}</p>
    {/if}</div>
</form>
