angular.module('ximdex.module.xmodifyusergroups')
    .controller('XModifyUserGroupsCtrl', ['$scope', '$http',
        'xUrlHelper', '$window', '$filter', '$modal',
        function ($scope, $http, xUrlHelper, $window, $filter, $modal) {

            var orderBy = $filter('orderBy');
            var orderAndReset = function () {
                $scope.filtered_groups = orderBy($scope.filtered_groups, 'Name');
                if ($scope.filtered_groups.length > 0) {
                    $scope.newGroup = $scope.filtered_groups[0];
                }
                if ($scope.all_roles.length > 0) {
                    $scope.newRole = $scope.all_roles[0].IdRole;
                }
            }
            $scope.init = function (nodeid) {
                $scope.nodeid = nodeid;
                var url = xUrlHelper.getAction({
                    action: 'modifyusergroups',
                    method: 'getGroups'
                });
                $http.get(url + "&nodeid=" + $scope.nodeid).
                    success(function (data, status, headers, config) {
                        $scope.user_name = data.user_name;
                        $scope.general_role = data.general_role;
                        $scope.all_roles = data.all_roles;
                        $scope.filtered_groups = data.filtered_groups;
                        $scope.user_groups_with_role = data.user_groups_with_role;
                        if ($scope.filtered_groups.length > 0) {
                            $scope.newGroup = $scope.filtered_groups[0];
                        }
                        if ($scope.all_roles.length > 0) {
                            $scope.newRole = $scope.all_roles[0].IdRole;
                        }
                    }).
                    error(function (data, status, headers, config) {

                    });
            }

            $scope.addGroup = function () {
                var role = $scope.newRole;
                var group = $scope.newGroup;
                var url = xUrlHelper.getAction({
                    action: 'modifyusergroups',
                    method: 'suscribegroupuser'
                });


                $http.get(url + "&nodeid=" + $scope.nodeid + "&newgroup=" + group.IdGroup + "&newrole=" + role).
                    success(function (data, status, headers, config) {
                        if (data.result == "OK") {
                            var ngroup = {
                                IdGroup: group.IdGroup,
                                Name: group.Name,
                                IdRole: role,
                                dirty: false
                            };
                            $scope.user_groups_with_role.push(ngroup);
                            var index = $scope.filtered_groups.indexOf(group);
                            $scope.filtered_groups.splice(index, 1);
                            orderAndReset();
                        }
                        //$window.humane.log(data.message, {addnCls: 'notification-success'});
                    }).
                    error(function (data, status, headers, config) {

                    });
            }

            $scope.update = function (index) {
                var role = $scope.user_groups_with_role[index].IdRole;
                var group = $scope.user_groups_with_role[index].IdGroup;
                var url = xUrlHelper.getAction({
                    action: 'modifyusergroups',
                    method: 'updategroupuser'
                });
                $http.get(url + "&nodeid=" + $scope.nodeid + "&group=" + group + "&role=" + role).
                    success(function (data, status, headers, config) {
                        if (data.result == "OK") {
                            $scope.user_groups_with_role[index].dirty = false;
                        }
                        //$window.humane.log(data.message, {addnCls: 'notification-success'});
                    }).
                    error(function (data, status, headers, config) {

                    });
            }
            $scope.openDeleteModal = function (index) {
                var modalInstance = $modal.open({
                    templateUrl: 'XModifyUserGroupsModal.html',
                    controller: 'XModifyUserGroupsModal',
                    resolve: {
                        index: function () {
                            return index;
                        }
                    }
                });
                modalInstance.result.then(function (index) {
                    $scope.delete(index);
                }, function () {
                });
            }
            $scope.delete = function (index) {
                var group = $scope.user_groups_with_role[index].IdGroup;
                var url = xUrlHelper.getAction({
                    action: 'modifyusergroups',
                    method: 'deletegroupuser'
                });
                $http.get(url + "&nodeid=" + $scope.nodeid + "&group=" + group).
                    success(function (data, status, headers, config) {
                        if (data.result == "OK") {
                            var group = {
                                IdGroup: group,
                                Name: $scope.user_groups_with_role[index].Name
                            }
                            $scope.filtered_groups.push(group);
                            $scope.user_groups_with_role.splice(index, 1);
                            orderAndReset();
                        }
                        //$window.humane.log(data.message, {addnCls: 'notification-success'});
                    }).
                    error(function (data, status, headers, config) {

                    });
            }
        }]);

angular.module('ximdex.module.xmodifyusergroups').controller('XModifyUserGroupsModal',
    function ($scope, $modalInstance, index) {

    $scope.ok = function () {
        $modalInstance.close(index);
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});