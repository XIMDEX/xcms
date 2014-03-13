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
		    {button label="Modify" class='validate btn main_action' }{*message="Would you like to save changes?"*}
	    </fieldset>
    </div>

	<div class="action_content">
	    <fieldset>
		    <div class="info-node">
		  	    <h3 class="info-title icon">{t}Used schema{/t}</h3>
                <span class="infor_form">{$templateName}</span>
		    </div>
		  	<div class="col2-3">		
				<h3>{t}Available channels{/t}</h3>
		  	{if $numchannels neq 0}
		 		{foreach from=$channels item=channel}
		  		    <div class="channel-section">
		  			    <input type="checkbox" name="channels[]" id="channel_{$channel.IdChannel}" value="{$channel.IdChannel}" {if ($channel.selected)} checked="checked" {/if} class="hidden-focus">
		  				<label class="icon checkbox-label" for="channel_{$channel.IdChannel}">{$channel.Description}</label>
		  			</div>
		  		{/foreach}
		  	{else}
		  			<p>{t}There aren't any channels associated to this project{/t}.</p>
		  	{/if}
		  	</div>	

		  	{if $numlanguages neq 0}
			<div class="col1-3">
				<h3>{t}Languages availables{/t}</h3>
						{foreach from=$languages item=language}
				<div class="languages-section">
				{if ($language.idChildren > 0)}
                    <input type="checkbox" name="languages[]" id="lang_{$language.idLanguage}" value="{$language.idLanguage}" checked="checked" class="hidden-focus" />
				{else}
					<input type="checkbox" name="languages[]" id="lang_{$language.idLanguage}" value="{$language.idLanguage}" class= "hidden-focus"/>
				{/if}
				<label for="lang_{$language.idLanguage}" class="icon checkbox-label">{$language.name}</label>
				<input type="text" name="aliases[{$language.idLanguage}]" class="alternative-name" value="{$language.alias}" class="cajag" placeholder="{t}Alternative name for paths &amp; breadcrumbs{/t}">
				</div>
						{/foreach}
					
				{else}
					<p>{t}There are no languages associated to this project{/t}.</p>
				{/if}
			</div>
		</fieldset>

		

	
	</div>


</form>
