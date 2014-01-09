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

<form method="post" name="print_form" id="print_form" action="{$action_url}">
	<input type="hidden" name="nodeid" value="{$nodeID}">
	<div class="action_header">
		<h2>{t}Available languages and translation{/t}</h2>
		<fieldset class="buttons-form">
			<a href="{$_MESSAGES_PATH}" class="lbOn validate">{button label="Modificar" class='validate btn main_action' }{*message="Would you like update this information?"*}</a>
		</fieldset>
	</div>
	<div class="action_content">
	{if $languageCount neq 0}
		<fieldset>
			{foreach from=$language_info key=id_language item=language}
				<p>
					<label class="aligned">{t}Translation from{/t} {$language.NAME}</label>
					<input type="text" name="language[{$id_language}]" value="{$language.ALIAS}" class="cajaxg">
				</p>
			{/foreach}
		</fieldset>
	{else}
		<fieldset>
			<legend><span>{t}Available languages{/t}</span></legend>
			<p>{t}There are no languages associated to this project{/t}</p>
		</fieldset>
	{/if}
	</div>
</form>

