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

{if isset($properties.Language) and count($properties.Language) > 0}
	{assign var="languages" value=$properties.Language}
	<fieldset>
		<div class="manageproperties">
			<div class="small-12 columns">
			<label class="label_title label_general2">{t}Languages{/t}</label>
			<div class="row-item row-special">
				<div class="hidden">
					<input type="radio" name="inherited_languages" class="languages_overwritten"
						value="overwrite" checked />
					<label>{t}Overwrite inherited languages{/t}</label>
				</div>
				{foreach from=$languages item=language}
					<span class="slide-element">
						<input type="checkbox" class="languages input-slide" name="Language[]" id="{$language.Name}_{$id_node}" 
							value="{$language.IdLanguage}"
							{if $language.Checked == 1}
								checked="checked"
							{/if} />
						<label for="{$language.Name}_{$id_node}" class="label-slide "> {$language.Name}</label>
					</span>
				{/foreach}
			</div>
			</div>
		</div>
	</fieldset>
{/if}