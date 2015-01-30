angular.module("ximdex.main.controller").controller "ximPUBLISHtools", [
    '$scope', '$http', '$interval', 'xUrlHelper',
    ($scope, $http, $interval, xUrlHelper) ->
        $scope.json = {}
        $scope.searchObj = {}
        $scope.urlParams = {}
    
        # helping function to inject external data
        $scope.init = (params, initLoop) ->
            $scope.urlParams =
                action: params.action.command
                id: params.nodes[0]
                module: params.action.module

            $scope.getFrameListLoop() if initLoop
            return
        
        # Variables and functions needed to switch on/off the ajax loop
        $scope.getFrameListInterval = null
        $scope.getFrameListLoop = () ->
            $scope.requestFrameList()
            $scope.getFrameListInterval = $interval $scope.requestFrameList, 5000
            return

        $scope.requestFrameList = () ->
            $scope.urlParams.method = 'getFrameList'
            url = xUrlHelper.getAction $scope.urlParams

            $http.get(url).success((data) ->
                $scope.json = data
                return
            )
            return

        $scope.$on("$destroy", () ->
            $interval.cancel $scope.getFrameListInterval
            return
        )
        
        # stop batch
        $scope.stopBatch = (batchId) ->
            $scope.urlParams.method = 'stopBatch'
            url = xUrlHelper.getAction $scope.urlParams

            $http {
                method: 'POST'
                url: url
                data: $.param {frm_deactivate_batch: 'yes', frm_id_batch: batchId}
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }
            return

        # start batch
        $scope.startBatch = (batchId) ->
            $scope.urlParams.method = 'startBatch'
            url = xUrlHelper.getAction $scope.urlParams

            $http {
                method: 'POST'
                url: url
                data: $.param {frm_activate_batch: 'yes', frm_id_batch: batchId}
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }
            return
        
        # increase batch priority
        $scope.incBatchPriority = (batchId) ->
            $scope.urlParams.method = 'changeBatchPriority'
            url = xUrlHelper.getAction $scope.urlParams

            $http {
                method: 'POST'
                url: url
                data: $.param {frm_increase: 'yes', frm_id_batch: batchId}
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }
            return
            
        # decrease batch priority
        $scope.decBatchPriority = (batchId) ->
            $scope.urlParams.method = 'changeBatchPriority'
            url = xUrlHelper.getAction $scope.urlParams

            $http {
                method: 'POST'
                url: url
                data: $.param {frm_decrease: 'yes', frm_id_batch: batchId}
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }
            return
        
        # method used in history view
        $scope.updateSearch = (searchObj) ->
            $scope.urlParams.method = 'getFrameList'
            $scope.urlParams.options = [{finished: '1'}]
            url = xUrlHelper.getAction $scope.urlParams
            $http({
                method: 'POST',
                url: url,
                data: $.param(searchObj),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .success((data) ->
                $scope.json = data;
                return
            )
            return
        
        # helping function to adapt remaining time for future frames
        $scope.timeFromNow = (date) ->
            thatDate = new Date(parseInt(date) * 1000)
            thisDate = new Date()

            timeDiffMSecs = Math.abs(thatDate.getTime() - thisDate.getTime())
            timeDiffSecs = timeDiffMSecs / 1000

            hours = Math.floor(timeDiffSecs / 3600)
            timeDiffSecs = timeDiffSecs % 3600

            min = Math.floor(timeDiffSecs / 60)
            timeDiffSecs = Math.floor(timeDiffSecs % 60)

            timeStr = hours + 'H ' + min + 'm ' + timeDiffSecs + 's'
        
        return
]