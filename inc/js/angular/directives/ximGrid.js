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
angular.module('ximdex.common.directive')
    .directive('ximGrid', function () {
        return {
            replace: true,
            restrict: 'E',
            scope: {
            	list: '=ximList'
            },
            templateUrl : 'inc/js/angular/templates/ximGrid.html',
            controller: ['$scope', '$element', '$attrs',  function($scope, $element, $attrs){   
    			if ($attrs.ximInitFields) {
    				$scope.fields = angular.fromJson($attrs.ximInitFields);
    			}
    			$scope.sortByField = 'modification';
    			$scope.reverse = true;
    			$scope.selectedItems = []

    			$scope.selectItem = function(item, event) {
					event.preventDefault();
    				if (event.ctrlKey) {
    					$scope.selectedItems.push(item.nodeid);	
    				} else {
    					$scope.selectedItems = [item.nodeid];
    				}
    				return false;
    			}
    			$scope.isSelected = function(itemId) {
    				return ($scope.selectedItems.indexOf(itemId) < 0)? false: true ;
    			}

    			$scope.contextmenu = function(item, event, inline){
    				$scope.selectItem(item, event);
					if (!event.ctrlKey) {
						$scope.$emit('openActionsMenu', {
							event: event,
							nodes: $scope.selectedItems,
							inline: inline
						});
					}
    			}

    			$scope.$watch('fields', function(){
    				$scope.$broadcast('ui-refresh');
    			}, true);
                $scope.$watch('filterText', function(){
                    $scope.$broadcast('ui-refresh');
                });
            }]
        }
    });