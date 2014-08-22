/**
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
 */
angular.module('ximdex.module.xtags')
    .controller('XTagsCtrl', ['$scope', '$attrs', 'xBackend', 'xTranslate', '$window', '$http', 'xUrlHelper', '$timeout', function($scope, $attrs, xBackend, xTranslate, $window, $http, xUrlHelper, $timeout){
    	$scope.documentTags = [];
    	$scope.cloudTags = [];
        $scope.namespaces = {};
        $scope.nodeId = $attrs.ximNodeId;
    	$scope.isEditor = $attrs.ximIsEditor;
        $scope.submitLabel = xTranslate('common.save');
        
        $scope.tagExistInArray = function(tag, array) {
            for (var i = 0, len = array.length; i < len; i++){
                if (tag.Name == array[i].Name && tag.IdNamespace == array[i].IdNamespace) {
                    return array[i];
                }
            }
            return false;
        }

        $scope.hideSelected = function(selectedItems, array) {
            for (var i = 0, len = array.length; i < len; i++){
                var docTag = $scope.tagExistInArray(array[i], selectedItems);
                if (docTag) {
                    array[i] = docTag;
                    array[i].selected = true;
                }
            }   
        }

        $scope.selectedCount = function(array) {
            var count = 0;
            for (var i = 0, len = array.length; i < len; i++)
                if (array[i].selected)
                    count++;
            return count;    
        }

        $scope.getNamespaceId = function(nemo) {
            for (namespace in $scope.namespaces) {
                if (nemo == $scope.namespaces[namespace].nemo) {
                    return namespace;
                }
            }
            return false;
        }

        $scope.newTag = {IdNamespace: $scope.getNamespaceId('custom')};

        //Takes inital tags data form json attributes rendered by smarty
        if ($attrs.ximDocumentTags)
            $scope.documentTags = angular.fromJson($attrs.ximDocumentTags);
        
        if ($attrs.ximCloudTags) {
            $scope.cloudTags = angular.fromJson($attrs.ximCloudTags);
            $scope.hideSelected($scope.documentTags, $scope.cloudTags);
        }
        
        if ($attrs.ximNamespaces) {
            var namespaces = angular.fromJson($attrs.ximNamespaces);
            for (var i = 0, len = namespaces.length; i < len; i++){
                $scope.namespaces[namespaces[i].id] = namespaces[i];
            }
        }

        $scope.addTag = function(tag){
            if (tag.isSemantic)
                tag.IdNamespace = $scope.getNamespaceId(tag.type);
            if (!$scope.tagExistInArray(tag, $scope.documentTags) && tag.Name) {	  
                $scope.dirty = true;
                tag.selected = true;
            	$scope.documentTags.push(tag);
            }
        }

        $scope.removeTag = function(index) {
        	$scope.dirty = true;
            $scope.documentTags[index].selected = false;
        	$scope.documentTags.splice(index, 1);
        }

        $scope.addNewTag = function() {
        	$scope.addTag(angular.copy($scope.newTag));
        	$scope.newTag = {IdNamespace: $scope.getNamespaceId('custom')};
        }

        $scope.addOntology = function(ontology){
            $scope.addTag({
                Name: ontology.name, 
                structured: true,
                IdNamespace: $scope.getNamespaceId('structured')
            });
        }

        $scope.removeOntology = function(ontology) {
            for (var i = 0, len = $scope.documentTags.length; i < len; i++){
                if ($scope.documentTags[i].Name === ontology.name) {
        			$scope.removeTag(i);	
                    break; 
                }
                
        	}
        }

        $scope.saveTags = function(tags) {
            $scope.submitState = 'submitting'
            var url = xUrlHelper.getAction({
                id: $scope.nodeId,
                module: 'ximTAGS',
                action: 'setmetadata',
                method: 'save_metadata'
            });
            $http.post(url, {tags:tags})
                .success(function(data){
                    $scope.submitState = 'success'
                    $scope.dirty = false;
                    $scope.submitMessages = data.messages;
                    $timeout(function(){
                        $scope.submitMessages = null;
                    }, 4000);
                    $scope.$emit('nodemodified', $scope.nodeId);
                })
                .error(function(data){
                    $scope.submitState = 'error'
                    $scope.submitMessages = data.messages;
                    $timeout(function(){
                        $scope.submitMessages = null;
                    }, 4000);
                });
        }

        $scope.keyPress = function (event) {
            if (event.keyCode == 13) $scope.addNewTag();
        }

        $scope.focus = function(event){
            if ($scope.isEditor && parseInt($scope.newTag.IdNamespace) == 2){
            $window.jQuery(".ontology-browser").ontologywidget({
                selected:[],
                onSelect: function(el){
                $scope.newTag.Name = el.name;
                $scope.$apply();
                }
            });
            $window.jQuery(".ontology-browser").ontologywidget("showTree");
            $window.jQuery(".ontology-browser").removeClass("hidden");
            $window.jQuery(".ontology-browser .textViewer").addClass("hidden");
            $window.jQuery(".ontology-browser .treeViewer").removeClass("hidden");
            }
        }   

        //Hacks to deal with mixed enviroment
        //Semantics tags added from the xeditor
        $window.jQuery(document).on('addTag', function(event, tag){
            $scope.$apply(function(){
                $scope.addTag(tag);
            });
        });
        $window.jQuery(document).on('saveTags', function(){
            $scope.$apply(function(){
                $scope.saveTags($scope.documentTags);
            }); 
        });

    }]);