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

{if ($all_roles)}
<form method="post" name="add_form" id="cu_form" action="{$action_add}">
	<input type="hidden" name="id_node" VALUE="{$id_node}">
	<fieldset>
		<legend><span>{t}Associate roles{/t}</span></legend>
		<ol>
			<li>
				<label for="id_role" class="aligned">{t}Existing roles{/t}</label>
				<select name="id_role" id="id_role" class="cajag">
					{foreach from=$all_roles item=role_info}
				     	<option value="{$role_info.IdRole}">{$role_info.Name}</option>
				     {/foreach}
				</select>
			</li>
		 </ol>
	</fieldset>
	<fieldset class="buttons-form">{button label="Associate role" title="Associate role" class="validate"}</fieldset>
</form>
{/if}

{if ($applied_roles)}
<form method="post" name="delete_form" id="cu_form" action="{$action_delete}">
	<input type="hidden" name="id_node" VALUE="{$id_node}">
	<fieldset>
		<legend><span>{t}Dissociate roles{/t}</span></legend>
		<ol>
			<li>
				<label for="delete_role" class="aligned">{t}Existing roles{/t}</label>
				<div class="right-block">
				<ol>
				{foreach from=$applied_roles item=role_info}
						<li><input name='roles_to_delete[{$role_info.IdRole}]' type='checkbox'>
			  			{$role_info.Name}</li>
				{/foreach}
				</ol>
			</li>
		 </ol>
	</fieldset>
<fieldset class="buttons-form">{button label="Dissociate role" title="Dissociate role" class="validate"}</fieldset>
</form>
{/if}
