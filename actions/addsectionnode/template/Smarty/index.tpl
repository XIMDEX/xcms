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

<form method="post" name="as_form" id="as_form" action="{$action_url}">
	<input type="hidden" name="nodeid" value="{$nodeID}"/>
	<input type="hidden" id="nodeURL" name="nodeURL" value="{$nodeURL}"/>
	<div class="action_header">
		<h2>{t}Add section{/t}</h2>
		
	</div>
	<div class="action_content section-properties">
        <div class="folder-name folder-normal icon input-select">
            <input type="text" name="name" id="name" maxlength="100" class="cajaxg full-size js_val_unique_name js_val_alphanumeric not_empty" placeholder="{t}Name of your section{/t}" data-idnode="{$nodeID}" />
            {if $sectionTypeCount > 1}
                <select id="type_sec" name="nodetype" class="caja validable not_empty folder-type">
                {foreach from=$sectionTypeOptions item=sectionTypeOption}
                    <option {if ($sectionTypeOption.id == $selectedsectionType)} selected{/if} value="{$sectionTypeOption.id}">{t}{$sectionTypeOption.name}{/t}</option>
                {/foreach}
                </select>
            {else}
                <input name="nodetype" type="hidden" value="{$sectionTypeOptions.id}" />
            {/if}
        </div>
		{include file="`$_APP_ROOT`/actions/addsectionnode/template/Smarty/languages.tpl"}
		{include file="`$_APP_ROOT`/actions/addsectionnode/template/Smarty/normal.tpl"}
	</div>
	<fieldset class="buttons-form positioned_btn">
		{button label="Create section" class='validate btn main_action' }{*message="Would you like to add this section?"*}
	</fieldset>	
</form>
