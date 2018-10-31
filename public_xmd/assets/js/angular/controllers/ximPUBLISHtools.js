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
    
	var MAX_INTERVAL = 90000;	// 90 seconds to refresh report
    var INCREMENT_INTERVAL = 1.1;
    var INIT_INTERVAL = 5000;
    
	$scope.json = [];
    $scope.urlParams = {};
    $scope.showing = [];
    var interval = null;
    
    $scope.init = function(params, initLoop) {
      $scope.urlParams = {
        action: params.action.command,
        id: params.nodes[0],
        module: params.action.module
      };
      if (initLoop) {
        $scope.getFrameListLoop();
      }
    };
    
    $scope.getFrameListInterval = null;
    
    $scope.getFrameListLoop = function() {
      $scope.requestFrameList();
      if (!interval) {
    	  
    	  // Initial interval value
    	  interval = INIT_INTERVAL;
      } else {
    	  
    	  // Increment
    	  $interval *= INCREMENT_INTERVAL;
      }
      if (interval > MAX_INTERVAL) {
    	  interval = MAX_INTERVAL;
      }
      
      console.log('Interval: ' + interval);
      
      $scope.getFrameListInterval = $interval($scope.requestFrameList, interval);
    };
    
    $scope.requestFrameList = function() {
      var url;
      $scope.urlParams.method = 'getFrameList';
      url = xUrlHelper.getAction($scope.urlParams);
      $http.get(url).success(function(data) {
        $scope.json = data;
      });
    };
    
    $scope.$on("$destroy", function() {
      $interval.cancel($scope.getFrameListInterval);
    });
    
    // Portal play
    $scope.playPortal = function(portalId) {
    	var params = $.param({
            	id: portalId
            });
    	callMethod('playPortal', params);
    	interval = null;
    };
    
    // Portal pause
    $scope.pausePortal = function(portalId) {
    	var params = $.param({
            	id: portalId
            });
    	callMethod('pausePortal', params);
    	interval = null;
    };
    
    // Servers restart
    $scope.restartServer = function(serverId) {
    	var params = $.param({
            	id: serverId
            });
    	callMethod('restartServer', params);
    	interval = null;
    };
    
    // Batchs restart
    $scope.restartBatchs = function(portalId) {
    	var params = $.param({
            	id: portalId
            });
    	callMethod('restartBatchs', params);
    	interval = null;
    };
    
    function callMethod(method, params)
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
