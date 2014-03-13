angular.module('ximdex.module.xtags')
    .directive('xtagsSuggested', ['$window', 'xTranslate', function ($window, xTranslate) {
        return {
            replace: true,
            scope: {
                nodeId: '=ximNodeId',
                selectCallback: '&ximOnSelect',
                filter: '&ximTagsFilter'
            },
            restrict: 'E',
            templateUrl : 'inc/js/angular/templates/xtagsSuggested.html',
            controller: ['$scope', '$element', '$attrs', '$http', 'xUrlHelper', function($scope, $element, $attrs, $http, xUrlHelper){   
            	
            	var url = xUrlHelper.baseUrl()+'/?mod=ximTAGS&action=setmetadata&method=getRelatedTagsFromContent';
            	
            	//Fetch suggested tags from backend
                $scope.loading=true;
                $http.get(url+'&nodeid='+$scope.nodeId).success(function(data){
            		if (data && data.status === 'ok') {
            			$scope.tags = [];
            			for (var set in data.semantic) {
        			        for (tag in data.semantic[set]) {
                                if(data.semantic[set].hasOwnProperty(tag)){
        			        	    data.semantic[set][tag].Name = tag;
                                    $scope.tags.push(data.semantic[set][tag]);
                                }
        			        }
        			    }
                        //TODO: Check for data.content responses
            		} else {
            			$scope.disconnected = true;
            		}
                    $scope.loading=false;
            	});
            	$scope.selectTag = function(tag){
            		$scope.selectCallback({tag:tag});
            	}
            }]
        }
    }]);
