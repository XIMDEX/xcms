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
    .directive('ximSelect', ['$window', function ($window) {
        return {
            require: 'ng-model',
            scope:{
                options: '=ximOptions',
                selProp: '@ximSelProp',
                styleProp: '@ximStyleProp',
                labelProp: '@ximLabelProp',
                disabled: '=ximDisabled'
            },
            templateUrl: 'inc/js/angular/templates/ximSelect.html',
            restrict: 'E',
            replace: true,
            link: function (scope, element, attrs, ctrl) {
                
                var getOption = function(key, value) {
                    if( Object.prototype.toString.call(scope.options) === '[object Array]' ) {
                        for (i = 0, len = scope.options.length; i < len; i++){
                            if (scope.options[i][key] === value) {
                                return scope.options[i];
                            }
                        }
                    } else {
                        for (option in scope.options){
                            if (scope.options[option][key] == value) {
                                return scope.options[option];
                            }
                        }   
                    } 
                }

                var setSelected = function(key, value, call) {
                    if (key) {
                        scope.selectedOption = getOption(key, value);
                    } else {
                        scope.selectedOption = value;   
                    }
                }

                scope.selectOption =  function(option) {
                    if (scope.selProp) {
                        ctrl.$setViewValue(option[scope.selProp]);
                    } else {
                        ctrl.$setViewValue(option);   
                    }
                    scope.selectedOption = option;
                };
                 
                // model -> view
                ctrl.$render = function() {
                    setSelected(scope.selProp, ctrl.$viewValue);
                };
                // load init select value
                setSelected(scope.selProp, ctrl.$viewValue, true);
            }
        }
    }]);