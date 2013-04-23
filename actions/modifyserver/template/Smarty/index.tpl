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

<form method="post" id='mdfsv_form' name="formulario" action='{$action_url}'>
	<input type="hidden" name="nodeid" value="{$id_node}">
	<input type="hidden" name="actionid" value="{$id_action}">
	<input type="hidden" id="nodeURL" name="nodeURL" value="{$nodeURL}">
	<input type="hidden" id="num_servers" name="num_servers" value="{$num_servers}">
	<input type="hidden" name="id" value="{if (!$server.id)}{$id_server}{else}none{/if}">

	<fieldset class="mdfsv_errors">
		<legend><span>{t}Error messages{/t}</span></legend>
		<p>
			<div class="messages">
				<div class="ui-widget messages errors-container">
				</div>
			</div>
		</p>
	</fieldset>

	<fieldset id="select_server">
		<legend><span>{t}Selected server{/t}</span></legend>
		<p>
				<label class="aligned">{t}Name{/t}</label>
				<select name='serverid' id="serverid" class='cajag'>
					<option {if (!$id_server)}selected{/if} value="none">{t}Create new server{/t}</option>
					{foreach from=$servers item=_server}
						<option  {if ($_server.Id == $id_server)} selected{/if} value="{$_server.Id}">{$_server.Description}
							</option>
					{/foreach}
				</select>
			</p>
	</fieldset>

	<fieldset id="props_server">
		<legend><span>{t}Server properties{/t}</span></legend>
        <ol>
			<li>
				<label for='protocol' class="aligned">{t}Protocol{/t}</label>
				<select name='protocol' id='protocol' class='cajag'>
					{foreach from=$protocols item=_protocol}
						<option value='{$_protocol.Id}' {if ($server.protocol == $_protocol.Id)} selected {/if}>
							{$_protocol.Description|gettext}
						</option>
					{/foreach}
				</select>
			</li>
			<li>
				<label id='labelUrl' for='url' class="aligned">{t}Remote URL{/t}</label>
				<input type="text" id='url' name='url' MAXLENGTH="100" VALUE="{$server.url}" class='cajag'>
			</li>
			<li class="host">
				<label id='labeldirRemota' for='host' class="aligned">{t}Remote address{/t}</label>
				<input type="text" id='host' name='host' MAXLENGTH="100" VALUE="{$server.host}" class='cajag'>
			</li>
			<li class="port">
				<label for='port' class="aligned">{t}Remote port{/t}</label>
				<input type="text" id='port' name='port' MAXLENGTH="100" VALUE="{$server.port}" class='cajag'>
			</li>
			<li>
				<label id='labelDirectorio' for='initialdirectory' class="aligned">{t}Remote directory{/t}</label>
				<input type="text" id='initialdirectory' name='initialdirectory' MAXLENGTH="100" VALUE="{$server.directory}" class='cajag'>
			</li>
			<li class="login">
				<label for='login' class="aligned">{t}Remote user{/t}</label>
				<input type="text" id='login' name='login' MAXLENGTH="100" VALUE="{$server.user}" class='cajag'>
			</li>
			<li class="password">
				<label for='password' class="aligned">{t}Password{/t}</label>
				<input type="password" id='password' name='password' class='cajag'>
			</li>
			<li class="password2">
				<label for='password2' class="aligned">{t}Repeat password{/t}</label>
				<input type="password" name='password2' id="password2" class='cajag'>
			</li>
			<li>
				<label for='description' class="aligned">{t}Description{/t}</label>
				<input type="text" id='description' name='description' MAXLENGTH="100" VALUE="{$server.description}" class='cajag'>
			</li>
			{if $otfAvailable}
				<li>
					<label for='serverOTF' class="aligned">{t}OTF server{/t}</label>
					<input type="checkbox" id='serverOTF' value='1' name='serverOTF' {if ($server.isServerOTF)}checked{/if}>
				</li>
			{/if}
			<li>
				<input type="checkbox" id='enabled' name='enabled' {if ($server.enable)}checked{/if}> <label for='enabled' class="aligned"> {t}Enabled{/t}</label>

			</li>
			<li>
				<input type="checkbox" id='preview' name='preview' {if ($server.preview)}checked{/if}> <label for='preview' class="aligned">{t}Preview server{/t}</label>

			</li>
			<li>
				<input type="checkbox" id='override' name='overridelocalpaths' {if ($server.path)}checked{/if} > <label for='overridelocalpaths' class="aligned">{t}Absolute URLs{/t}</label>

			</li>
			<li>
				<label for='encode' class="aligned">{t}Encoding{/t}</label>
				<select id='encode' name='encode' class='cajag'>
					{foreach from=$encodes item=_encode}
						<option value='{$_encode.Id}' {if ($server.encode == $_encode.Id)} selected {/if}>
						{$_encode.Description}
						</option>
					{/foreach}
				</select>
			</li>
		</ol>
	</fieldset>

	<fieldset>
		<legend><span>{t}Channels{/t}</span></legend>
		{if $numchannels neq 0}
			<ol>
			{foreach from=$channels item=_channel}
				<li>
					<label for='channels[]' class="aligned"> {$_channel.Description|gettext}</label>
					<input id='channels[]' name='channels[]' type='checkbox' value='{$_channel.IdChannel}' {if ($_channel.InServer)}checked{/if}>
				</li>
			{/foreach}
			</ol>
		{else}
			<p>{t}There are no channels associated to this project{/t}</p>
		{/if}
	</fieldset>

	<fieldset class="buttons-form">
		<input type="hidden" name="borrar">
		{if (0 != $id_server)}
			{button id="delete_server" label="Delete server"}
			{button id="create_server" label="Update" class="validate"}<!--message="Would you like to create this server?"-->
		{else}
			{button id="create_server" label="Create" class="validate"}<!--message="Would you like to create this server?"-->
		{/if}
	</fieldset>
</form>
