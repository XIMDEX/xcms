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

<form ng-controller="XModifyMetadata" ng-init='nodeid = {$idnode}; types = {$metadataTypes}; metadataList = {$metadata}; init();' 
        method="post" action="{$action_url}" class="form_metadata" id="form_metadata">
    {include file="actions/components/title_Description.tpl"}
    <div class="message-success message" id="metadata_success_message"></div>
    <div class="message-error message" id="metadata_error_message"></div>
    <div class="action_content">
        <div class="row tarjeta">
            <div class="small-12 columns title_tarjeta">
                <h2 class="h2_general">{t}Create new metadata{/t}</h2>
            </div>
            <div>
                <div class="input-text icon columns addMetadataFormField" style="width: 30%;">
                    <label class="label_title label_general lable-text" for="metadataName">{t}Name{/t} *</label>
                    <input type="text" ng-model="name" class="input_general cajaxg validable not_empty" id="metadataName" maxlength="255" />
                </div>
                <div class="input-text icon columns addMetadataFormField" style="width: 40%;">
                    <label class="label_title label_general" for="metadataDefaultValue">{t}Default value{/t}</label>
                    <input type="text" ng-model="defaultValue" class="input_general cajaxg validable not_empty" id="metadataDefaultValue" />
                </div>
                <div class="input-select icon columns addMetadataFormField" style="width: 30%;">
                    <label class="label_title label_general label-select" for="metadataType">{t}Type{/t} *</label>
                    <select ng-model="type" id="metadataType">
                        <option ng-repeat="type in types" value="#/type/#">#/type/#</option>
                    </select>
                </div>
                <div class="sep"></div>
            </div>
            <div class="small-12 columns">
                <fieldset class="buttons-form">
                    <button type="button" id="" ng-click="create()" 
                            class="btn ui-state-default ui-corner-all button submit-button ladda-button main_action" data-style="slide-up" 
                            data-size="xs"><span class="ladda-label">{t}Add{/t}</span></button>
                </fieldset>
            </div>
        </div>
        <br />
        <div class="row tarjeta">
            <h2 class="h2_general">{t}Metadata list{/t}</h2>
            <div ng-repeat="metadata in metadataList | orderBy: 'key'" class="row-item icon">
                <div class="metadataInfo">
                    <div class="input-text icon columns addMetadataFormField" style="width: 30%;">
                        <input type="text" ng-model="metadata.name" class="input_general cajaxg validable not_empty" 
                                ng-value="metadata.name" maxlength="255" title="{t}Metadata name{/t}" />
                    </div>
                    <div class="input-text icon columns addMetadataFormField" style="width: 40%;">
                        <input type="text" ng-model="metadata.defaultValue" class="input_general cajaxg validable" 
                                ng-value="metadata.defaultValue" title="{t}Metadata optional default value{/t}" />
                    </div>
                    <div class="input-text icon columns addMetadataFormField" style="width: 20%;">
	                    <input type="text" class="input_general cajaxg" ng-value="metadata.type" disabled="disabled" 
	                           title="{t}Metadata type{/t}" />
	                </div>
	                <div class="input-text icon columns addMetadataFormField" style="width: 10%;">
                        <input type="text" class="input_general cajaxg" ng-value="metadata.values" disabled="disabled" 
                                title="{t}Total of values related to metadata{/t}" />
                    </div>
                </div>
	            <div class="metadataOptions">
                    <button type="button" class="delete-btn icon btn-unlabel-rounded-delete metadataDeleteButton" 
                            ng-click="remove(metadata.idMetadata)">
                        <span>{t}Delete{/t}</span>
	                 </button>
	            </div>
	            <div class="sep"></div>
            </div>
            <div class="small-12 columns" ng-if="metadataList.length <= 0">
                <div class="alert alert-info">
                    <strong>Info!</strong> {t}There are no metadata created yet{/t}.
                </div>
            </div>
            <div class="small-12 columns" ng-if="metadataList.length > 0 || removed.length > 0">
                <fieldset class="buttons-form">
                    <button type="button" id="" ng-click="openSaveModal()" 
                            class="btn ui-state-default ui-corner-all button submit-button ladda-button main_action" data-style="slide-up" 
                            data-size="xs"><span class="ladda-label">{t}Save metadata{/t}</span></button>
                </fieldset>
            </div>
        </div>
    </div>
</form>
