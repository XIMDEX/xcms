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

if (angular.module('ximdex').notRegistred('MetadataCtrl')) {
    angular.module('ximdex')
        .controllerProvider.register('MetadataCtrl', ['$scope', '$attrs', 'xBackend', 'xTranslate', '$timeout', function($scope, $attrs, xBackend, xTranslate, $timeout){
            
            $scope.t = xTranslate;
            $scope.languages = angular.fromJson($attrs.ximLanguages);
            $scope.defaultLanguage = $attrs.ximDefaultlanguage;
            $scope.method = $attrs.ximMethod;
            // Pass this string to i18n files
            $scope.submitLabel = "Save";

            $scope.update = function() {
                var formData = {'nodeid': $scope.nodeId, 'langid': $scope.defaultLanguage};
                    xBackend.sendFormData(formData, {action: $attrs.ximAction, method: 'getDocumentVersion', id: $scope.nodeId}, function(data){
                        if (data) {
                            $scope.nodeversion = data.version;
                        }
                    });
            }

            $scope.submitForm = function(form){
                if (form.$valid) {
                    var formData = {'languages_metadata': angular.copy($scope.languages_metadata)};
                    $scope.submitStatus = 'submitting';
                    xBackend.sendFormData(formData, {action: $attrs.ximAction, method: $scope.method, id: $scope.nodeId}, function(data){
                        if (data && data.metadata) {
                            form.$setPristine();
                        }
                        if (data && data.messages) {
                            $scope.submitStatus = 'success';
                            $scope.submitMessages = data.messages;
                            $timeout(function(){
                                $scope.submitMessages = null;
                            }, 4000);
                        }
                    });
                }
            }

        }]);
    angular.module('ximdex').registerItem('MetadataCtrl');
}

//Start angular compile and binding
X.actionLoaded(function(event, fn, params) {
    X.angularTools.initView(params.context, params.tabId);    
});
