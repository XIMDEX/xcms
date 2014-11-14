angular.module("ximdex.module.xmodifystatesrole", ['ngAnimate']).controller "XModifyStatesRoleCtrl", [
    "$scope"
    "$http"
    "xUrlHelper"
    "$timeout"
    ($scope, $http, xUrlHelper, $timeout) ->
        url = xUrlHelper.getAction
            action: 'modifystatesrole',
            method: 'update_states'

        $scope.saveChanges = ->
            $scope.loading = true
            petition = $http.post url,
                states: $scope.all_states,
                idRole: $scope.idRole
            petition.success (data, status, headers, config) ->
                $scope.loading = false
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