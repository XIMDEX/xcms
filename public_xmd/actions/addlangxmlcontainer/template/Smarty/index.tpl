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
	<input type="hidden" name="nodeid" value="{$idNode}" class="ecajag" />
	<input type="hidden" name="templateid" value="{$idTemplate}" class="ecajag" />
    <div class="action_header">
		<h5 class="direction_header"> {t}Name Node:{/t} {t}{$node_name}{/t}</h5>
		<h5 class="nodeid_header"> {t}ID Node:{/t} {$nodeid}</h5>
		<hr />
    </div>
	<div class="action_content">
		<div class="row tarjeta">
			<div class="small-12 columns title_tarjeta">
				<h2 class="h2_general">{t}Add language{/t}</h2>
			</div>
			<div class="small-12 columns">
				<div class="input">
				    <div class="info-node">
			  	    	<label class="label_title label_general">{t}Used schema{/t}</label>
						<div class="text-border">
	    	            	<span class="infor_form ">{$templateName}</span>
						</div>
					</div>
				</div>
			</div>
		  	{if $numlanguages neq 0}
				<div class="small-12 columns">
					<div class="input">
						<label class="label_title label_general">{t}Languages availables{/t} *</label>
						{foreach from=$languages item=language}
							<div class="languages-section">
								{if ($language.idChildren > 0)}
                   					<input type="checkbox" name="languages[]" id="lang_{$language.idLanguage}_{$idNode}" value="{$language.idLanguage}" 
                   							checked="checked" class="hidden-focus" />
								{else}
									<input type="checkbox" name="languages[]" id="lang_{$language.idLanguage}_{$idNode}" value="{$language.idLanguage}" 
											class= "hidden-focus"/>
								{/if}
								<label for="lang_{$language.idLanguage}_{$idNode}" class="icon checkbox-label">{$language.name}</label>
								<input type="text" name="aliases[{$language.idLanguage}]" class="alternative-name" value="{$language.alias}" 
										class="cajag" placeholder="{t}Alternative name for paths &amp; breadcrumbs{/t}">
							</div>
						{/foreach}
					</div>
				</div>
			{else}
				<p>{t}There are no languages associated to this project{/t}.</p>
			{/if}
		</div>
		<div class="small-12 columns">
			<fieldset class="buttons-form">
				{button label="Modify" class='validate btn main_action' }{*message="Would you like to save changes?"*}
			</fieldset>
		</div>
	</div>
</form>