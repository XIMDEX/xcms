###
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
###
 
if angular.module('ximdex').notRegistred('addSectionCtrl')
    angular.module('ximdex')
        .controllerProvider.register 'addSectionCtrl', ['$scope', '$http', 'xUrlHelper', 
        ($scope, $http, xUrlHelper) ->
      
            $scope.sectionTypeOptions = [];
            $scope.sectionTypeSelected = null;
      
            $scope.languageOptions = [];
            $scope.languageOptionsSelected = null;

            $scope.subfolders = [];
            $scope.subfoldersSelected = null;

            #  ----------------------------------------------

            #  Retrieve options to build the form
            $scope.init = (params) ->
                urlParams = 
                    action: params.action.command
                    id: params.nodes[0]
                    module: params.action.module
                    method: 'getSectionInfo'
                url = xUrlHelper.getAction(urlParams);
                
                $http.get(url).success((data) ->
                    $scope.sectionTypeOptions = data.sectionTypeOptions
                    $scope.sectionTypeSelected = $scope.sectionTypeOptions[0]
                    $scope.languageOptions = data.languageOptions
                    $scope.languageOptionsSelected = $scope.languageOptions[0]
                    $scope.subfolders = data.subfolders
                    $scope.changeSubfolders()
                    return
                )
                return

            # refresh subfolder list according with selected nodetype
            $scope.changeSubfolders = () ->
                index = $scope.sectionTypeSelected.value
                $scope.subfoldersSelected = $scope.subfolders[index]
                return
            
            return
        
    ]
    angular.module('ximdex').registerItem('addSectionCtrl');
