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

<ol>
{if count($messages) > 0}
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
{else}
	<li>{t}No messages found{/t}</li>
{/if}
</ol>
