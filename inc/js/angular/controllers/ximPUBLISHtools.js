var mainController = angular.module('ximdex.main.controller');

mainController
        .controller('ximPUBLISHtools', ['$scope', '$http', '$interval', 'xUrlHelper', function ($scope, $http, $interval, xUrlHelper) {

                $scope.json = {};
                $scope.searchObj = {};
                $scope.urlParams = {};

                // helping function to inject external data
                $scope.init = function (params, initLoop) {
                    $scope.urlParams = {
                        action: params.action.command,
                        id: params.nodes[0],
                        module: params.action.module
                    };
                    if (initLoop) {
                        $scope.getFrameListLoop();
                    }
                };

                // Variables and functions needed to switch on/off the ajax loop
                $scope.getFrameListInterval = null;
                $scope.getFrameListLoop = function () {
                    $scope.requestFrameList();
                    $scope.getFrameListInterval = $interval($scope.requestFrameList, 5000);
                };
                $scope.requestFrameList = function () {
                    $scope.urlParams.method = 'getFrameList';
                    var url = xUrlHelper.getAction($scope.urlParams);

                    $http.get(url).success(function (data) {
                        $scope.json = data;
                    });
                };
                $scope.$on(
                        "$destroy",
                        function handleDestroyEvent() {
                            $interval.cancel($scope.getFrameListInterval);
                        }
                );

                // batch management functions
                $scope.stopBatch = function (batchId) {
                    $scope.urlParams.method = 'stopBatch';
                    var url = xUrlHelper.getAction($scope.urlParams);

                    $http({
                        method: 'POST',
                        url: url,
                        data: $.param({frm_deactivate_batch: 'yes', frm_id_batch: batchId}),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
                };

                $scope.startBatch = function (batchId) {
                    $scope.urlParams.method = 'startBatch';
                    var url = xUrlHelper.getAction($scope.urlParams);

                    $http({
                        method: 'POST',
                        url: url,
                        data: $.param({frm_activate_batch: 'yes', frm_id_batch: batchId}),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
                };

                $scope.incBatchPriority = function (batchId) {
                    $scope.urlParams.method = 'changeBatchPriority';
                    var url = xUrlHelper.getAction($scope.urlParams);

                    $http({
                        method: 'POST',
                        url: url,
                        data: $.param({frm_increase: 'yes', frm_id_batch: batchId}),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
                };

                $scope.decBatchPriority = function (batchId) {
                    $scope.urlParams.method = 'changeBatchPriority';
                    var url = xUrlHelper.getAction($scope.urlParams);

                    $http({
                        method: 'POST',
                        url: url,
                        data: $.param({frm_decrease: 'yes', frm_id_batch: batchId}),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
                };

                // method used in history view
                $scope.updateSearch = function (searchObj) {
                    console.log('updateSearch');
                    $scope.urlParams.method = 'getFrameList';
                    $scope.urlParams.options = [{finished: '1'}];
                    var url = xUrlHelper.getAction($scope.urlParams);
                    console.log(url);
                    $http({
                        method: 'POST',
                        url: url,
                        data: $.param(searchObj),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                            .success(function (data) {
                                $scope.json = data;
                            });
                };

                // helping function to adapt remaining time for future frames
                $scope.timeFromNow = function (date) {
                    var thatDate = new Date(parseInt(date) * 1000);
                    var thisDate = new Date();

                    var timeDiffMSecs = Math.abs(thatDate.getTime() - thisDate.getTime());
                    var timeDiffSecs = timeDiffMSecs / 1000;

                    var hours = Math.floor(timeDiffSecs / 3600);
                    timeDiffSecs = timeDiffSecs % 3600;

                    var min = Math.floor(timeDiffSecs / 60);
                    timeDiffSecs = Math.floor(timeDiffSecs % 60);

                    var timeStr = hours + 'H ' + min + 'm ' + timeDiffSecs + 's';
                    return timeStr;
                };

            }]);
