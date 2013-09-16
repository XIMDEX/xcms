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
		{button label="Modify user" class='validate btn main_action' }
	</fieldset>
	</div>
	<div class="action_content">
		<fieldset>
			<ol>
				<li>
					{t}Usuario{/t}
					{$login}
				</li>
				<li>
					<label for="name" class="aligned">{t}First and last names{/t}</label>
					<input type="text" name="name" id="name" value="{$name}" class='cajaxg validable not_empty'>
				</li>
				<li>
					<label for="email" class="aligned">{t}Email{/t}</label>
					<input type="text" name="email" id="email" value="{$email}" class='cajaxg validable not_empty is_email'>
				</li>
				<li>
					<label for="password_" class="aligned">{t}Modify password{/t}</label>
					<input type="password" name='password_' id="password_" class='caja validable field_equals__password_repeated'>
				</li>
				<li>
					<label for="password_repeated" class="aligned">{t}Repeat password{/t}</label>
					<input type="password" name='password_repeated' id="password_repeated" class='caja'>
				</li>
				<li>
					<label for="locale" class="aligned">{t}Language{/t}</label>
					<select name="locale" id="locale" class="classxg">
					{section name=i loop=$locales}
						<option value="{$locales[i].Code}" {if ($locales[i].Code == $user_locale || ( $user_locale == null && $locales[i].Code == $smarty.const.DEFAULT_LOCALE))} selected{/if}>{$locales[i].Name|gettext} ({$locales[i].Lang})</option>
					{/section}
					</select>
				</li>
			</ol>
		</fieldset>
	</div>

</form>
