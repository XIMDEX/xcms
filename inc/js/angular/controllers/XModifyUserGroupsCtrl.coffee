angular.module('ximdex.module.xmodifyusergroups').controller 'XModifyUserGroupsCtrl', [
    '$scope', '$http',
    'xUrlHelper', '$window',
    '$filter', 'xDialog'
    ($scope, $http, xUrlHelper, $window, $filter, xDialog) ->
        orderBy = $filter 'orderBy';
        orderAndReset = () ->
            $scope.filtered_groups = orderBy $scope.filtered_groups, 'Name'
            if ($scope.filtered_groups.length > 0)
                $scope.newGroup = $scope.filtered_groups[0]
            if ($scope.all_roles.length > 0)
                $scope.newRole = $scope.all_roles[0].IdRole
            return


        $scope.init = () ->
            if ($scope.filtered_groups.length > 0)
                $scope.newGroup = $scope.filtered_groups[0]

            if ($scope.all_roles.length > 0)
                $scope.newRole = $scope.all_roles[0].IdRole
            return

        $scope.addGroup = () ->
            role = $scope.newRole
            group = $scope.newGroup
            url = xUrlHelper.getAction
                action: 'modifyusergroups',
                method: 'suscribegroupuser'
            $http.get url + "&nodeid=" + $scope.nodeid + "&newgroup=" + group.IdGroup + "&newrole=" + role
                .success (data, status, headers, config) ->
                    if (data.result == "OK")
                        ngroup =
                            IdGroup: group.IdGroup
                            Name: group.Name
                            IdRole: role
                            dirty: false
                        $scope.user_groups_with_role.push ngroup
                        index = $scope.filtered_groups.indexOf group
                        $scope.filtered_groups.splice index, 1
                        orderAndReset()
                    return
                .error (data, status, headers, config) ->
                    return


        $scope.update = (index) ->
            role = $scope.user_groups_with_role[index].IdRole
            group = $scope.user_groups_with_role[index].IdGroup
            url = xUrlHelper.getAction
                action: 'modifyusergroups'
                method: 'updategroupuser'

            $http.get url + "&nodeid=" + $scope.nodeid + "&group=" + group + "&role=" + role
                .success (data, status, headers, config) ->
                    if (data.result == "OK")
                        $scope.user_groups_with_role[index].dirty = false
                    return
                .error (data, status, headers, config) ->
                    return
            return

        $scope.openDeleteModal = (index) ->
            $scope.index=index
            xDialog.openConfirmation $scope.delete,
                "ui.dialog.messages.you_are_going_to_delete_this_association,_do_you_want_to_continue?"
            return

        $scope.delete = (res) ->
            return if !res
            index=$scope.index
            group = $scope.user_groups_with_role[index].IdGroup
            url = xUrlHelper.getAction
                action: 'modifyusergroups'
                method: 'deletegroupuser'

            $http.get url + "&nodeid=" + $scope.nodeid + "&group=" + group
                .success (data, status, headers, config) ->
                    if (data.result == "OK")
                        group =
                            IdGroup: group
                            Name: $scope.user_groups_with_role[index].Name

                        $scope.filtered_groups.push group
                        $scope.user_groups_with_role.splice index, 1
                        orderAndReset()
                    return

                .error (data, status, headers, config) ->
            return
]
