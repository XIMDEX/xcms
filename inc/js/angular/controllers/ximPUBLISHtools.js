var mainController = angular.module('ximdex.main.controller');

mainController
        .controller('ximPUBLISHtools', ['$scope', '$http', '$interval', function ($scope, $http, $interval) {
//                var url = xUrlHelper.getAction({
//                    action: 'managebatchs',
//                    method: 'getFrameList',
//                    mod: 'ximPUBLISHtools',
//                    noCacheVar: '1421669274703',
//                    nodes: [10012]
//                });

                $scope.fakeUrlFrameList = 'http://lab13.ximdex.net/ximdexdc2/xmd/loadaction.php?nodes[]=10012&nodeid=10012&action=managebatchs&method=getFrameList&noCacheVar=1421669274703&mod=ximPUBLISHtools';
                $scope.fakeUrlFrameListHistory = 'http://lab13.ximdex.net/ximdexdc2/xmd/loadaction.php?nodes[]=10012&nodeid=10012&action=managebatchs&method=getFrameList&finished=1&noCacheVar=1421669274703&mod=ximPUBLISHtools';

                $scope.fakeUrlStopBatch = 'http://lab13.ximdex.net/ximdexdc2/xmd/loadaction.php?nodes[]=10012&nodeid=10012&action=managebatchs&method=stopBatch&noCacheVar=1421669274703&mod=ximPUBLISHtools';
                $scope.fakeUrlStartBatch = 'http://lab13.ximdex.net/ximdexdc2/xmd/loadaction.php?nodes[]=10012&nodeid=10012&action=managebatchs&method=startBatch&noCacheVar=1421669274703&mod=ximPUBLISHtools';
                $scope.fakeUrlChangeBatchPriority = 'http://lab13.ximdex.net/ximdexdc2/xmd/loadaction.php?nodes[]=10012&nodeid=10012&action=managebatchs&method=changeBatchPriority&noCacheVar=1421669274703&mod=ximPUBLISHtools';
                $scope.json = {};
                $scope.searchObj = {};



                $scope.getFrameListInterval = null;
                $scope.getFrameListLoop = function () {
                    $scope.requestFrameList();
                    $scope.getFrameListInterval = $interval($scope.requestFrameList, 5000);
                };
                $scope.requestFrameList = function () {
                    $http.get($scope.fakeUrlFrameList)
                            .success(function (data) {
                                $scope.json = data;
                            });
                };
                $scope.$on(
                        "$destroy",
                        function handleDestroyEvent() {
                            $interval.cancel($scope.getFrameListInterval);
                        }
                );

                $scope.stopBatch = function (batchId) {
                    $http({
                        method: 'POST',
                        url: $scope.fakeUrlStopBatch,
                        data: $.param({frm_deactivate_batch: 'yes', frm_id_batch: batchId}),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                            .success(function (data) {
                            });
                };

                $scope.startBatch = function (batchId) {
                    $http({
                        method: 'POST',
                        url: $scope.fakeUrlStartBatch,
                        data: $.param({frm_activate_batch: 'yes', frm_id_batch: batchId}),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                            .success(function (data) {
                            });
                };

                $scope.incBatchPriority = function (batchId) {
                    $http({
                        method: 'POST',
                        url: $scope.fakeUrlChangeBatchPriority,
                        data: $.param({frm_increase: 'yes', frm_id_batch: batchId}),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                            .success(function (data) {
                            });
                };
                
                $scope.decBatchPriority = function (batchId) {
                    $http({
                        method: 'POST',
                        url: $scope.fakeUrlChangeBatchPriority,
                        data: $.param({frm_decrease: 'yes', frm_id_batch: batchId}),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                            .success(function (data) {
                            });
                };

                $scope.updateSearch = function (searchObj) {
                    $http({
                        method: 'POST',
                        url: $scope.fakeUrlFrameListHistory,
                        data: $.param(searchObj),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                            .success(function (data) {
                                $scope.json = data;
                            });
                };

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
