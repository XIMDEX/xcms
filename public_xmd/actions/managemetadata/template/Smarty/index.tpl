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

<form method="post" name="managemetadata" id="managemetadata_form" action="{$action_url}" ng-controller="MetadataCtrl" ng-init="nodeId = '{$nodeid}';" ng-cloak xim-languages='{$json_languages}' xim-defaultlanguage='{$default_language}' xim-method="{$go_method}"
    xim-action="{$action}" novalidate>
    <div class="action_header">
        <h5 class="direction_header"> Name Node: {$name}</h5>
        <h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
        <hr>

    </div>
    <!--<div class="message" ng-show="submitMessages.length">
        <p class="ui-state-primary ui-corner-all msg-info">
            #/submitMessages/#
        </p>
    </div>-->
    <div ng-view ng-show="submitMessages.length" {literal}ng-class="{'message-success': submitStatus=='success'}" {/literal} class="slide-item message-success message">
        <p>#/submitMessages/#</p>
    </div>
    <div class="action_content metadata_action">

        <div class="small-8 columns metadata_data">
            <label class="label_title label_general">{t}Languages{/t}</label>
            <div class="input-select">
            <select name="" id="" class="language_selector js_language_selector" ng-model="defaultLanguage" ng-change="update(nodeId, defaultLanguage)">
                <option ng-repeat="l in languages" ng-disabled="!l.Name" ng-selected="defaultLanguage == l.IdLanguage" value="#/l.IdLanguage/#">
                    #/l.Name/#
                </option>
            </select>
            </div>
            <div class="empty_state">
                <div class="alert alert-info">
                    <strong>Info!</strong> {t}Select at less one above{/t}
                </div></div>

            <div class="js_form_sections">
                {foreach from=$languages_metadata key=l item=langelements}
                <div class="js_form_section" id="language_selector_{$l}" ng-show="defaultLanguage == {$l}">

                    {foreach from=$langelements item=e key=k}
                    <p>
                        <label for="languages_metadata[{$l}]['{$k}']" class="label_title">{t}{$k|upper}{/t}</label>
                        {if $elements[$k] == 'text'}
                        <input name="languages_metadata[{$l}]['{$k}']" type="text" class="full_size" ng-model="languages_metadata[{$l}]['{$k}']" ng-init="languages_metadata[{$l}]['{$k}'] = '{t}{$languages_metadata[{$l}][{$k}]}{/t}'"> {elseif $elements[$k] == 'textarea'}
                        <textarea name="languages_metadata[{$l}]['{$k}']" id="" cols="30" rows="9" class="full_size" ng-model="languages_metadata[{$l}]['{$k}']" ng-init="languages_metadata[{$l}]['{$k}'] = '{t}{$languages_metadata[{$l}][{$k}]}{/t}'">
                        </textarea>
                        {else}
                        <br/> {/if}
                    </p>
                    {/foreach}

                </div>
                {/foreach}
            </div>
        </div>
<!--metadata_info-->


        <div class="small-4 columns">
            <div class="row tarjeta">
            <h2 class="h2_general" style="margin-bottom:0;">{t}System metadata info{/t}</h2>

            <br>
            <img src="{$imagesrc}" alt="Thumbnail image" class="thumbnail_item">

            <div class="name_info cuadrado" >
                <h3>{t}Name [NodeID]{/t}</h3>
                <p>{$nodename} <span class="nodeid">[{$nodeid}]</span></p>
            </div>
            <div class="version_info cuadrado" >
                <h3>{t}Version{/t}</h3>
                <p ng-init="nodeversion = '{$nodeversion}'">
                    #/nodeversion/#
                </p>
            </div>
            <div class="path_info cuadrado" style="border-bottom:0!important" data-path="{$nodepath}">
                <h3>{t}Path{/t}</h3>
                <div class="path_mask">
                    <p data-path="{$nodepath}" class="path_uri">{$nodepath}</p>
                </div>
            </div>
            </div></div>
        <div class="small-12 columns">
        <fieldset class="buttons-form">
            <button class="btn main_action button_main_action button" ng-click="submitForm(managemetadata)" xim-button xim-label="'ui.dialog.confirmation.save' | xI18n" xim-state="submitStatus" xim-disabled="managemetadata.$invalid || managemetadata.$pristine">
            </button>
        </fieldset>
    </div>
    </div>
    <h2>{t}Manage metadata{/t}: {$name}</h2>

</form>
