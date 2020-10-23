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

<form method="post" id="print_form" action="{$action_url}">
    <input name="theme" type="hidden" />
    {include file="actions/components/title_Description.tpl"}
    {if {empty($langs)}}
        <div class="message-warning message">
            <p>{t}There aren't any created languages. You should create a new one{/t}.</p>
        </div>
    {/if}
    <div class="action_content">
        <div class="row tarjeta">
            <div class="small-12 columns title_tarjeta">
                <h2 class="h2_general">{t}Add project{/t}</h2>
            </div>
            <div class="small-12 columns">
                <div class="input">
                    <label class="label_title label_general">{t}Name{/t} *</label>
                    <div class="icon icon-positioned project input">
                        <input type="text" name="name" id="foldername" 
                        		class="input_general_icon cajaxg validable not_empty js_val_alphanumeric js_val_unique_name full-size" 
                        		placeholder="{t}Project name{/t}" data-idnode="{$nodeID}" required />
                    </div>
                </div>
            </div>
            <div class="small-12 columns">
                <label class="label_title label_general">{t}Available channels{/t} *</label>
                {foreach from=$channels key=index item=channelData}
                	<div class="channel">
                		<input type="checkbox" class="validable canales check_group__canales hidden-focus"  
                				name="channels_listed[{$channelData.id}]" id="p_{$channelData.id}" value="{$channelData.id}" />
                		<label for="p_{$channelData.id}" class="checkbox-label icon">{$channelData.name}</label>
                	</div>
            	{foreachelse}
            		<p class="message_warning">{t}There are no channels created in the system{/t}.</p>
            	{/foreach}
            	<div class="sep"></div>
            </div>
            <div class="small-12 columns">
                <label class="label_title label_general">{t}Available languages{/t} *</label>
                {foreach from=$langs key=index item=languageData}
                	<div class="language">
                		<input type="checkbox" class="validable idiomas check_group__idiomas hidden-focus"  
                				name="languages_listed[{$languageData.id}]" id="p_{$languageData.id}" value="{$languageData.id}" />
                		<label for="p_{$languageData.id}" class="checkbox-label icon">{$languageData.name}</label>
                	</div>
            	{foreachelse}
            		<p class="message_warning">{t}There are no languages created in the system{/t}.</p>
            	{/foreach}
            	<div class="sep"></div>
            </div>
            {if {!empty($themes)}}
                <label class="label_title label_general">{t}Available themes{/t}</label>
                <div class="row themes">
                    {$i = 0}
                    {foreach from=$themes key=index item=theme}
                        {if $i > 0 and $i % 4 eq 0}
                            <div class="sep"></div>
                        {/if}
                        <div class="themeContainer">
                            <div class="theme">
                                <div class="img_container">
                                    <img src="{url}actions/addfoldernode/themes/{$theme.name}/{$theme.name}.png{/url}" alt="{$theme.title}" />
                                    <div class="actions">
                                        <a href="" class="icon select" data-theme="{$theme.name}">Select</a> {if $theme.configurable}
                                            <a data-theme="{$theme.name}" href="" class="icon custom">Custom</a> {/if}
                                    </div>
                                </div>

                                <p class="title">{t}{$theme.title}{/t}</p>
                                <p class="type">{t}{$theme.description}{/t}</p>
                            </div>
                        </div>
                        {$i = $i + 1}
                    {/foreach}
                </div>
            {/if}

            <div class="small-12 columns">
                <fieldset class="buttons-form ">
                    {button label="{t}Create project{/t}" class='validate btn main_action'}
                </fieldset>
            </div>
		</div>
    </div>
</form>