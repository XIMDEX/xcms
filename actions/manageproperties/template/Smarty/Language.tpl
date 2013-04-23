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


{assign var="languages" value=$properties.Language}

<fieldset>

<legend><span>{t}Languages{/t}</span></legend>
<div class="manageproperties">

	<div class="xright-block">
		<input type="radio" name="inherited_languages" class="languages_inherited"
			value="inherited" {if $Language_inherited == 'inherited'}checked{/if} />
		<label>{t}Use inherited languages{/t}</label>
		<ol>
			<li>
			{foreach from=$languages item=language}
				{$language.Name},
			{/foreach}
			</li>
		</ol>
	</div>

	<div class="xright-block">

		<div>
			<input type="radio" name="inherited_languages" class="languages_overwritten"
				value="overwrite" {if $Language_inherited == 'overwrite'}checked{/if} />
			<label>{t}Overwrite inherited languages{/t}</label>
		</div>

		<div class="left-block">
			<ol>
				{foreach from=$languages item=language}
				<li>
					<input
						type="checkbox"
						class="languages"
						name="Language[]"
						value="{$language.IdLanguage}"
						{if ($language.Checked == 1)}
							checked="{$language.Checked}"
						{/if}
						{if $Language_inherited == 'inherited'}
							disabled
						{/if}
						/>
					{$language.Name}
				</li>
				{/foreach}
			</ol>
		</div>

<!--
		<div class="right-block">
			<ol>
				{foreach from=$languages item=language}
				<li>
					<div class="novisible recursive languages_recursive_{$language.IdLanguage}">
						<input
							type="checkbox"
							class="languages_recursive"
							name="Language_recursive[]"
							value="{$language.IdLanguage}"
							/>
						{t}Create idiomatic version recursively{/t}
					</div>
					<div class="{if ($language.Checked != 1)}novisible{/if} apply">
						<button
							type="button"
							class="languages_apply"
							name="Language_apply"
							value="{$language.IdLanguage}">
						{t}Crear idioma{/t}
						</button>
					</div>
				</li>
				{/foreach}
			</ol>
		</div>
-->
	</div>
</div>

</fieldset>
