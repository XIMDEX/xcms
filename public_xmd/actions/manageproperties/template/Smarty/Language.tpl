{**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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
	<div class="row tarjeta propertyform">
		<h2 class="h2_general">{t}Languages{/t}</h2>
		<div class="manageproperties">
			{if ($inProject eq false)}
				<div>
					<input type="radio" name="inherited_languages" class="languages_inherited" value="inherited" 
                            id="languages_inherited_{$id_node}" {if $Language_inherited == 'inherited'}checked="checked"{/if} />
					<label for="languages_inherited_{$id_node}">{t}Use inherited languages{/t}:</label>
					<ul class="inheritlist">
						{foreach from=$languages item=language}
							{if $language.Inherited}
								<li class="inherit_list_element">{$language.Name}</li>
							{/if}
				        {/foreach}
					</ul>
				</div>
				<div class="sep">
					<input type="radio" name="inherited_languages" class="languages_overwritten" value="overwrite" 
                            id="languages_overwritten_{$id_node}" {if $Language_inherited == 'overwrite'}checked="checked"{/if} />
					<label for="languages_overwritten_{$id_node}">{t}Overwrite inherited languages{/t}:</label>
				</div>
			{else}
				<input type="hidden" name="inherited_languages" value="overwrite" />
				<label>{t}Available project languages{/t}:</label>
			{/if}
			{if ($languages)}
				<div class="overwrited_properties">
					{foreach from=$languages item=language}
						<span name="check_languages" class="slide-element languagesmp{if $Language_inherited == 'inherited'} disabled{/if}">
							<input type="checkbox" class="languages input-slide" name="Language[]" id="language_{$language.Name}_{$id_node}" 
									value="{$language.Id}" {if $language.Checked == 1}checked="checked"{/if} />
								<label for="language_{$language.Name}_{$id_node}" class="label-slide"> {$language.Name}</label>
						</span>
					{/foreach}
				</div>
			{/if}
			<hr />
			<div>
				<input type="checkbox" class="languages_recursive" name="Language_recursive" id="Language_recursive_{$id_node}" value="1" /> 
				<label for="Language_recursive_{$id_node}">{t}Associate languages recursively{/t}</label>
			</div>
		</div>
	</div>
{/if}
