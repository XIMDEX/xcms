/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

angular.module("ximdex.main.controller").controller("ximPUBLISHtools", [
	'$scope', '$http', '$interval', 'xUrlHelper', function($scope, $http, $interval, xUrlHelper) {
    
    // 90 seconds maximum to refresh report
	var MAX_INTERVAL = 2000;	// 90000;
	
	// 5 seconds for interval of refresh data
    var INIT_INTERVAL = 2000;
    var INCREMENT_INTERVAL = 1.1;
    
	$scope.json = [];
    $scope.urlParams = {};
    $scope.showing = [];
    $scope.interval = null;
    $scope.getFrameListInterval = null;
    
    $scope.init = function(params, initLoop)
    {
    	$scope.urlParams = {
    			action: params.action.command,
    			id: params.nodes[0],
    			module: params.action.module
    	};
    	if (initLoop) {
    		$scope.getFrameListLoop();
    	}
    };
    
    $scope.getFrameListLoop = function()
    {
    	$scope.interval = null;
    	$scope.requestFrameList();
    };
    
    $scope.requestFrameList = function()
    {
    	$scope.urlParams.method = 'getFrameList';
    	var url = xUrlHelper.getAction($scope.urlParams);
    	$http.get(url).success(function(data) {
    		$scope.json = data;
    	});
    	
    	// Increment interval
    	// console.log('Interval: ' + $scope.interval);
    	if (! $scope.interval) {
    		
    		// Reset interval
    		$scope.interval = INIT_INTERVAL;
    	} else {
	    	$scope.interval = Math.round($scope.interval * INCREMENT_INTERVAL);
			if ($scope.interval > MAX_INTERVAL) {
				$scope.interval = MAX_INTERVAL;
			}
    	}
    	$interval.cancel($scope.getFrameListInterval);
    	$scope.getFrameListInterval = $interval($scope.requestFrameList, $scope.interval);
    };
    
    $scope.$on("$destroy", function()
    {
    	$interval.cancel($scope.getFrameListInterval);
    });
    
    // Portal play
    $scope.playPortal = function(portalId)
    {
    	var params = $.param({
            	id: portalId
            });
    	$scope.callMethod('playPortal', params);
    	$scope.getFrameListLoop();
    };
    
    // Portal pause
    $scope.pausePortal = function(portalId)
    {
    	var params = $.param({
            	id: portalId
            });
    	$scope.callMethod('pausePortal', params);
    	$scope.getFrameListLoop();
    };
    
    // Servers restart
    $scope.restartServer = function(serverId)
    {
    	var params = $.param({
            	id: serverId
            });
    	$scope.callMethod('restartServer', params);
    	$scope.getFrameListLoop();
    };
    
    // Batchs restart
    $scope.restartBatchs = function(portalId)
    {
    	var params = $.param({
            	id: portalId
            });
    	$scope.callMethod('restartBatchs', params);
    	$scope.getFrameListLoop();
    };
    
    $scope.boostPortal = function(portalId, value)
    {
    	var params = $.param({
            	id: portalId,
            	value: value
            });
    	$scope.callMethod('boostPortal', params);
    	$scope.getFrameListLoop();
    };
    
    $scope.callMethod = function(method, params)
    {
    	var url;
        $scope.urlParams.method = method;
        url = xUrlHelper.getAction($scope.urlParams);
        $http({
          method: 'POST',
          url: url,
          data: params,
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        });
    }
  }
]);
