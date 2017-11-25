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
	<div class="action_header">
		<h5 class="direction_header"> Name Node: {t}User Manager{/t}</h5>
		<h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
		<hr>
	</div>
	<div class="action_content">
		<div class="row tarjeta">
			<div class="small-12 columns title_tarjeta">
				<h2 class="h2_general">{t}Add User{/t}</h2>
			</div>
			<div class="small-12 columns">
				<div class="input">
					<label  class="label_title label_general" for="login">{t}Username{/t} *</label>
					<input type="text" name="login" id="login" class="input_general full_size validable not_empty js_val_unique_name js_val_alphanumeric" placeholder="{t}Username in Ximdex CMS{/t}" tabindex="1" data-idnode="{$id_node}">
				</div></div>
				<div class="small-6 columns">
					<div class="input">
					<label for="pass" class="label_title label_general" for="pass">{t}Password{/t} *</label>
					<input type="password" name="pass" id="pass" class="input_general full_size validable not_empty js_val_min__6" placeholder="{t}Password{/t}"  tabindex="2">
					</div></div>
				<div class="small-6 columns">
					<label for="confirmpass" class="label_title label_general" for="confirmpass" >{t}Repeat password{/t} *</label>
					<input type="password" name="confirmpass" id="confirmpass" class="input_general full_size validable not_empty field_equals__pass" placeholder="{t}Password{/t}"  tabindex="3">
				</div>
			<div class="small-12 columns">
				<div class="input">
					<label for="login" class="label_title label_general" for="username">{t}Name and surname{/t} *</label>
					<input type="text" name="name" id="username" class="input_general full_size validable not_empty" placeholder="{t}Name and surname{/t}"  tabindex="4">
				</div></div>
			<div class="small-12 columns">
				<div class="input">
					<label for="email" class="label_title label_general" for="email">{t}E-mail{/t} *</label>
					<input type="text" name="email" id="email" class="input_general full_size validable not_empty is_email" placeholder="{t}E-mail{/t}"  tabindex="5">
				</div></div>
				<div class="small-6 columns">
					<div class="input-select" style="margin-bottom:15px!important;">
					<label for="generalrole" class="label_title label_general" for="generalrole">{t}Role in general group{/t}</label>
					<select name="generalrole" id="generalrole" class="full_size"  tabindex="6">
					{foreach from=$roles item=role}
						<option value="{$role.IdRole}">{$role.Name}</option>
					{/foreach}
					</select>
					</div></div>
				<div class="small-6 columns">
					<div class="input-select" style="margin-bottom:15px!important;">
					<label for="locale" class="label_title label_general" for="locale">{t}Interface language{/t}</label>
					<select name="locale" id="locale" class="full_size"  tabindex="7">
					{section name=i loop=$locales}
						<option value="{$locales[i].Code}" {if ($locales[i].Code == $smarty.const.DEFAULT_LOCALE)} selected{/if}>{$locales[i].Name|gettext} ({$locales[i].Lang})</option>
					{/section}
					</select>
					</div></div>
			<div class="small-8 columns">
				<div class="alert alert-info">
					<strong>Info!</strong> {t}Users registered without their consent may violate the rules of your country{/t}
				</div></div>
			<div class="small-4 columns">
				<fieldset class="buttons-form">
                    {button label='Create user' class='validate btn main_action btn_margin2' tabindex="8"}
				</fieldset>
			</div>
				</div></div>
</form>



