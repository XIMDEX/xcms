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

<form ng-controller="XModifyMetadataGroups" ng-init='nodeid = {$idnode}; metadataList = {$metadata}; schemes = {$data}; init();' 
        method="post" action="{$action_url}" class="form_metadata" id="form_metadata">
    {include file="actions/components/title_Description.tpl"}
    <div class="message-success message" id="metadatagroups_success_message"></div>
    <div class="message-error message" id="metadatagroups_error_message"></div>
    <div ng-cloak class="action_content">
        <div class="row tarjeta">
            <div class="small-12 columns title_tarjeta">
                <h2 class="h2_general">{t}Associate metadata to group{/t}</h2>
            </div>
            <div>
                <div class="input-select icon columns addMetadataFormField" style="width: 30%;">
                    <label class="label_title label_general lable-text label-select" for="metadata">{t}Metadata{/t} *</label>
                    <select ng-model="metadata" id="metadata">
                        <option ng-repeat="metadata in metadataList | orderObjectBy: 'name'" 
                                value="#/metadata.idMetadata/#">#/metadata.name/#</option>
                    </select>
                </div>
                <div class="input-select icon columns addMetadataFormField" style="width: 35%;">
                    <label class="label_title label_general label-select" for="schema">{t}Scheme{/t} *</label>
                    <select ng-model="scheme" id="scheme">
                        <option ng-repeat="scheme in schemes | orderObjectBy: 'name'" value="#/scheme.id/#">#/scheme.name/#</option>
                    </select>
                </div>
                <div class="input-select icon columns addMetadataFormField" style="width: 35%;">
                    <label class="label_title label_general label-select" for="group">{t}Group{/t} *</label>
                    <select ng-model="group" id="group">
                        <option ng-repeat="group in schemes[scheme].groups | orderObjectBy: 'name'" 
                                value="#/group.id/#">#/group.name/#</option>
                    </select>
                </div>
                <div class="sep"></div>
                <div class="metadata_options">
                    <input type="checkbox" ng-model="required" value="1" id="metadata-required" class="metadata_checkbox_option" />
                    <label for="metadata-required">{t}Required{/t}</label>
                    <input type="checkbox" ng-model="readonly" value="1" id="metadata-readonly" class="metadata_checkbox_option" />
                    <label for="metadata-readonly">{t}Read only{/t}</label>
                    <input type="checkbox" ng-model="enabled" value="1" id="metadata-enabled" class="metadata_checkbox_option" />
                    <label for="metadata-enabled">{t}Enabled{/t}</label>
                </div>
            </div>
            <div class="small-12 columns">
                <fieldset class="buttons-form">
                    <button type="button" id="" ng-click="create()" 
                            class="btn ui-state-default ui-corner-all button submit-button ladda-button main_action" data-style="slide-up" 
                            data-size="xs"><span class="ladda-label">{t}Add to group{/t}</span></button>
                </fieldset>
            </div>
        </div>
        <div ng-if="hasElements(schemes)">
	        <fieldset>
	            <accordion close-others="true" ng-init="firstOpen = false; firstDisabled = false;">
		            <accordion-group ng-repeat="scheme in schemes | orderObjectBy: 'name'" heading="#/scheme.name/#" is-open="0">
		                <div class="clearfix metadata_group" ng-repeat="group in scheme.groups | orderObjectBy: 'name'">
		                    <h5 class="direction_header group_name">#/group.name/#</h5>
                            <hr />
		                    <ng-show ng-show="hasElements(group.metadata)">
			                    <div ng-repeat="metadata in group.metadata | orderObjectBy: 'name'" class="metadata_info">
			                        <div class="metadataInfo">
				                        <input type="text" ng-value="metadata.name" disabled="disabled" class="metadata_name_text"
	                                            title="{t}Metadata name{/t}" />
				                        <input type="text" class="metadata_values_text" ng-value="metadata.values" disabled="disabled" 
	                                            title="{t}Total of values related to metadata in this group{/t}" />
				                        <input type="checkbox" ng-model="metadata.required" id="metadata-required-#/metadata.id/#" 
	                                            class="metadata_checkbox_option" />
				                        <label for="metadata-required-#/metadata.id/#">{t}Required{/t}</label>
				                        <input type="checkbox" ng-model="metadata.readonly" id="metadata-readonly-#/metadata.id/#" 
		                                        class="metadata_checkbox_option" />
		                                <label for="metadata-readonly-#/metadata.id/#">{t}Read only{/t}</label>
		                                <input type="checkbox" ng-model="metadata.enabled" id="metadata-enabled-#/metadata.id/#" 
		                                        class="metadata_checkbox_option" />
		                                <label for="metadata-enabled-#/metadata.id/#">{t}Enabled{/t}</label>
		                            </div>
	                                <div class="metadataOptions" ng-if="metadata.values == 0">
					                    <button type="button" class="delete-btn icon btn-unlabel-rounded-delete metadataDeleteButton" 
					                           ng-click="remove(metadata.id, group.id, scheme.id)">
					                        <span>{t}Delete{/t}</span>
					                     </button>
					                </div>
					                <div class="sep"></div>
			                    </div>
		                    </ng-show>
		                    <ng-show ng-show="! hasElements(group.metadata)">
		                        <p class="no_metadata_in_group">{t}There is no metadata for this group{/t}</p>
		                    </ng-show>
		                </div>
		            </accordion-group>
	            </accordion>
	        </fieldset>
            <fieldset class="buttons-form">
                <button type="button" id="" ng-click="save()" 
                        class="btn ui-state-default ui-corner-all button submit-button ladda-button main_action" data-style="slide-up" 
                        data-size="xs"><span class="ladda-label">{t}Save metadata{/t}</span></button>
            </fieldset>
        </div>
    </div>
</form>
