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
angular.module('ximdex.common.service')//Abstraction for server communications. TODO: Expose to client a REST like interface
    .factory('xUrlHelper', ['$window', function($window) {
        return {
            restUrl: function() {
                return $window.X.restUrl;
            },
            baseUrl: function() {
                return $window.X.baseUrl;
            },
            getAction: function(params){
                var timestamp = new Date().getTime();
                var actionUrl = this.restUrl()+'?noCacheVar='+timestamp;
                if(params.action){
                    actionUrl+='&action='+params.action;
                }
                if(params.method){
                    actionUrl+='&method='+params.method;
                }
                if (params.id) {
                    actionUrl += '&nodeid=' + params.id + '&nodes[]=' + params.id;
                }else if (params.nodes){
                    var str = "";
                    for(var i = 0; i<params.nodes.length; i++){
                        str += '&nodes[]=' + params.nodes[i].nodeid
                    }
                    actionUrl += str;
                } else if (params.IDParent) {
                    actionUrl+='&nodeid='+params.IDParent+'&nodes[]='+params.IDParent;
                }
                if (params.module){
                    actionUrl+='&mod='+params.module;
                }
                if (params.options) {
                    if (typeof params.options == 'string' || params.options instanceof String)
                        actionUrl += "&" + params.options;
                    else
                        actionUrl = this.parametrize(actionUrl, params.options[0]);
                }

                return actionUrl;
            },
            parametrize: function(url, params) {
                angular.element.each(params, function(key, value){
                    url+= '&'+key+'='+value;
                });
                return url;
            }
        }
    }]);
