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

<form method="post" id="language_form" action="{$action_url}"   class='validate_ajax'>
<input type="hidden" name="nodeid" id='nodeid' value="{$nodeid}">
<div class="action_header">
	<h2>{t}Add language{/t}</h2>
	<fieldset class="buttons-form">
			{button label="Create" class='validate  btn main_action'}{*message="Do you want to create the language?"*}

	</fieldset>
</div>
<div class="action_content">
	<fieldset>
		<ol>
				<li><label for="name" class="aligned">{t}Name{/t}</label><input type="text" name="langname" id="langname" class="cajaxg validable not_empty"></li>
				<li><label for="isoname" class="aligned">{t}ISO name{/t}</label>
				<select name="isoname" id="isoname" class="cajag validable not_empty">
				<option value="">{t}Select an ISO code{/t}</option>
				{foreach from=$languages item=language}
					<option value="{$language.code}">{$language.name} ( {$language.code} )</option>
				{/foreach}
				</select>
			</li>
		<li><label for="description" class="aligned">{t}Description{/t}</label><input type="text" name="description" id="description" class="cajaxg validable not_empty"></li>
		<li><label for="enabled" class="aligned">{t}Activated{/t}</label><input type="checkbox" name="enabled" id="enabled" value="1" checked="checked"></li>
				</ol>
				</fieldset>
</div>

</form>
