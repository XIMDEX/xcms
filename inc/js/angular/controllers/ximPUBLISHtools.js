angular.module("ximdex.main.controller").controller("ximPUBLISHtools", [
  '$scope', '$http', '$interval', 'xUrlHelper', function($scope, $http, $interval, xUrlHelper) {
    $scope.json = {};
    $scope.searchObj = {};
    $scope.urlParams = {};
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
    $scope.updateSearch = function(searchObj) {
      var url;
      $scope.urlParams.method = 'getFrameList';
      $scope.urlParams.options = [
        {
          finished: '1'
        }
      ];
      url = xUrlHelper.getAction($scope.urlParams);
      $http({
        method: 'POST',
        url: url,
        data: $.param(searchObj),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function(data) {
        $scope.json = data;
      });
    };
    $scope.timeFromNow = function(date) {
      var hours, min, thatDate, thisDate, timeDiffMSecs, timeDiffSecs, timeStr;
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
  }
]);
