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


<form method="post" name="alx_form" id="alx_form" action="{$action_url}">
	<input type="hidden" name="nodeid" value="{$idNode}" class="ecajag">
	<input type="hidden" name="templateid" value="{$idTemplate}" class="ecajag">

  <div class="action_header">
	<h2>{t}Languages{/t}, {t}Channels{/t} </h2>
  	<fieldset class="buttons-form">
		{button label="Modify" class='validate btn main_action' }<!--message="Would you like to save changes?"-->
	</fieldset>
  </div>

	<div class="action_content">
		<fieldset>
		  <p>{t}Used template{/t}:  <span class="infor_form">{$templateName}</span></p>
				{if $numlanguages neq 0}
					<ol>
						{foreach from=$languages item=language}
							<li><label for="lang_{$language.idLanguage}" class="aligned">{$language.name}</label>

								{if ($language.idChildren > 0)}
								<input type="checkbox" name="languages[]"
									id="lang_{$language.idLanguage}" value="{$language.idLanguage}" checked="checked" />
								{else}
								<input type="checkbox"
									name="languages[]" id="lang_{$language.idLanguage}" value="{$language.idLanguage}" />
								{/if}
								<input type="text" name="aliases[{$language.idLanguage}]" value="{$language.alias}" class="cajag">
							</li>
						{/foreach}
					</ol>
				{else}
					<p>{t}There are no languages associated to this project{/t}</p>
				{/if}
		</fieldset>

		<fieldset>
				{if $numchannels neq 0}
					<ol>
						{foreach from=$channels item=channel}
							<li>
								<input type="checkbox" name="channels[]" value="{$channel.IdChannel}"
									{if ($channel.selected)}checked="checked"{/if}>
								<label>{$channel.Description}</label>
							</li>
						{/foreach}
					</ol>
				{else}
					<p>{t}There are no channels associated to this project{/t}</p>
				{/if}
		</fieldset>
	</div>


</form>
