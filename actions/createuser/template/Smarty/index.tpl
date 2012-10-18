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

<form method="post" name="cu_form" id="cu_form" action="{$action_url}">
	<input type="hidden" name="id_node" VALUE="{$id_node}">
	<fieldset>
		<legend><span>{t}Add user{/t}</span></legend>
		<ol>
			<li>
				<label for="login" class="aligned">{t}User{/t}</label>
				<input type="text" name="login" id="login" class="cajag validable not_empty">
			</li>
			<li>
				<label for="name" class="aligned">{t}Name{/t}</label>
				<input type="text" name="name" id="username" class="cajag validable not_empty">
			</li>
			<li>
				<label for="pass" class="aligned">{t}Password{/t}</label>
				<input type="password" name="pass" id="pass" class="cajag validable not_empty field_equals__confirmpass">
			</li>
			<li>
				<label for="confirmpass" class="aligned">{t}Repeat password{/t}</label>
				<input type="password" name="confirmpass" id="confirmpass" class="cajag validable not_empty">
			</li>
			<li>
				<label for="email" class="aligned">{t}E-mail{/t}</label>
				<input type="text" name="email" id="email" class="cajag validable not_empty is_email">
			</li>
			<li>
				<label for="generalrole" class="aligned">{t}Role in general group{/t}</label>
				<select name="generalrole" id="generalrole" class="classxg">
				{foreach from=$roles item=role}
					<option value="{$role.IdRole}">{$role.Name}</option>
				{/foreach}
				</select>
			</li>
			<li>
				<label for="locale" class="aligned">{t}Language{/t}</label>
				<select name="locale" id="locale" class="classxg">
				{section name=i loop=$locales}
					<option value="{$locales[i].Code}" {if ($locales[i].Code == $smarty.const.DEFAULT_LOCALE)} selected{/if}>{$locales[i].Name|gettext} ({$locales[i].Lang})</option>
				{/section}
				</select>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons-form">
		{button type="reset" label='Reset' class='form_reset'}
		{button label='Create user' class='validate' }
	</fieldset>
	<fieldset>
		<p>*{t}Users registered without their consent may violate the rules of your country{/t}</p>
	</fieldset>
</form>