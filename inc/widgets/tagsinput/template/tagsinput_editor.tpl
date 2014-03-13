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

<div class="kupu-toolbox-container" 
	ng-controller="XTagsCtrl"
	xim-document-tags='{$tags}'
	xim-namespaces='{$namespaces}'
	xim-node-id='{$_enviroment["id_node"]}'>
	<h3 id="kupu-toolbox-tags-header" style="-moz-user-select: none"
		class="kupu-toolbox-heading kupu-toolbox-heading-closed ui-draggable">
		<button i18n:attributes="title" title="Show/hide tags"
			id="kupu-toolbox-tags-button"
			class="kupu-floatingtoolbox-button kupu-toolbox-tags-button"
			type="button">&nbsp;</button>
			Tags
	</h3>

	<div class="xim-tagsinput-container editor_tags" id="{$id}"  style="display:none">
	    <div class="xim-tagsinput-container-list ">
		    <ul class="xim-tagsinput-list" style="clear:both;">
  			<li class="xim-tagsinput-newtag">
		        	<xim-select class="tag-type btn-rounded"
						ng-model="newTag.IdNamespace"
						xim-options="namespaces"
						xim-label-prop="type"
						xim-style-prop="nemo"
						xim-sel-prop="id"
						ng-init="newTag.IdNamespace = namespaces['1'].id">
					</xim-select>
					<input type="text" class="xim-tagsinput-input editor_input_tags" id="tag_input" placeholder="{t}Create new tags here{/t}..." ng-model="newTag.Name" ng-class="{literal}{error: tagExistInArray(newTag, documentTags)}{/literal}" ng-keyup="keyPress($event)"/>
					<button type="button" class="btn-unlabel-rounded icon add-btn" ng-click="addNewTag()" ng-disabled="tagExistInArray(newTag, documentTags)">{t}Add{/t}</button>
		     	</li>		    	
				<div class="tags_container">
					<li class="xim-tagsinput-tag icon xim-tagsinput-type-#/namespaces[tag.IdNamespace].nemo/#" ng-repeat="tag in documentTags">
						<span class="xim-tagsinput-text" data-tooltip="#/namespaces[tag.IdNamespace].uri/#">
						#/tag.Name/#
						</span>
							<a ng-href="#/namespaces[tag.IdNamespace].uri/#" class="ontology_link">#/namespaces[tag.IdNamespace].type/#</a>
						<a class="xim-tagsinput-tag-remove icon" href="#" ng-click="removeTag($index)"> &times; </a>
					</li>
				</div>
		      
		    </ul>
		</div>
	</div>
    
 
	 <!-- <div class="xim-tagsinput-container-related">
	  <strong>Relativos:</strong>
     <ul class="xim-tagsinput-list-related" style="clear:both;"></ul>
    </div> 
       
    <div class="xim-tagsinput-container-suggested">
     <strong>Sugeridos:</strong>
     <ul class="xim-tagsinput-list-suggested" style="clear:both;"></ul>
    </div> -->
</div>
<!--  </div> -->
