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
    .directive('ximButton', ['$window', function ($window) {
        return {
            replace: true,
            scope: {
                state: '=ximState',
                loading: '=ximLoading', 
                disabled: '=ximDisabled',
                label: '=ximLabel',
                progress: '=ximProgress'
            },
            restrict: 'A',
            template: '<button type="button" class="button ladda-button" data-style="slide-up" data-size="xs" ng-disabled="flagDisabled">'+
                    '<span class="ladda-label">#/label/#</span>'+
                '</button>',
            link: function postLink(scope, element, attrs) {
                var loader = $window.Ladda.create(element[0]);
                scope.$watch('state', function(newState, oldState){
                    switch (newState) {
                        case 'submitting':
                        case 'pending':
                            loader.start();
                            break;
                        case 'resolved':
                        case 'success':
                        case 'error':
                            loader.stop();
                            break;
                    }
                });
                scope.$watch('loading', function(newState, oldState){
                    if (newState != oldState) {
                        (newState) ? loader.start() : loader.stop();
                    }
                });
                scope.$watch('progress', function(newValue, oldValue){
                    if (oldValue != newValue)
                        loader.setProgress(newValue)
                });
                //Not really needed just a hack to fix angular not properly binding disabled property (Angular)
                scope.flagDisabled = false;
                scope.$watch('disabled', function(newValue, oldValue){
                    scope.flagDisabled = newValue;
                });
            }
        }
    }]);