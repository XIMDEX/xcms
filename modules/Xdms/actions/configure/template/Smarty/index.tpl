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

<form method="POST" name="tags_form" class="setmetadata-tags_form"
	ng-controller="XTagsCtrl"
	xim-document-tags='{$tags}'
	xim-cloud-tags='{$cloud_tags}'
	xim-namespaces='{$namespaces}'
	xim-node-id="{$id_node}"
	ng-cloak>
	<div class="action_header">
		<h2>{t}Tag this node{/t}: {$node_name}</h2>
	</div>
	<div class="message slide-item" ng-show="submitMessages.length" ng-class="{literal}{'message-success': submitState == 'success'}{/literal}">
	    <p class="ui-state-primary ui-corner-all msg-info" ng-repeat="message in submitMessages">
	        #/message.message/#
	    </p>
	</div>
	<div class="action_content">
		<div class="xim-tagsinput-container-list">
			<div class="xim-tagsinput-newtag  col2-3">
				<xim-select class="tag-type btn-rounded"
					ng-model="newTag.IdNamespace"
					xim-options="namespaces"
					xim-label-prop="type"
					xim-style-prop="nemo"
					xim-sel-prop="id"
					ng-init="newTag.IdNamespace = namespaces['1'].id">
				</xim-select>
				<input type="text" class="xim-tagsinput-input" id="tag_input" placeholder="{t}Create new tags here{/t}..." ng-model="newTag.Name" ng-class="{literal}{error: tagExistInArray(newTag, documentTags)}{/literal}" ng-keyup="keyPress($event)"/>
				<button type="button" class="btn-unlabel-rounded icon add-btn" ng-click="addNewTag()" ng-disabled="tagExistInArray(newTag, documentTags)">{t}Add{/t}</button>
			</div>
	    </div>
		<div class="xim-tagsinput-container col2-3">
			<div class="title-box">{t}Document tags{/t}</div>
	   		<ul class="xim-tagsinput-list">
	   			<li class="xim-tagsinput-tag icon xim-tagsinput-type-#/namespaces[tag.IdNamespace].nemo/#" {literal}ng-class="{'noxtooltip': (tag.Description==null||tag.Description=='')}"{/literal} ng-repeat="tag in documentTags">
	   				<a ng-href="#/tag.Link/#" target="_blank" class="xim-tagsinput-text" data-xtooltip="#/tag.Description/#">
					#/tag.Name/#
					</a>
 					<a ng-if="(namespaces[tag.IdNamespace].nemo == 'dPerson'
 					            || namespaces[tag.IdNamespace].nemo == 'dPlace'
                                || namespaces[tag.IdNamespace].nemo == 'dOrganisation'
                                || namespaces[tag.IdNamespace].nemo == 'dCreativeWork'
                                || namespaces[tag.IdNamespace].nemo == 'dOthers') &&
 					    (namespaces[tag.IdNamespace].uri != null && namespaces[tag.IdNamespace].uri != '')"
                       ng-href="#/namespaces[tag.IdNamespace].uri/#" class="ontology_link"
                       target="_blank">#/namespaces[tag.IdNamespace].type/#</a>
					<a ng-if="(namespaces[tag.IdNamespace].nemo != 'dPerson'
 					            && namespaces[tag.IdNamespace].nemo != 'dPlace'
                                && namespaces[tag.IdNamespace].nemo != 'dOrganisation'
                                && namespaces[tag.IdNamespace].nemo != 'dCreativeWork'
                                && namespaces[tag.IdNamespace].nemo != 'dOthers')"
                       href="#" class="ontology_link"
                       >#/namespaces[tag.IdNamespace].type/#</a>
					<a class="xim-tagsinput-tag-remove icon" href="#" ng-click="removeTag($index)"> &times; </a>
				</li>
				<p ng-hide="documentTags.length">{t}There aren't any tags defined yet{/t}.</p>
	    	</ul>
	    </div>
		
		<div class="suggested col1-3">
			{if $isStructuredDocument}
				<xtags-suggested xim-on-select="addTag(tag)" xim-node-id="nodeId" xim-document-tags="documentTags"></xtags-suggested>
			{/if}
			<div class="tagcloud xim-tagsinput-container-related" ng-if="cloudTags.length && cloudTags.length != selectedCount(cloudTags)">
				<div class="title-box">{t}Suggested tags from Ximdex CMS{/t}</div>
				<ul class="nube_tags">
					<li class="xim-tagsinput-taglist icon xim-tagsinput-type-#/namespaces[tag.IdNamespace].nemo/#" ng-repeat="tag in cloudTags" ng-click="addTag(tag)" ng-hide="tag.selected">
	                    <span class="tag-text">#/tag.Name/#</span>
<!-- 	                    <span class="amount right">#/tag.Total/#</span> -->
	                </li>
				</ul>
			</div>
			<ontologyBrowser />
		</div>
	</div>
	<fieldset class="buttons-form positioned_btn">
			<button class="button_main_action"
				xim-button
				xim-state="submitState"
				xim-label="'ui.dialog.confirmation.save' | xI18n"
				ng-click="saveTags(documentTags)"
				xim-disabled="!dirty">
			</button>
		</fieldset>
</form>
