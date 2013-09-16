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

	$channels[IdChannel]
	$channels[Description]
*}

<h2>{t}Languages{/t}</h2>
<fieldset>
	{if count($languages) > 0}
		<ol>
			{foreach from=$languages item=language}
				<li>

					<label class="aligned">{$language.Name|gettext}</label>
					<input name='languages[]' type='checkbox' value='{$language.IdLanguage}' />
					<input type='text' name='aliases[{$language.IdLanguage}]' class='cajag'/>
				</li>
			{/foreach}
			<li>
				<label class="aligned">{t}Select master language{/t}</label>
				<select class="cajaxg" name='master'>
					<option value="">&laquo;{t}None{/t}&raquo;</option>
					{foreach from=$languages item=language}
						<option  value='{$language.IdLanguage}'>{$language.Name|gettext}</option>
					{/foreach}
				</select>
			</li>
		</ol>
	{else}
		<p>{t}There are no languages associated to this project{/t}</p>
	{/if}
</fieldset>
<h2>{t}Channels{/t}</h2>
<fieldset>
	{if count($channels) > 0}
		<ol>
			{foreach from=$channels item=channel}
				<li><input name='channels[]' type='checkbox' value='{$channel.IdChannel}' />
				<label>{$channel.Description|gettext}</label>
				</li>
			{/foreach}
		</ol>
	{else}
		<p>{t}There are no channels associated to this project{/t}</p>
	{/if}
</fieldset>
