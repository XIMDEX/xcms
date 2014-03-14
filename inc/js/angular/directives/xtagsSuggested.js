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
