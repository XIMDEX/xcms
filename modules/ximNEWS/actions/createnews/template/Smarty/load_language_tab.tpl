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

<ol>
	{foreach from=$form_elements key=ene item=element}

			{if $element.type eq 'text'}
				<li>
					<label class="aligned">{$element.label}</label>
					<input type="text" name="{$element.name}_{$id_lang}" value="" class="cajaxxg"/>
				</li>
			{elseif $element.type eq 'fecha'}
				<li>
					<label class="aligned">{$element.label}</label>
					<input type="text" name="{$element.name}_{$id_lang}" id="{$element.name}_{$id_lang}"
								value="{$now}" class="calendarior"/>
				</li>
			{elseif $element.type eq 'textarea'}
				<li>
					<label class="aligned">{$element.label}</label>
					<textarea name="{$element.name}_{$id_lang}" rows="7" cols="100"	class="cajaxxg"/></textarea>
				</li>
			{else}

			{/if}
	{/foreach}
</ol>
