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
    
	$scope.json = [];
    $scope.searchObj = {};
    $scope.urlParams = {};
    $scope.showing = [];
    
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
      $scope.getFrameListInterval = $interval($scope.requestFrameList, 5000);
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
    
    $scope.stopBatch = function(batchId) {
      var url;
      $scope.urlParams.method = 'stopBatch';
      url = xUrlHelper.getAction($scope.urlParams);
      $http({
        method: 'POST',
        url: url,
        data: $.param({
          frm_deactivate_batch: 'yes',
          frm_id_batch: batchId
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      });
    };
    
    $scope.startBatch = function(batchId) {
      var url;
      $scope.urlParams.method = 'startBatch';
      url = xUrlHelper.getAction($scope.urlParams);
      $http({
        method: 'POST',
        url: url,
        data: $.param({
          frm_activate_batch: 'yes',
          frm_id_batch: batchId
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      });
    };
    
    $scope.incBatchPriority = function(batchId) {
      var url;
      $scope.urlParams.method = 'changeBatchPriority';
      url = xUrlHelper.getAction($scope.urlParams);
      $http({
        method: 'POST',
        url: url,
        data: $.param({
          frm_increase: 'yes',
          frm_id_batch: batchId
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      });
    };
    
    $scope.decBatchPriority = function(batchId) {
      var url;
      $scope.urlParams.method = 'changeBatchPriority';
      url = xUrlHelper.getAction($scope.urlParams);
      $http({
        method: 'POST',
        url: url,
        data: $.param({
          frm_decrease: 'yes',
          frm_id_batch: batchId
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      });
    };
    
    $scope.timeFromNow = function(date) {
      var hours, min, thatDate, thisDate, timeDiffMSecs, timeDiffSecs, timeStr;
      if (date == null) {
        return;
      }
      thatDate = new Date(parseInt(date) * 1000);
      thisDate = new Date();
      timeDiffMSecs = Math.abs(thatDate.getTime() - thisDate.getTime());
      timeDiffSecs = timeDiffMSecs / 1000;
      hours = Math.floor(timeDiffSecs / 3600);
      timeDiffSecs = timeDiffSecs % 3600;
      min = Math.floor(timeDiffSecs / 60);
      timeDiffSecs = Math.floor(timeDiffSecs % 60);
      return timeStr = hours + 'H ' + min + 'm ' + timeDiffSecs + 's';
    };
    return;
  }
]);
