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

	

<div class="col2-3 left">	

<h3>{t}Channels{/t}</h3>

	{if count($channels) > 0}
			{foreach from=$channels item=channel}
				<div class="channel-section">
					<input name='channels[]' type='checkbox' value='{$channel.IdChannel}' class="hidden-focus" id="{$channel.IdChannel}_{$idNode}"/>
					<label  class="icon checkbox-label"  for="{$channel.IdChannel}_{$idNode}">{$channel.Description|gettext}</label>
				</div>
			{/foreach}
		
	{else}
		<p>{t}There are no channels associated to this project{/t}</p>
	{/if}

</div>

<div class="languages-available col1-3"><h3>{t}Languages{/t}</h3>
		
		{if count($languages) > 0}
			
				{foreach from=$languages item=language}
					
					<div class="languages-section">
		
						<input name='languages[]' type='checkbox' value='{$language.IdLanguage}'  id='{$language.IdLanguage}_{$idNode}' class="hidden-focus"/>
						<label  for="{$language.IdLanguage}_{$idNode}" class="icon checkbox-label">{$language.Name|gettext}</label>
						<input type='text' name='aliases[{$language.IdLanguage}]' class="alternative-name" placeholder="{t}Alternative name for paths &amp; breadcrumbs{/t}"/>
					</div>
					
				{/foreach}
				
				
					<select class="alternative_select" name='master' id="master">
						<option value="">{t}Select master language{/t}</option>
						{foreach from=$languages item=language}
							<option  value='{$language.IdLanguage}'>{$language.Name|gettext}</option>
						{/foreach}
					</select>
				
		{else}
			<p>{t}There are no languages associated to this project{/t}</p>
		{/if}
	</div>
