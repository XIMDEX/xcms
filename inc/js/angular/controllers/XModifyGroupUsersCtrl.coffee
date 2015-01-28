angular.module("ximdex.main.controller").controller 'XModifyGroupUsersCtrl', [
    '$scope', '$http',
    'xUrlHelper', '$window',
    '$filter', 'xDialog',
    ($scope, $http, xUrlHelper, $window, $filter, xDialog) ->

        orderBy = $filter 'orderBy'

        orderAndReset = () ->
            $scope.users_not_associated = orderBy $scope.users_not_associated, 'name'
            if $scope.users_not_associated[0]?
                $scope.newUser = $scope.users_not_associated[0]
            $scope.newRole = Object.keys($scope.roles)[0]
            $scope.users_associated = orderBy $scope.users_associated, 'UserName'
            return

        $scope.init = () ->
            $scope.newRole = Object.keys($scope.roles)[0]
            return

        $scope.addGroup = () ->
            role = $scope.newRole
            user = $scope.newUser.id
            url = xUrlHelper.getAction
                action: 'modifygroupusers',
                method: 'addgroupuser'
            $http.get url + "&nodeid=" + $scope.nodeid + "&id_user=" + user + "&id_role=" + role
                .success (data, status, headers, config) ->
                    if (data.result == "ok")
                        nUser =
                            IdUser: $scope.newUser.id
                            UserName: $scope.newUser.name
                            IdRole: role
                            dirty: false
                        $scope.users_associated.push nUser
                        index = $scope.users_not_associated.indexOf $scope.newUser
                        $scope.users_not_associated.splice index, 1
                        orderAndReset()
                .error (data, status, headers, config) ->


        $scope.update = (index) ->
            user = $scope.users_associated[index]
            url = xUrlHelper.getAction
                action: 'modifygroupusers'
                method: 'editgroupuser'

            $http.get url + "&nodeid=" + $scope.nodeid + "&user=" + user.IdUser + "&role=" + user.IdRole
                .success (data, status, headers, config) ->
                    if (data.result == "ok")
                        $scope.users_associated[index].dirty = false
                    return
                .error (data, status, headers, config) ->
            return

        $scope.openDeleteModal = (index) ->
            $scope.index=index
            xDialog.openConfirmation $scope.delete,
                "ui.dialog.messages.you_are_going_to_delete_this_association,_do_you_want_to_continue?"

        $scope.delete = (res) ->
            return if !res
            index = $scope.index
            user = $scope.users_associated[index]
            url = xUrlHelper.getAction
                action: 'modifygroupusers'
                method: 'deletegroupuser'

            $http.get url + "&nodeid=" + $scope.nodeid  + "&user=" + user.IdUser
                .success (data, status, headers, config) ->
                    if (data.result == "ok")
                        nuser =
                            id: user.IdUser
                            name: user.UserName
                        $scope.users_not_associated.push nuser
                        $scope.users_associated.splice index, 1
                        orderAndReset()
                    return

                .error (data, status, headers, config) ->
            return
]
