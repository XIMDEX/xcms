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

<form method="post" id="mu_action" action="{$action_url}">
	<div class="action_header">
		<h2>{t}Modify user{/t}</h2>
			<fieldset class="buttons-form">
		{button label="Modify user" class='validate btn main_action' tabindex="7" }
	</fieldset>
	</div>
	<div class="action_content">
				<h3>
					{t}User{/t}: {$login}
				</h3>
				<p>
					<label for="name" class="label_title">{t}First and last names{/t}</label>
					<input type="text" name="name" id="name" value="{$name}" class='full_size validable not_empty' placeholder="{t}First and last names{/t}"  tabindex="1">
				</p>
				<p>
					<label for="email" class="label_title">{t}Email{/t}</label>
					<input type="text" name="email" id="email" value="{$email}" class='full_size validable not_empty is_email' placeholder="{t}Email{/t}"  tabindex="2">
				</p>
				<p class="col1_2 col_left">
					<label for="password_" class="label_title">{t}Modify password{/t}</label>
					<input type="password" name='password_' id="password_" class='full_size caja validable field_equals__password_repeated' placeholder="{t}Modify password{/t}"  tabindex="3">
				</p>
				<p class="col1_2 col_right">
					<label for="password_repeated" class="label_title">{t}Repeat password{/t}</label>
					<input type="password" name='password_repeated' id="password_repeated" class='full_size' placeholder="{t}Repeat password{/t}"  tabindex="4">
				</p>
				<p class="col1_2 col_left" >
					<label for="generalrole" class="label_title" for="generalrole">{t}Role in general group{/t}</label>
					<select name="generalrole" id="generalrole" class="full_size"  tabindex="5">
					{foreach from=$roles item=role}
						<option value="{$role.IdRole}" {if ($role.IdRole == $general_role)} selected{/if} >{$role.Name}</option>
					{/foreach}
					</select>
				</p>				
				<p class="col1_2 col_right">
					<label for="locale" class="label_title">{t}Language{/t}</label>
					<select name="locale" id="locale" class="full_size"  tabindex="6">
					{section name=i loop=$locales}
						<option value="{$locales[i].Code}" {if ($locales[i].Code == $user_locale || ( $user_locale == null && $locales[i].Code == $smarty.const.DEFAULT_LOCALE))} selected{/if}>{$locales[i].Name|gettext} ({$locales[i].Lang})</option>
					{/section}
					</select>
				</p>
	</div>

</form>
