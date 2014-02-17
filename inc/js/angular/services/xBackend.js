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
    .factory('xBackend', ['$http', '$rootScope', '$timeout', 'xUrlHelper', function($http, $rootScope, $timeout, xUrlHelper) {
        return {
            sendFormData: function(formData, params, callback){
                var actionUrl = xUrlHelper.getAction(params);
                if (actionUrl) {
                    $http({
                            method  : 'POST',
                            url     : actionUrl,
                            data    : $.param(formData),  // pass in data as strings
                            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                    }).success(function(data) {         
                            if (formData.IDParent || formData.id)
                                $rootScope.$broadcast('nodeModified', formData.IDParent || formData.id);
                            if (data)
                                callback(data);
                    });
                }  
            }, 
            subscribe: function(params, callback){
                var url = xUrlHelper.getAction(params);
                var timer = null;
                var etag = null
                var interval = 4000;
                var pollStep = function (interval) {
                    return Math.floor(interval/5);
                }
                var stopPolling = function(){
                    if (timer) {
                        $timeout.cancel(timer);
                    }
                }
                var requestCallback = function(data, status){
                    if (data && status == 200) {
                        callback(data);
                        if (data.etag) {
                            etag = data.etag;
                        }
                    }
                    if (etag) {
                        switch (status) {
                            case 200:
                                if (interval - pollStep(interval) >= 1000)
                                    interval -= pollStep(interval);
                                break;
                            case 304:
                                if (interval + pollStep(interval) <= 8000)
                                    interval += pollStep(interval);
                                break;
                        }
                    }
                    timer = $timeout(refresh, interval);
                }
                var refresh = function() {
                   
                    if (etag)    
                        var etagUrl = url+'&etag='+etag
                    $http({
                        method  : 'GET',
                        url     : etagUrl || url
                    }).success(function(data, status){         
                        requestCallback(data, status);
                    }).error(function(data, status){         
                        requestCallback(data, status);
                    });
                }
                
                if (url)
                    refresh();
                return {
                    refresh: function(){
                        stopPolling();
                        refresh();
                    },
                    unsubscribe: function() {
                        stopPolling(); 
                    }
                }   
            }
        }
    }]);