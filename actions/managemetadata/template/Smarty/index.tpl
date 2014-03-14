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

<form method="post" name="managemetadata" id="managemetadata_form" action="{$action_url}"
	ng-controller="MetadataCtrl"
    ng-init="nodeId = '{$nodeid}';"
    ng-cloak
    xim-languages='{$json_languages}'
    xim-defaultlanguage='{$default_language}'
    xim-method="{$go_method}"
    xim-action="{$action}"
    novalidate>
	<div class="action_header" ng-hide="submitMessages.length">
		<h2>{t}Manage metadata{/t}</h2>
		<fieldset class="buttons-form">
            <button class="btn main_action button_main_action button" 
                ng-click="submitForm(managemetadata)"
                xim-button
                xim-label="submitLabel"
                xim-state="submitStatus">
                {t}Save{/t}
            </button>
		</fieldset>
	</div>
    <div class="message" ng-show="submitMessages.length">
        <p class="ui-state-primary ui-corner-all msg-info">
            [[submitMessages]]
        </p>
    </div>
	<div class="action_content metadata_action">

		<div class="col2-3 left metadata_data">

            <select name="" id="" class="language_selector js_language_selector"
            	ng-model="defaultLanguage">
                <option 
                    ng-repeat="l in languages" 
                    ng-disabled="!l.Name"
                    ng-selected="defaultLanguage == l.IdLanguage" 
                    value="[[l.IdLanguage]]">
                    [[l.Name]]
                </option>
            </select>

            <div class="empty_state">
                <span class="title icon language">{t}No language selected{/t}</span>
                <span>{t}Select at less one above{/t}</span>
            </div>
           
            <div class="js_form_sections">
                {foreach from=$languages item=l}
                    <div class="js_form_section" id="language_selector_{$l.IdLanguage}" 
                        ng-show="defaultLanguage == {$l.IdLanguage}">
                       
                        {foreach from=$elements item=e}
                         <p>
                            <label for="languages_metadata[{$l.IdLanguage}][{$e.name}]" class="label_title">{t}{$e.name|upper}{/t}</label>
                            {if $e.type == 'text'}
                                <input name="languages_metadata[{$l.IdLanguage}][{$e.name}]" type="text" class="full_size" 
                                    ng-model="languages_metadata.{$l.IdLanguage}.{$e.name}" 
                                    ng-init="languages_metadata.{$l.IdLanguage}.{$e.name} = '{$languages_metadata[{$l.IdLanguage}][{$e.name}]}'">
                            {elseif $e.type == 'textarea'}
                                <textarea name="languages_metadata[{$l.IdLanguage}][{$e.name}]" id="" cols="30" rows="9" class="full_size"
                                    ng-model="languages_metadata.{$l.IdLanguage}.{$e.name}" 
                                    ng-init="languages_metadata.{$l.IdLanguage}.{$e.name} = '{$languages_metadata[{$l.IdLanguage}][{$e.name}]}'">
                                </textarea>
                            {else}
                                <br/>
                            {/if}
                              </p>
                        {/foreach}
                      
                    </div>
                {/foreach}
            </div>
         </div>
            
            

        <div class="col1-3 right metadata_info">
            <h4>{t}System metadata info{/t}</h4>

            <br>
            <img src="{$imagesrc}" alt="Thumbnail image" class="thumbnail_item">

            <div class="name_info">
                <h3>{t}Name [NodeID]{/t}</h3>
                <p>{$nodename} <span class="nodeid">[{$nodeid}]</span></p>
            </div>
            <div class="version_info">
                <h3>{t}Version{/t}</h3>
                <p>{$nodeversion}</p>
            </div>
            <div class="path_info" data-path="{$nodepath}" >
                <h3>{t}Path{/t}</h3>
                <div class="path_mask">
                <p data-path="{$nodepath}" class="path_uri">{$nodepath}</p>
                </div>
            </div>
        </div>
                
    </div>
		
	</div>

</form>
