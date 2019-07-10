angular.module("ximdex.main.controller").controller "XUserMenuCtrl", [
    "$scope", "xTabs",
    ($scope, xTabs) ->
        $scope.changeLang = (langCode, langTitle) ->
            action =
                command: "changelang"
                name: langTitle
                params: [
                    code: langCode
                ]
            nodes = [
                nodeid: "10000"
            ]
            xTabs.pushTab action, nodes
            return
        $scope.modifyAccount = (userId, tabTitle) ->
            action =
                command: "modifyuser"
                method: "index"
                name: tabTitle
            nodes = [
                nodeid: userId
            ]
            xTabs.pushTab action, nodes
            return
        return
]