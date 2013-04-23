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


			{if !empty($users)}
				<form method="post" id="muga_form" action="{$action_add}">
					<input type="hidden" name="nodeid" value="{$nodeid}" />
					<fieldset>
						<legend><span>{t}Users who belong to the group{/t}:" <i>{$name}</i> "</span></legend>
        	                    		<ol>
	                        			<li>
                                				<strong>{t}User{/t}</strong>
								<select name='id_user' id='id_user' class='cajag validable not_empty'>
									<option value="">{t}Select user{/t}</option>
									{foreach from=$users key=id_user item=user}
										<option value="{$id_user}">{$user}</option>
									{/foreach}
								</select> {t}with{/t}
								<strong>{t}Role{/t}</strong>
								<select name='id_role' id='id_role' class='cajag validable not_empty'>
									<option value="">{t}Select a role{/t}</option>
									{foreach from=$roles item=role}
										<option value="{$role.IdRole}">{$role.Name}</option>
									{/foreach}
								</select>
                			                </li>
                	               		</ol>
					</fieldset>
	                               	<fieldset class="buttons-form">
						{button id="add_user" label="Add new" class="validate" }<!--message="Would you like to add user with role to the group?"-->
					</fieldset>
				</form>
			{/if}
			{if count($user_infos) > 0}
				<form method="post" id="muged_form" action="{$action_edit_delete}">
					<fieldset>
				                 <legend><span>{t}Change roles{/t}</span></legend>
						<ol>
							{foreach from=$user_infos key=id_rel item=user_info}
			        				<li>
										{if ($idnode != "101")} {* if not group general *}
											<input name="users[]" type="checkbox" value="{$user_info.IdUser}">
										{/if}
									{$user_info.UserName}
			        			 		{t}with role{/t}
					        			<input type="hidden" name="user_for_role[]" value="{$user_info.IdUser}">
									<select name='id_user_role[]' class='cajag validable not_empty'>
										<option value="">{t}Select a role{/t}</option>
										{foreach from=$roles item=role}
											<option value="{$role.IdRole}"{if ($user_info.IdRole == $role.IdRole)} selected="selected"{/if}>{$role.Name}</option>
										{/foreach}
									</select>
			        				</li>
							{/foreach}
			                       </ol>
                       			</fieldset>
					<fieldset class="buttons-form">
						{button label="Reset" type="reset"}
						{button id="guardar" onclick="update_form_action('editgroupuser');"  label="Save associations" class="validate" message="Subscriptions of group are going to be updated, would you like to continue?"}
						{if ($idnode != "101")} {* if not group general *}
						{button id="eliminar" 	onclick="update_form_action('deletegroupuser');"  label="Delete selected associations" class='validate' message="Selected associations will be deleted. Would you like to continue?"}
						{/if}
						</fieldset>
					</form>
			{/if}


