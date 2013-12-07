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

{include file="`$_APP_ROOT`/xmd/template/Smarty/helper/li_for_js.tpl"}
<div class="container">
<div class="action_header">
		{if $view_head}
			<!-- <h2>{$_ACTION_NAME|gettext}</h2>-->
			{if $num_nodes > 1}
				<h2>
				{foreach from=$nodes item=node}
					{$node.name},
				{/foreach}
				</h2>
			{else}
				<h2>{$_NODE_PATH}</h2>
			{/if}
		{/if}
			<fieldset class="buttons-form">
	{if ($goback) }
		{button type="goback" history="$history_value" label="Go back"}
	{else}
		{button type="close" label="Close" class="btn main_action"}
	{/if}
	</fieldset>
	</div>
	<div class="action_container ui-widget">
<div class="message">
			{include file="`$_APP_ROOT`/xmd/template/Smarty/helper/messages.tpl"}
	
	</div>

	</div>
</div>
<script>

</script>
