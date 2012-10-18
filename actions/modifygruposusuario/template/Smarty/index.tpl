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
	<fieldset>
		<input type="hidden" name="nodeid" value="{$id_node}">
		<legend>
			<span>{t}Add group{/t}</span><!--{t}Groups which it belongs{/t}[&nbsp;<u>{t}{$user_name}{/t}</u>&nbsp;]-->
		</legend>
			{if ($filtered_groups)}
	    	<ol>
			<!--<li>
				<b>{t}Role of general group{/t}:</b>
				<input type='hidden' name='oldglobalRole' value='{$general_role}'>
				<select name='globalRole' class='cajag'>
				{foreach from=$all_roles item=rol_info}
					<option value="{$rol_info.IdRole}"{if $rol_info.IdRole == $general_role} selected="selected"{/if}>
				 	{$rol_info.Name|gettext}
				 	</option>
					 {/foreach}
				</select>
			</li>-->
			<li><label class="aligned">{t}Add group{/t} </label>
		 		<select name='newgroup' class='cajag'>
		 		{foreach from=$filtered_groups item=group_info}
					<option value="{$group_info.IdGroup}">
						{$group_info.Name}
					</option>
				{/foreach}
				</select>
				</li>
				<li>
					<label class="aligned">{t}With role{/t}</label>
				<select name='newrole' class='cajag'>
				{foreach from=$all_roles item=rol_info}
					<option value="{$rol_info.IdRole}">
				 	{$rol_info.Name|gettext}
				 	</option>
				{/foreach}
				</select>

			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons-form">{button label="Add group" title="Add group" class="addgroupuser validate"}<!--message="You are adding a new subscription. Would you like to continue?"-->
								</fieldset>
		{else}
				<p>{t}There are not{/t} {if ($user_groups_with_role)}{t}more{/t}{/if} {t}available groups to be associated with the user{/t}</p>
		{/if}
		</fieldset>
	<fieldset>
	<legend><span>{t}Remove from group{/t}</span></legend>
	{if ($user_groups_with_role )}
		<ol>
			<li><label class="aligned">{t}Remove from group{/t}</label>
			<div class="right-block">
			<ol>
		  {foreach from=$user_groups_with_role item=user_group_info}
				<li>
				{if ($user_group_info.IdGroup != "101")} {* if not group general *}
				<input name='checked[]' type='checkbox' value='{$user_group_info.IdGroup}' />
				{else}
				<input name='checked[]' type='checkbox' value='{$user_group_info.IdGroup}' disabled="true" />
				{/if}
		     		{$user_group_info.Name}
		     		<input type='hidden' name='idGroups[]' value='{$user_group_info.IdGroup}'>
				<input type='hidden' name='idRoleOld[]' value='{$user_group_info.IdRole}'>
				<select name='idRole[]' class='cajag'>

				{foreach from=$all_roles item=rol_info}
					<option value="{$rol_info.IdRole}"{if $rol_info.IdRole == $user_group_info.IdRole} selected="selected"{/if}>
					{$rol_info.Name|gettext}
					</option>
				{/foreach}
				</select>
				</li>

        {/foreach}
				</ol>
				</div>
				</li>
			<li>
				<img src="{$_URL_ROOT}/xmd/images/pix_t.gif" alt="" border="0">
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons-form">
	{button
		label="Delete associations"
		class="deletegroupuser validate"
		message="If some subscription is selected it will be deleted. Are you sure you want to continue?"}
	{button
		label="Update associations"
		class="updategroupuser validate"
		message="Subscriptions to groups will be updated. Would you like to continue?"}
	</fieldset>
	{else}
				<p>{t}There are no groups associated with user yet{/t}</p>
	{/if}
</form>
