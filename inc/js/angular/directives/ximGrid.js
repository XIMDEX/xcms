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
    .directive('ximGrid', [function () {
        return {
            replace: true,
            restrict: 'E',
            scope: {
            	list: '=ximList',
                filterText: '=ximFilter',
                url: '=ximUrl',
                page: '=ximActualPage',
                pages: '=ximTotalPages',
                upPage: '=ximUpPage',
                downPage: '=ximDownPage',
                searching: '=ximSearching'
            },
            templateUrl : 'inc/js/angular/templates/ximGrid.html',
            controller: ['$scope', '$element', '$attrs', '$http', 'xUrlHelper',  function($scope, $element, $attrs, $http, xUrlHelper){   
    			if ($attrs.ximInitFields) {
    				$scope.fields = angular.fromJson($attrs.ximInitFields);
    			}
    			
    			$scope.selectedItems = [];

                $attrs.ximList=$scope.list;

                var url = xUrlHelper.baseUrl()+"/xmd/loadaction.php";
                $scope.lastpage;
                $scope.page=1;
                //$scope.pages=$scope.list.pages;
                $scope.searching=false;

                $scope.JSONtoParams = function (json){
                	var res={action:"browser3",
                                handler:"SQL",
                                method:"search",
                                output:"JSON"};
                	for (var i in json) {
				      	if(json.hasOwnProperty(i)){
					      	if(typeof json[i] != 'string' & isNaN(json[i])){
					      		for (var j in json[i]) {
					      			if(json[i].hasOwnProperty(j)){
								      	if(typeof json[i][j] != 'string' & isNaN(json[i][j])){
								      		for (var k in json[i][j]) {
								      			if(json[i][j].hasOwnProperty(k)){
									      			if(typeof json[i][j][k] == 'string' || !isNaN(json[i][j][k])){
											      		res['query['+i+']['+j+']['+k+']']=json[i][j][k];
											      	}
										      	}
										   	}
								      	}else{
								      		res['query['+i+']['+j+']']=json[i][j];
								      	}
								    }
							   	}
					      	}else{
					      		res['query['+i+']']=json[i];
					      	}
					    }
				   	}
				   	
				   	return res;
                }

                $scope.updateGrid = function(page) {
                    
                    $scope.searching=true;
                    $scope.showFieldsSelector=false;
                    $scope.list.query.page=$scope.page;
                    $http(
                        {
                            url:url,
                            method:'POST',
                            params:$scope.JSONtoParams($scope.list.query)
                        }).
                        success(function(data, status, headers, config) {
                          $scope.filterText="";
                          $attrs.ximFilter="";
                          $scope.list=data;
                          $scope.searching=false;
                        }).
                        error(function(data, status, headers, config) {
                            if(page){
                                $scope.page=$scope.lastpage;
                            }
                            $scope.searching=false;
                        });
                }

                $scope.upPage = function(){
                    if(!$scope.searching & $scope.page<$scope.list.pages){
                        $scope.lastpage=$scope.page;
                        $scope.page++;
                        $scope.updateGrid(true);
                    }
                };

                $scope.downPage = function(){
                    if(!$scope.searching & $scope.page>1){
                        $scope.lastpage=$scope.page;
                        $scope.page--;
                        $scope.updateGrid(true);
                    }
                };

                $attrs.ximUpPage=$scope.upPage;
                $attrs.ximDownPage=$scope.downPage;

    			$scope.selectItem = function(item, event) {
					event.preventDefault();
                    if(!$scope.searcing){
	                    if (event.ctrlKey) {
	    					$scope.selectedItems.push(item.nodeid);	
	    				} else {
	    					$scope.selectedItems = [item.nodeid];
	    				}
	    			}
    				return false;
    			}

    			$scope.isSelected = function(itemId) {
    				return ($scope.selectedItems.indexOf(itemId) < 0)? false: true ;
    			}

    			$scope.contextmenu = function(item, event, inline){
    				if(!$scope.searching){
	    				$scope.selectItem(item, event);
						if (!event.ctrlKey) {
							$scope.$emit('openActionsMenu', {
								event: event,
								nodes: $scope.selectedItems,
								inline: inline
							});
						}
					}
    			}

                $scope.sort = function(field){
                    if(!$scope.searching){
                        if($scope.list.query.sorts[0].field!=field.target){
                            $scope.list.query.sorts[0].field=field.target; 
                            $scope.list.query.sorts[0].order="asc";
                            $scope.page=1;
                        }else{
                            if($scope.list.query.sorts[0].order=="asc"){
                                $scope.list.query.sorts[0].order="desc";
                            }else{
                                $scope.list.query.sorts[0].order="asc";
                            }
                        }
                        $attrs.ximList.query=$scope.list.query;
                        $scope.updateGrid(false);
                    }
                };

    			$scope.$watch('fields', function(){
    				$scope.$broadcast('ui-refresh');
    			}, true);
                $scope.$watch('filterText', function(){
                    $scope.$broadcast('ui-refresh');
                });
                $scope.$on('toggleFieldsSelector', function(event){
                	if(!$scope.searching){
                    	$scope.showFieldsSelector = !$scope.showFieldsSelector;
                	}
                });
            }]
        }
    }]);