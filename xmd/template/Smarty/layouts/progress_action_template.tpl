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

<div id="ajax_container" class="ui-widget ui-widget-content ui-corner-all">
	<div class="progress ui-widget ui-widget-content ui-helper-reset ui-corner-all" >
		<ul class=" indexProgress">
			{foreach from=$steps item=step}
				<li>{$step}</li>
			{/foreach}
		</ul>
	</div>
	<div class="contentProgress ui-helper-reset">
		<div class="container">
			{if ($view_head)}
				<h1>{$_ACTION_NAME|gettext}</h1>
				<h2 class='action_desc'><strong>{t}Description{/t}: </strong>{$_ACTION_DESCRIPTION|gettext}</h2>
				<h2 class='nodo_path'><strong>{t}Node{/t}: </strong>{$_NODE_PATH}</h2>
			{/if}
			{if ($_ACTION_CONTROLLER)}
				{include file="$_ACTION_CONTROLLER"}
			{/if}
		</div>
		<div class="footerActionProgress">
			<div class="buttonBefore">
				{button class="previousAction" label="Back" onclick="previous()}
			</div>
			<div class="buttonNext">
				{button label="Next" class="nextAction" onclick="next()"}
			</div>
		</div>
	</div>
</div>
