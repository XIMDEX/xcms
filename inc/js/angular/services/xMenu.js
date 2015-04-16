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

angular.module('ximdex.common.service')
    .factory('xMenu', ['$timeout', '$window', '$document', '$compile', '$rootScope', '$templateCache', function($timeout, $window, $document, $compile, $rootScope, $templateCache) {

        var scope = {};

        destroyMenu = function() {
            if(scope.$destroy !== undefined){
                scope.$destroy();
            }
            var oldMenu = angular.element('div.xim-actions-menu');
            if(oldMenu.length>0){
                oldMenu.remove();
            }
        }

        initMenu = function (options, nodes, callback){
            var hammerBody = Hammer(document.getElementsByTagName('body')[0]);
            hammerBody.off('tap');
            destroyMenu();

            var menu = angular.element(
                '<xim-menu></xim-menu>');
            scope = $rootScope.$new();
            scope.options = options;
            scope.optionLabel = 'name';
            scope.optionClass = 'command';
            scope.top = options.top;
            scope.left = options.left;
            scope.select = function(result){
                if (callback)
                    callback(result, nodes);
                destroyMenu();
            }
            var menuC = $compile(menu)(scope);
            angular.element('body').append(menuC);


            hammerBody.on('tap', function (ev) {
                var e = angular.element(ev.target);
                if (!e.hasClass("button-container-list")) {
                    destroyMenu();
                }
                hammerBody.off('tap');
            });

            if (!scope.$$phase)
                scope.$digest();
        }

        return {
            open: function(options, nodes, callback) {
                initMenu(options, nodes, callback);
            },
            close: function() {
                destroyMenu();
            }
        }
    }]);