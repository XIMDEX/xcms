angular.module("ximdex.main.controller").controller "XTabsCtrl", [
    "$scope", "xTabs", "xUrlHelper", "$http", "$interval", "$sce"
    ($scope, xTabs, xUrlHelper, $http, $interval, $sce) ->

        $scope.tabs = xTabs.getTabs()
        $scope.removeTab = xTabs.removeTab
        $scope.setActiveTab = xTabs.setActive
        $scope.closeAllTabs = xTabs.closeAll
        $scope.offAllTabs = xTabs.offAll
        $scope.getActiveIndex = xTabs.getActiveIndex

        $scope.menuTabsEnabled = false
        $scope.showingMenu = false
        $scope.welcomeTab = ""

        reloadWelcomeTab = () ->
            $http.get(xUrlHelper.getAction(
                action: "welcome"
                nodes: [{nodeid: 10000}]
            )).success (data) ->
                if data
                    xTabs.loadCssAndJs data
                    $scope.welcomeTab = $sce.trustAsHtml(data)
            return

        reloadWelcomeTab()

        $interval(
                () ->
                    return if $scope.tabs.length > 0
                    reloadWelcomeTab()
            ,
                15000
        )

        $scope.closeMenu = () ->
            $scope.showingMenu=false
            return

        $scope.$watch 'numberOfTabs',
            (newValue, oldValue) ->
                return if newValue == 0

        $scope.$on "modifiedTabs",
            () ->
                temp = angular.element('#angular-content > .hbox-panel > .tabs-container')
                container = angular.element '#angular-content > .hbox-panel > .tabs-container > ul.ui-tabs-nav'
                containerWidth = temp.width()
                contents = angular.element '#angular-content > .hbox-panel > .tabs-container > ul.ui-tabs-nav > li'
                contentsWidth = 0
                contents.each (index, element) ->
                    return if index == 0
                    contentsWidth += angular.element(element).width() + 2
                if containerWidth - 30 < contentsWidth
                    container.css "left", (containerWidth-contentsWidth-30) + "px"
                    $scope.menuTabsEnabled = true
                else
                    container.css "left", "0px"
                    $scope.menuTabsEnabled = false


]