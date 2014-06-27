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
    .factory('xBrowserWindow', ['$window', '$rootScope', 'xTranslate', 'xMenu', function($window, $rootScope, xTranslate, xMenu) { 
        
        var openAction = function(action, nodes) {
            $window.jQuery(':ui-browserwindow').browserwindow('openAction', action, nodes);     
        }

        $rootScope.$on('openAction', function(event, action){
            openAction({
                bulk: 0,
                callback: 'callAction',
                command: action.command,
                icon: null,
                module: action.module,
                name: xTranslate(action.name),
            }, action.nodeid);
        });
        
        $rootScope.$on('openActionsMenu', function(event, params){
            X.testEvent = params.event
            var nodes = [];
            for (var i = params.nodes.length - 1; i >= 0; i--) {
                var node = {
                    nodeid: {
                        value: params.nodes[i]
                    }
                }
                nodes.push(node);
            };
            $window.jQuery(':ui-browserwindow').browserwindow('getActions', {
                nodes: nodes,
                cb: function(options) {
                    options.inline = params.inline;
                    options.result = openAction;
                    xMenu.legacyOpen(options);
                },
                data: nodes[0],
                selector: {},
                menuPos: {x:params.event.clientX, y:params.event.clientY-10}
            });

            console.log("open actions menu", params, $window.jQuery(':ui-browserwindow'))
        });

        
    }]);
