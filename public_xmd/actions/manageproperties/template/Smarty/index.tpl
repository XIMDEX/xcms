{**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

<form method="post" name="modifyproperties" action="{$action_url}">
	{include file="actions/components/title_Description.tpl"}
	<div class="action_content">
		<fieldset>
			{foreach from=$properties key=prop item=property}
				{include file="actions/manageproperties/template/Smarty/`$prop`.tpl"}
			{/foreach}
            {if isset($DefaultServerLanguage) and isset($properties['Language']) and count($properties['Language'])}
                <div class="row tarjeta propertyform">
			        <h2 class="h2_general">{t}Default server language{/t}</h2>
			        <div class="manageproperties">
			             {foreach from=$properties['Language'] item=language}
                            {if $language.Inherited}
                                <div class="prop_default_server_language">
	                                <input type="radio" name="default_server_language" value="{$language.Id}" 
	                                    id="default_server_language_{$language.Id}" 
	                                    {if ($DefaultServerLanguage == $language.Id)}checked="checked"{/if} />
	                                <label for="default_server_language_{$language.Id}">{$language.Name}</label>
                                </div>
                            {/if}
                        {/foreach}
			        </div>
			    </div>
			{/if}
		</fieldset>
		<div class="small-12 columns">
			<fieldset class="buttons-form">
	            {button label="Modify properties" class="validate btn main_action"}
	            {*message="Are you sure you want to change default properties?"*}
			</fieldset>
		</div>
	</div>
</form>