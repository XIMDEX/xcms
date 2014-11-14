angular.module("ximdex.module.xmodifystates", ["ui.sortable", 'ngAnimate']).controller "XModifyStatesCtrl", [
    "$scope"
    "$http"
    "xUrlHelper"
    "$timeout"
    "$window"
    ($scope, $http, xUrlHelper, $timeout, $window) ->
        url = xUrlHelper.getAction
            action: 'modifystates',
            method: 'update_states'

        $scope.toDelete = []
        $scope.thereAreMessages = false

        $scope.sortableOptions =
            axis: 'y'
            items: 'li.sortable'
            handle: '.sortable_element'

        #Add a new blank state to workflow
        $scope.addStatus = (index) ->
            n =
                id: null
                name: ""
                description: ""
            $scope.all_status_info.splice index + 1, 0, n

        #Delete a state
        $scope.deleteStatus = (index) ->
            $scope.toDelete.push $scope.all_status_info.splice(index, 1)[0]

        $scope.saveChanges = () ->
            $scope.loading = true
            petition = $http.post url,
                states: $scope.all_status_info,
                idNode: $scope.idNode,
                toDelete: $scope.toDelete
            petition.success (data, status, headers, config) ->
                $scope.loading = false
                if data.result == "ok"
                    $scope.all_status_info = JSON.parse data.all_status_info
                    $scope.toDelete = []
                    $window.com.ximdex.widgetsVars.bw1.element.browserwindow("emptyActionsCache");
                if data.message != ""
                    $scope.messageClass = if data.result == "ok" then "message-success" else "message-error"
                    $scope.thereAreMessages = true
                    $scope.message = data.message
                    $timeout(
                        ->
                            $scope.thereAreMessages = false
                            $timeout(
                                ->
                                    $scope.message = ""
                            ,
                                500
                            )
                    ,
                        2000
                    )
            petition.error (data, status, headers, config) ->
                $scope.loading = false
]