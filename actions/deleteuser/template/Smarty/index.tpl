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

<!--{include file="$_APP_ROOT/xmd/template/Smarty/helper/messages.tpl"}-->
<form method="post" name="el_form" action="{$action_url}">
	<fieldset>
		<legend><span>{t}Delete user{/t}</span></legend>
		<input type=hidden name='id_node' value="{$id_node}">
		<p>{t}The user <b>{$login}</b> ({$email}) is going to be deleted.{/t}</p>
		<p>{t}This action cannot be undone.{/t}</p>
		<!--<ol>
			<li>
				<label for="login" class="aligned">{t}User{/t}</label>
				<input type="text" name="login" id="login" readonly value="{$login}" class="cajag">
			</li>
			<li>
				<label for="realname" class="aligned">{t}First and last names{/t}</label>
				<input type="text" name="realname" id="realname" readonly value="{$realname}" class="cajag">
			</li>
			<li>
				<label for="email" class="aligned">{t}E-mail{/t}</label>
				<input type="text" name="email" id="email" readonly value="{$email}" class="cajag">
			</li>
		</ol>-->
	</fieldset>
	<fieldset class="buttons-form">
		{button class="validate" label="Delete" message="Are you sure you want to delete this user?"}
	</fieldset>
</form>
