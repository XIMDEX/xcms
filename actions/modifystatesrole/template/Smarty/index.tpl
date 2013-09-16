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


<form method="post" id="msr_action" action="{$action_add}">
	<div class="action_header">
		<h2>{t}Add status to role{/t}</h2>
					<fieldset class="buttons-form">
	{button label="Add status" class="validate btn main_action" }
			</fieldset>
	</div>
		<div class="action_content">
			<fieldset>
			<ol>
			<li>
				<label for="id_state" class="aligned">{t}Status{/t}</label>
				<select name="id_state" id="id_state" class="validable not_empty">
			<option value="">{t}Select a status{/t}</option>
				{foreach from=$all_states key=id_state item=state_name}
			<option value="{$id_state}">{$state_name|gettext}</option>
				{/foreach}
				</select>
				</li></ol>
				</fieldset>
		</div>

</form>
<form method="post" id="msrd_action" action="{$action_delete}">
	<div class="action_header">
<h2>{t}Delete-Status{/t}</h2>
			<fieldset class="buttons-form">
			{button label="Delete status" class="validate btn main_action" message="Would you like to delete this status of the role?"}

		</fieldset>
	</div>
<div class="action_content"><fieldset>
	<ol>
		{foreach from=$role_states key=id_state item=state_name}
		<li>
		<input type="checkbox" name="states[]" value="{$id_state}">

		{$state_name|gettext}
	</li>
		{/foreach}
		</ol>
		</fieldset>
		</div>

</form>