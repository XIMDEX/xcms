 if (angular.module('ximdex').notRegistred('XTagsCtrl')){
    angular.module('ximdex')
        .controllerProvider.register('XTagsCtrl', ['$scope', '$attrs', 'xBackend', 'xTranslate', '$window', '$http', 'xUrlHelper', function($scope, $attrs, xBackend, xTranslate, $window, $http, xUrlHelper){
        	$scope.documentTags = [];
        	$scope.cloudTags = [];
            $scope.namespaces = {};
        	$scope.nodeId = $attrs.ximNodeId;
            $scope.submitLabel = xTranslate('common.save');
            $scope.newTag = {IdNamespace: '1'};
        	
            if ($attrs.ximDocumentTags)
                $scope.documentTags = angular.fromJson($attrs.ximDocumentTags);
            
            if ($attrs.ximCloudTags)
                $scope.cloudTags = angular.fromJson($attrs.ximCloudTags);
            
            if ($attrs.ximNamespaces)
                var namespaces = angular.fromJson($attrs.ximNamespaces);
                for (i = 0, len = namespaces.length; i < len; i++){
                    $scope.namespaces[namespaces[i].id] = namespaces[i];
                }

            $scope.addTag = function(tag){
            	if (tag.isSemantic) {
                    for (key in $scope.namespaces){
                        if ($scope.namespaces[key].nemo === tag.type) {
                            tag.IdNamespace = $scope.namespaces[key].id;
                            break;
                        }
                    }
                }
                tag.selected = true;
            	$scope.documentTags.push(tag);
            }

            $scope.removeTag = function(index) {
            	$scope.documentTags[index].selected = false;
            	$scope.documentTags.splice(index, 1);
            }

            $scope.addNewTag = function() {
            	$scope.addTag(angular.copy($scope.newTag));
            	$scope.newTag = {IdNamespace: '1'};
            }

            $scope.addOntology = function(ontology){
                $scope.addTag({
                    Name: ontology.name, 
                    structured: true,
                    IdNamespace: '2'
                });
            }

            $scope.removeOntology = function(ontology) {
            	for (var i=0; i < $scope.documentTags.lentgh; i++) {
            		if ($scope.documentTags[i].Name === ontology.name) {
            			$scope.removeTag(i);	
                        break; 
                    }
                    
            	}
            }
            $scope.saveTags = function(tags) {
                console.log($attrs.action, tags);
                $http.post($attrs.action, {tags:tags})
                    .success(function(data){
                        console.log("success", data);
                    })
                    .error(function(data){
                        console.log("error", data);
                    });
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
                	
                	var url = xUrlHelper.baseUrl()+'?mod=ximTAGS&action=setmetadata&method=getRelatedTagsFromContent';
                	
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

