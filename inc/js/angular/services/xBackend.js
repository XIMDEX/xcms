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
    .factory('xBackend', ['$http', '$rootScope', 'xUrlHelper', function($http, $rootScope, xUrlHelper) {
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
            }
        }
    }]);