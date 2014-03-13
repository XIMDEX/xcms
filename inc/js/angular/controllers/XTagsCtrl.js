angular.module('ximdex.module.xtags')
    .controller('XTagsCtrl', ['$scope', '$attrs', 'xBackend', 'xTranslate', '$window', '$http', 'xUrlHelper', '$timeout', function($scope, $attrs, xBackend, xTranslate, $window, $http, xUrlHelper, $timeout){
    	$scope.documentTags = [];
    	$scope.cloudTags = [];
        $scope.namespaces = {};
    	$scope.nodeId = $attrs.ximNodeId;
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
            if (!$scope.tagExistInArray(tag, $scope.documentTags)) {	
                if (tag.isSemantic) {
                    for (var key in $scope.namespaces){
                        if ($scope.namespaces[key].nemo === tag.type) {
                            tag.IdNamespace = $scope.namespaces[key].id;
                            break;
                        }
                    }
                }
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
            event.preventDefault();
            if (event.keyCode == 13)
                $scope.addNewTag();
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