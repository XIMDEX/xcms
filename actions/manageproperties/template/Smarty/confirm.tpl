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
<h2>{t}Edit properties{/t}</h2>
<form method="post" name="modifyproperties" action="{$action_url}">

	<fieldset class="">


		<input type="hidden" name="nodeId" value="{$nodeId}" />

		{foreach from=$properties key=propName item=propValue}
			{foreach from=$propValue item=value}
				<input type="hidden" name="{$propName}[]" value="{$value}" />
			{/foreach}
		{/foreach}

		<ol>
		{foreach from=$messages item=message}
			{if ($message.type == "0")}
				{assign var="class" value="ui-state-error ui-corner-all msg-error"}
				{assign var="icon" value="ui-icon ui-icon-alert"}
			{elseif ($message.type == "1")}
				{assign var="class" value="ui-state-highlight ui-corner-all msg-warning"}
				{assign var="icon" value="ui-icon ui-icon-notice"}
			{else}
				{assign var="class" value="ui-state-primary ui-corner-all msg-info"}
				{assign var="icon" value="ui-icon ui-icon-info"}
			{/if}
			<li class="{$class}">
				<span class="{$icon}"></span>
				{$message.message}
			</li>
		{/foreach}
			<li class="{$class}">
				<span class="{$icon}"></span>
				{t}Changes will not be undone.{/t}
			</li>

			<li class="">
				<input type="checkbox" value="YES" name="confirmed" />
				{t}Would you like to continue with the changes?{/t}
			</li>
		</ol>

	</fieldset>

	<fieldset class="buttons-form">
		{button label="Cancel" type="goback" class="btn"}
		{button label="Modify" class="validate  btn main_action" }
	</fieldset>

</form>
