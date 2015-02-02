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
    .directive('ximUploader', function () {
        return {
            replace: true,
            restrict: 'E',
            templateUrl : 'inc/js/angular/templates/ximUploader.html',
            controller: ['$scope', '$element', '$attrs', 'xUrlHelper', 'xTranslate', function($scope, $element, $attrs, xUrlHelper, xTranslate){   
            	
            	$scope.nodeId = $attrs.ximNodeId;

            	$scope.targetUrl = xUrlHelper.getAction({
    	            action: 'fileupload_common_multiple',
    	            type: 'common',
    	            method: 'uploadFlowFile',
    	            id: $scope.nodeId,
    	        });

                $scope.options = angular.fromJson($attrs.ximUploaderOptions); 
    	    	if ($scope.options.metaFields) {
                    $scope.globalMeta = {};
                }

    	    	$scope.$on('flow::fileAdded', function (event, $flow, file) {
                    if ($scope.options.allowedExtensions && $scope.options.allowedExtensions.indexOf(file.getExtension()) === -1) {
                        //Prevent adding file if is not accepted
                        event.preventDefault();
                    };
                    file.isImage = file.file.type.indexOf("image") !== -1;
    	    	});

    	    	$scope.$on('flow::fileError', function (event, $flow, file, jsonMessage) {
    	    		var message = (jsonMessage) ? angular.fromJson(jsonMessage) : null;
                    if (message && message.msg) {
                        file.errorMsg = message.msg;
                    } 
    	    	});

    	    	$scope.$on('flow::fileSuccess', function (event, $flow, file, jsonMessage) {
    	    		var message = (jsonMessage) ? angular.fromJson(jsonMessage) : null;
                    if (message && message.msg) {
                        file.successMsg = message.msg;
                    }  	
    	    	});

    	    	$scope.$on('flow::complete', function () {
                    $scope.$emit('nodemodified', $scope.nodeId);
    	    	});

    	    	$scope.uploadFiles = function() {
                    if ($scope.allowUpload()) {
                		$scope.$flow.opts.target = $scope.targetUrl;
                        $scope.$flow.opts.query = function(file, chunk) {
                            var meta = {}
                            if (!!$scope.globalMeta) meta = $scope.globalMeta;
                            if (!!file.meta) angular.extend(meta, file.meta);
                            var query = {
                                meta: angular.toJson(meta),
                                overwrite: file.overwrite || false,
                                ximFilename: file.ximFilename,
                                type: $scope.options.type || null
                            };
                            return query;
        	            };
        	    		$scope.$flow.opts.testChunks = false;
        	            $scope.$flow.opts.progressCallbacksInterval = 0;
        	    		$scope.$flow.upload();
                    }
    	    	}

                $scope.allowUpload = function(){
                    if (!$scope.totalFiles() > 0 || $scope.invalidFiles() === $scope.totalFiles()) {
                        return false;
                    } else if(!!$scope.options.metaFields && $scope.options.globalMetaOnly){
                        for (field in $scope.options.metaFields) {
                            if ($scope.options.metaFields[field].required) {
                                if (!$scope.globalMeta || !$scope.globalMeta[field]) {
                                    return false
                                }
                            }
                        }
                    }
                    if ($scope.invalidFiles() === $scope.$flow.files.length) return false;
                    return true;  
                }

                $scope.uploadButtonLabel = function(){
                    //TODO: Adapt xTranslate to deal with replacement arguments
                    var label = xTranslate('widgets.fileUploader.upload')+' '+( $scope.totalFiles() - $scope.invalidFiles())+' '+xTranslate('widgets.fileUploader.files');
                    return label;
                }
                
                $scope.invalidFiles = function(){
                    var count = 0;
                    for (var i = $scope.$flow.files.length - 1; i >= 0; i--) {
                        if ($scope.$flow.files[i].invalid) {
                            count ++
                        }
                    };
                    return count;
                }
                $scope.totalFiles = function(){
                    var count = 0;
                    for (var i = $scope.$flow.files.length - 1; i >= 0; i--) {
                        if (!$scope.$flow.files[i].isComplete() && !$scope.$flow.files[i].error) {
                            count ++
                        }
                    };
                    return count;    
                }
            }]
        }
    });