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

<form method="POST" name="tags_form" class="setmetadata-tags_form" action="{$action_url}" 
	ng-controller="XTagsCtrl" 
	xim-document-tags='{$tags}'
	xim-cloud-tags='{$cloud_tags}'
	xim-namespaces='{$namespaces}'
	xim-node-id="{$id_node}" 
	ng-cloak>
	<div class="action_header">
		<h2>{t}Tag this node{/t}</h2>
		<fieldset class="buttons-form">
			{button label='Guardar' class='asoc enable_checks validate btn main_action'}
		</fieldset>
	</div>

	<div class="action_content">
		<div class="xim-tagsinput-container-list">
			<div class="xim-tagsinput-newtag  col2-3">
				<input type="text" class="xim-tagsinput-input" id="tag_input" placeholder="{t}Create new tags here{/t}..." ng-model="newTag.Name"/>
				<!-- <xim-select
					ng-model="newTag.type"
					xim-options="namespaces"
					xim-label-prop="nemo"
					xim-style-prop="nemo">
				</xim-select> -->
				<button type="button" class="btn-unlabel-rounded icon add-btn" ng-click="addNewTag()">{t}Add{/t}</button>
				<div>[[newTag.Name]]</div>
			</div>
	    </div>
		<div class="xim-tagsinput-container col2-3">
			<div class="title-box">{t}Document tags{/t}</div>
	   		<ul class="xim-tagsinput-list">
	   			<li class="xim-tagsinput-tag type" ng-repeat="tag in documentTags">
	   				<select class="hidden vertical collapsable ximdexInput icon button type-selector" name="type"
	   					ng-model="tag.type"
	   					ng-options="namespace as namespace.type for namespace in namespaces"
	   					></select>
					<span class="xim-tagsinput-text" data-tooltip="[[tag.Name]]">
					[[tag.Name]]
					</span>
<!-- 					<span class="amount">[[tag.Total]]</span> -->
					{* <a class="xim-tagsinput-tag-properties" href="#"> &infin; </a> *}
					<a class="xim-tagsinput-tag-remove icon" href="#" ng-click="removeTag($index)"> &times; </a>
				</li>
				<p ng-hide="documentTags.length">{t}There aren't any tags defined yet{/t}.</p>
	    	</ul>
	    </div>
		
		<div class="suggested col1-3">
			<xtags-suggested xim-on-select="addTag(tag)" xim-node-id="nodeId"></xtags-suggested>
			<div class="tagcloud xim-tagsinput-container-related" ng-show="cloudTags.length">
				<div class="title-box">{t}Suggested tags from Ximdex CMS{/t}</div>
				<ul class="nube_tags">
					<li class="xim-tagsinput-taglist icon custom" ng-repeat="tag in cloudTags" ng-click="addTag(tag)" ng-hide="tag.selected">
	                    <span class="tag-text">[[tag.Name]]</span>
<!-- 	                    <span class="amount right">[[tag.Total]]</span> -->
	                </li>
				</ul>
			</div>
			<ontologyBrowser />
		</div>
	</div>
</form>
