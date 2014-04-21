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
angular.module('ximdex.main.controller')
    .controller('XUploaderCtrl', ['$scope', '$attrs', 'xUrlHelper', 'xCheck', function($scope, $attrs, xUrlHelper, xCheck){
    	
        var targetUrl = xUrlHelper.getAction({
            action: 'fileupload_common_multiple',
            type: 'common',
            method: 'uploadFile',
            id: $attrs.ximNodeId,
            options: {
                option: 0
            }
        });
        $scope.XC = xCheck;

        $scope.uploader = {};
    	
        $scope.fileAdded = function(event, file){
            file.isImage = (file.file.type.indexOf("image")==-1) ? false : true;
    	}

    	$scope.uploadFiles = function(flow) {
    		flow.opts.target = targetUrl;
    		flow.opts.query = function(file, chunk) {
                return {
                    meta: angular.toJson(file.meta), 
                    overwrite: file.nameExist || false,
                    ximFilename: file.ximFilename
                }
            };
    		flow.opts.testChunks = false;
            flow.opts.progressCallbacksInterval = 0;
    		flow.upload();
    	}
        
        $scope.fileError= function(file, jsonMessage) {
            var message = angular.fromJson(jsonMessage);
            if (message && message.msg) {
                file.errorMsg = message.msg;
            }  
        }

        $scope.uploadComplete= function() {
            console.log("COMPLETE", $scope.uploader);
            $scope.$emit('nodeModified', $attrs.ximNodeId);
        }

    }]);