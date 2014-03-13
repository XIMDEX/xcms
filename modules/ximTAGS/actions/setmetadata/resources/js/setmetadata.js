 if (angular.module('ximdex').notRegistred('XTagsCtrl')){
    angular.module('ximdex')
        .controllerProvider.register('XTagsCtrl', ['$scope', '$attrs', 'xBackend', 'xTranslate', '$window', '$http', 'xUrlHelper', '$timeout', function($scope, $attrs, xBackend, xTranslate, $window, $http, xUrlHelper, $timeout){
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
                $http.post($attrs.action, {tags:tags})
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
                if (event.keyCode == 13)
                    $scope.addNewTag();
            }
   
        }]);
    angular.module('ximdex').registerItem('XTagsCtrl');
    
    angular.module('ximdex')
        .compileProvider.directive('xtagsSuggested', ['$window', 'xTranslate', function ($window, xTranslate) {
            return {
                replace: true,
                scope: {
                    nodeId: '=ximNodeId',
                    selectCallback: '&ximOnSelect',
                    filter: '&ximTagsFilter'
                },
                restrict: 'E',
                templateUrl : 'modules/ximTAGS/actions/setmetadata/template/Angular/xtagsSuggested.html',
                controller: ['$scope', '$element', '$attrs', '$http', 'xUrlHelper', function($scope, $element, $attrs, $http, xUrlHelper){   
                	
                	var url = xUrlHelper.baseUrl()+'/?mod=ximTAGS&action=setmetadata&method=getRelatedTagsFromContent';
                	
                	//Fetch suggested tags from backend
                    $http.get(url+'&nodeid='+$scope.nodeId).success(function(data){
                		if (data && data.status === 'ok') {
                			$scope.tags = []
                			for (var set in data.semantic) {
            			        for (tag in data.semantic[set]) {
            			        	data.semantic[set][tag].Name = tag;
            			        	$scope.tags.push(data.semantic[set][tag]);
            			        }
            			    }
                            //TODO: Check for data.content responses
                		} else {
                			$scope.disconnected = true;
                		}
                	});
                	$scope.selectTag = function(tag){
                		$scope.selectCallback({tag:tag});
                	}
                }]
            }
        }]);
    angular.module('ximdex').registerItem('xtagsSuggested');
    
    angular.module('ximdex')
        .compileProvider.directive('ximOntologyBrowser', ['$window', function ($window) {
            return {
                replace: true,
                scope: {
                    selectedItems: '=ximSelectedList',
                    onSelect: '&ximOnSelect',
                    onUnSelect: '&ximOnUnSelect'
                },
                restrict: 'A',
                link: function (scope, element, attrs) {
                    selected = [];
                    for (var i = 0, len = scope.selectedItems.length; i < len; i++) {
                        selected.push(scope.selectedItems[i].Name);
                    }
                    $window.jQuery(element).ontologywidget({
            			selected: selected,
                        onSelect: function(el) {
            				scope.$apply(function(){
            					scope.onSelect({ontology:el});
            				});
            			},
            			offSelect: function(name) {
            				scope.$apply(function(){
            					scope.onUnSelect({ontology:{name:name}});
            				});
            			}
            		});    
	            }
            }
        }]);
    angular.module('ximdex').registerItem('ximOntologyBrowser');
}
//Start angular compile and binding
X.actionLoaded(function(event, fn, params) {
    X.angularTools.initView(params.context, params.tabId);
});

