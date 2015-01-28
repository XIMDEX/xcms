angular
.module("ximdex.module.xsetextensions", [])
.controller "XSetExtensionsCtrl", [
    "$scope"
    "$http"
    "xUrlHelper"
    "$timeout"
    "$window"
    ($scope, $http, xUrlHelper, $timeout, $window) ->
        url = xUrlHelper.getAction
            action: 'setextensions',
            method: 'update_extensions'

        $scope.saveChanges = ->
            $scope.loading = true
            $http.post url,
                states: $scope.commonAllowedExtensions,
            .success (data, status, headers, config) ->
                $scope.loading = false
                if data.message != ""
                    showMessage(data.result, data.message)
            .error (data, status, headers, config) ->
                $scope.loading = false

        showMessage = (result, message) ->
            $scope.messageClass = if result == "ok" then "message-success" else "message-error"
            $scope.thereAreMessages = true
            $scope.message = message
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

]