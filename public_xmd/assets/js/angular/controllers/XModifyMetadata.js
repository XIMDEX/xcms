/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

angular.module("ximdex.main.controller").controller('XModifyMetadata', ['$scope', '$http', 'xUrlHelper', '$window', '$filter', 'xDialog', 
  	function($scope, $http, xUrlHelper, $window, $filter, xDialog)
  	{
	    $scope.init = function()
	    {
	    	$scope.removed = [];
	    	$scope.metadataList = $scope.metadataList.map(function(value) {
	    		var result = value;
	    		result['key'] = value.name;
	    		return result;
	    	});
	    };
	    
	    $scope.create = function()
	    {
	    	var name = $scope.name;
	    	var type = $scope.type;
	    	if (! name || ! type) {
	    		$scope.showError('Name and type are required');
	    		return false;
	    	}
	    	var defaultValue = $scope.defaultValue;
	    	var url = xUrlHelper.getAction({
	    		action: "createmetadata",
	    		method: "add"
	    	});
	    	var postData = {name: name, defaultValue: defaultValue, type: type};
	    	return $http.post(url, postData)
	    		.success(function(data, status, headers, config) {
					if (data.result === "ok") {
						postData['idMetadata'] = data["id"];
						postData['key'] = name;
						$scope.metadataList.push(postData);
						$scope.name = $scope.defaultValue = $scope.type = '';
						$scope.showMessages(['Metadata ' + name + ' has been created']);
						return true;
					}
					if (data.error) {
						$scope.showError(data.error);
					}
					return false;
	    		})
	    		.error(function(data, status, headers, config) {});
	    };
	    
	    $scope.remove = function(id)
	    {
	    	if (! id) {
	    		return;
	    	}
	    	if ($scope.removed.indexOf(id) >= 0) {
	    		return;
	    	}
	    	index = $scope.metadataList.findIndex(function(metadata) {
	    		return (metadata.idMetadata == id)
	    	});
	    	if (index < 0) {
	    		return;
	    	}
	    	$scope.removed.push(id);
	    	$scope.metadataList.splice(index, 1);
	    }
	    
	    $scope.save = function(res)
	    {
	    	if (! res) {
	    		return;
	    	}
	    	var url = xUrlHelper.getAction({
	    		action: "createmetadata",
	    		method: "save"
	    	});
	    	var postData = {removed: $scope.removed, metadata: $scope.metadataList};
	    	return $http.post(url, postData)
	    		.success(function(data, status, headers, config) {
					if (data.result === "ok") {
						if (data.messages && data.messages.length > 0) {
							$scope.showMessages(data.messages);
						}
						$scope.removed = [];
						return true;
					}
					if (data.error) {
						$scope.showError(data.error);
					}
					return false;
	    		})
	    		.error(function(data, status, headers, config) {});
	    };
	    
	    $scope.openSaveModal = function()
	    {
	    	if ($scope.removed.length) {
	    		return xDialog.openConfirmation($scope.save, "You are going delete " + $scope.removed.length 
	    				+ " metadata, do you want to continue?");
	    	}
	    	return $scope.save(true);
	    };
	    
	    $scope.showMessages = function(messages)
	    {
	    	var successMessage = $('#metadata_success_message');
			successMessage.html('<p>' + messages.join('. ') + '</p>');
			successMessage.show();
			successMessage.delay(8000).hide(0);
	    }
	    
	    $scope.showError = function(error)
	    {
	    	var successMessage = $('#metadata_error_message');
			successMessage.html('<p>' + error + '</p>');
			successMessage.show();
			successMessage.delay(8000).hide(0);
	    }
  	}
]);
