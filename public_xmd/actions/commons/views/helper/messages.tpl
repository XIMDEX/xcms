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

{if count($messages) > 0}
	{foreach from=$messages item=message}
		{if ($message.type == "0")}
			{assign var="class" value="message message-error"}
			{assign var="icon" value="ui-icon ui-icon-alert"}
		{elseif ($message.type == "1")}
			{assign var="class" value="message message-warning"}
			{assign var="icon" value="ui-icon ui-icon-notice"}
		{else}
			{assign var="class" value="message message-info"}
			{assign var="icon" value="ui-icon ui-icon-info"}
		{/if}

		<div class="action_content {$class}">
			<div class="small-12 columns">
				<div class="alert alert-info">
					<strong>Info!</strong>{$message.message}
		</div></div></div>
	{/foreach}
{else}
	<div class="action_content">
		<div class="small-12 columns">
			<div class="alert alert-info">
				<strong>Info!</strong>{t}No messages found{/t}
			</div></div>
	</div>
{/if}
