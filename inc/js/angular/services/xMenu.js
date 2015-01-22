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
        var body = $document.find('body');

        destroyMenu = function() {

        }

        initMenu = function (options, nodes, callback){
            var menu = angular.element(
                '<xim-menu expanded="'+options.expanded+'" top="'+options.top+'" left="'+options.left+'"></xim-menu>');
            var hammerBody = Hammer(document.getElementsByTagName('body')[0]);
            hammerBody.off('tap');
            angular.element('div.xim-actions-menu').remove();

            scope = $rootScope.$new();
            var menuC = $compile(menu)(scope);
            angular.element('body').append(menuC);
            scope.options = options;
            scope.optionLabel = 'name';
            scope.optionClass = 'command';
            scope.select = function(result){
                destroyMenu();
                if (callback)
                    callback(result, nodes);
            }
            hammerBody.on('tap', function (ev) {
                if (ev.target.classList[0] != "xim-actions-dropdown") {
                    angular.element('div.xim-actions-menu').remove();
                }
                hammerBody.off('tap');
            });

            if (!scope.$$phase)
                scope.$digest();
            $timeout(function() {
                var finY, menuY, newtop, windowY;
                menuY = angular.element('div.xim-actions-menu').height();
                windowY = $window.innerHeight;
                finY = menuY + parseInt(scope.top) - $window.document.body.scrollTop;
                if (finY > windowY) {
                    newtop = parseInt(scope.top) - (finY - windowY) - (scope.expanded ? 10 : 0);
                    angular.element('div.xim-actions-menu').css({
                        top: newtop
                    });
                }
            }, 0);
        }

        return {
            open: function(options, nodes, callback) {
                initMenu(options, nodes, callback)
            },
            close: function() {

            }
        }
    }]);