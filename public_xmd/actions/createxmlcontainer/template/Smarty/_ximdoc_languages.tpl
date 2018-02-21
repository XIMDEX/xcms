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

{* Accepts
	$languages[Idlanguage]
	$languages[Name]
*}
<div class="languages-available small_12 columns">
	<label class="label_title label_general">{t}Languages{/t} *</label>
    {if count($languages) > 0}
        {foreach from=$languages item=language}
			<div class="languages-section">
				<input name='languages[]' type='checkbox' value='{$language.IdLanguage}'  id='{$language.IdLanguage}_{$idNode}' class="hidden-focus" />
				<label  for="{$language.IdLanguage}_{$idNode}" class="icon checkbox-label">{$language.Name|gettext}</label>
				<input type='text' name='aliases[{$language.IdLanguage}]' class="alternative-name" 
						placeholder="{t}Alternative name for paths &amp; breadcrumbs{/t}" />
			</div>
        {/foreach}
		<div class="input-select icon">
			<p>
				<select name='master' class="cajaxg document-type" id="master">
					<option value="">{t}Select master language{/t}</option>
		            {foreach from=$languages item=language}
						<option  value='{$language.IdLanguage}'>{$language.Name|gettext}</option>
	        	    {/foreach}
				</select>
			</p>
		</div>
    {else}
		<p>{t}There are no languages associated to this project{/t}</p>
    {/if}
</div>