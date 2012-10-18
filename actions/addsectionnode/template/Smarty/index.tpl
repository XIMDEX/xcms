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
	<input type="hidden" name="nodeid" VALUE="{$nodeID}">

	<fieldset>
    <legend><span>{t}Add section{/t}</span></legend>
		<ol>
			<li>
				<label for="name" class="aligned">{t}Name{/t}</label>
				<input type="text" name="name" id="name" maxlength="100" class="cajaxg validable not_empty">
			</li>
			<li>

				<label class="aligned">{t}Section type{/t}</label>
				{if $sectionTypeCount > 1}
					<select name="nodetype" class="caja validable not_empty">
					{foreach from=$sectionTypeOptions item=sectionTypeOption}
						<option value="{$sectionTypeOption.id}">{$sectionTypeOption.name}</option>
					{/foreach}
					</select>
				{else}
					<p>{t}Normal{/t}</p>
					<input name="nodetype" type="hidden" value="{$sectionTypeOptions.id}" />
				{/if}
			</li>
			{if $otfAvailable}
				<li>
					<input name="sectionOTF" type='checkbox' value="isSectionOTF">
					<label>{t}OTF section{/t}</label>
				</li>
			{/if}
		</ol>
	</fieldset>

	<fieldset>
		<legend><span>{t}Languages{/t}</span></legend>
			{if $languageCount neq 0}
				<ol>
					{foreach from=$languageOptions item=languageOption}
						<li>
							<input name="langidlst[]" type='checkbox' value="{$languageOption.IdLanguage}">
							<label>{$languageOption.Name|gettext}</label>
							<input type="text" name="namelst[{$languageOption.IdLanguage}]" class="cajaxg">
						</li>
					{/foreach}
				</ol>
			{else}
				<p>There are no languages associated to this project</p>
			{/if}
	</fieldset>

	{if $availableThemesCount > 0}
		<fieldset>
			<legend><span>{t}Themes{/t}</span></legend>
			<p>	<label class="aligned">{t}Selected theme{/t}</label>
				<select name="selectedTheme" class="caja validable not_empty">
					{foreach from=$availableThemes key=key item=theme}
						<option value="{$key}">{$theme}</option>
					{/foreach}
				</select></p>
		</fieldset>
	{/if}

	<fieldset class="buttons-form">
		{button label="Reset" class='form_reset' type="reset"}
		{button label="Create section" class='validate' }<!--message="Would you like to add this section?"-->
	</fieldset>
</form>
