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

angular.module("ximdex.main.controller").controller("XModifyMetadataGroups", ["$scope", "$http", "xUrlHelper", "$window", "$filter"
		, "xDialog", 
  	function($scope, $http, xUrlHelper, $window, $filter, xDialog)
  	{
	    $scope.init = function()
	    {
	    	$scope.removed = [];
	    	$scope.enabled = true;
	    };
	    
	    $scope.hasElements = function(source)
	    {
	    	var result = false;
	    	if (Array.isArray(source)) {
	    		result = source.length > 0;
	    	} else if (typeof source === "object") {
	    		result = Object.keys(source).length > 0;
	    	}
	    	return result;
	    }
	    
	    $scope.create = function()
	    {
	    	var metadata = $scope.metadata;
	    	var scheme = $scope.scheme;
	    	var group = $scope.group;
	    	if (! metadata || ! scheme || ! group) {
	    		$scope.showError("Metadata, scheme and group are required");
	    		return false;
	    	}
	    	var required = $scope.required;
	    	var readonly = $scope.readonly;
	    	var enabled = $scope.enabled;
	    	var url = xUrlHelper.getAction({
	    		action: "modifymetadatagroups",
	    		method: "add"
	    	});
	    	var postData = {metadata: metadata, scheme: scheme, group: group, required: required, readonly: readonly, enabled: enabled};
	    	return $http.post(url, postData)
	    		.success(function(data, status, headers, config) {
					if (data.result === "ok") {
						postData.idRelMetadataGroupMetadata = data.id;
						var metaList = $scope.schemes[scheme].groups[group].metadata;
						if (Array.isArray(metaList)) {
							metaList = {}
						}
						var name = $scope.metadataList[metadata].name;
						metaList[data.id] = {
							id: data.id,
							name: name,
							required: required,
							readonly: readonly,
							enabled: enabled,
							values: 0
						};
						$scope.schemes[scheme].groups[group].metadata = metaList;
						$scope.metadata = $scope.scheme = $scope.group = "";
						$scope.required = $scope.readonly = false;
						$scope.enabled = true;
						$scope.showMessages(["Metadata " + name + " has been added to group"]);
						return true;
					}
					if (data.error) {
						$scope.showError(data.error);
					}
					return false;
	    		})
	    		.error(function(data, status, headers, config) {});
	    };
	    
	    $scope.remove = function(idRel, group, scheme)
	    {
	    	if (! idRel || ! scheme || ! group) {
	    		return;
	    	}
	    	if ($scope.removed.indexOf(idRel) >= 0) {
	    		return;
	    	}
	    	if ($scope.schemes[scheme].groups[group].metadata[idRel]) {
	    		delete $scope.schemes[scheme].groups[group].metadata[idRel];
	    		$scope.removed.push(idRel);
	    	}
	    }
	    
	    $scope.save = function()
	    {
	    	var url = xUrlHelper.getAction({
	    		action: "modifymetadatagroups",
	    		method: "save"
	    	});
	    	var postData = {schemes: $scope.schemes, removed: $scope.removed};
	    	return $http.post(url, postData)
	    		.success(function(data, status, headers, config) {
					if (data.result === "ok") {
						if (data.messages && data.messages.length > 0) {
							$scope.showMessages(data.messages);
						}
						return true;
					}
					if (data.error) {
						$scope.showError(data.error);
					}
					return false;
	    		})
	    		.error(function(data, status, headers, config) {});
	    };
	    
	    $scope.showMessages = function(messages)
	    {
	    	var successMessage = $("#metadatagroups_success_message");
			successMessage.html("<p>" + messages.join(". ") + "</p>");
			successMessage.show();
			successMessage.delay(8000).hide(0);
	    }
	    
	    $scope.showError = function(error)
	    {
	    	var successMessage = $("#metadatagroups_error_message");
			successMessage.html("<p>" + error + "</p>");
			successMessage.show();
			successMessage.delay(8000).hide(0);
	    }
  	}
]);
