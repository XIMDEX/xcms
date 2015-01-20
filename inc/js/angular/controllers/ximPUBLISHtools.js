var mainController = angular.module('ximdex.main.controller');

mainController
        .controller('ximPUBLISHtools', ['$scope', '$http', 'xUrlHelper', function ($scope, $http, xUrlHelper) {
//                var url = xUrlHelper.getAction({
//                    action: 'managebatchs',
//                    method: 'getFrameList',
//                    mod: 'ximPUBLISHtools',
//                    noCacheVar: '1421669274703',
//                    nodes: [10012]
//                });

                $scope.pullDataOnce = false;
                $scope.fakeUrlFrameList = 'http://lab13.ximdex.net/ximdexdc2/xmd/loadaction.php?nodes[]=10012&action=managebatchs&method=getFrameList&noCacheVar=1421669274703&mod=ximPUBLISHtools';

                $scope.stopBatchOnce = false;
                $scope.fakeUrlStopBatch = 'http://lab13.ximdex.net/ximdexdc2/xmd/loadaction.php?nodes[]=10012&action=managebatchs&method=stopBatch&noCacheVar=1421669274703&mod=ximPUBLISHtools';

                $scope.fakeUrlStartBatch = 'http://lab13.ximdex.net/ximdexdc2/xmd/loadaction.php?nodes[]=10012&action=managebatchs&method=startBatch&noCacheVar=1421669274703&mod=ximPUBLISHtools';

                $scope.json = {};



                $scope.getFrameList = function () {
                    console.log('getFrameList0');
                    if (!$scope.pullDataOnce) {
                        console.log('pullState');
                        $http.get($scope.fakeUrlFrameList)
                                .success(function (data) {
                                    console.log("received data!");
                                    $scope.json = data;
                                });
                        $scope.pullDataOnce = true;
                    }
                };

                $scope.stopBatch = function (batchId) {
                    console.log('stopBatch0 ' + batchId);
                    $http({
                        method: 'POST',
                        url: $scope.fakeUrlStopBatch,
                        data: $.param({frm_deactivate_batch: 'yes', frm_id_batch: batchId}),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                            .success(function (data) {
                                console.log("stopBatch! " + batchId);
                                $scope.json = data;
                            })
                            .error(function () {
                                console.log("errorBatch stop!!" + batchId);
                            });
                };

                $scope.startBatch = function (batchId) {
                    console.log('startBatch0');
                    $http({
                        method: 'POST',
                        url: $scope.fakeUrlStartBatch,
                        data: $.param({frm_activate_batch: 'yes', frm_id_batch: batchId}),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                            .success(function (data) {
                                console.log("startBatch! " + batchId);
                                $scope.json = data;
                            })
                            .error(function () {
                                console.log("startBatch resumed error!!");
                            });
                };
            }]);
