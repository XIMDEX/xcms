angular.module("ximdex.main.controller").controller "XTabsCtrl", [
    "$scope", "xTabs", "xUrlHelper", "$http", "$interval", "$sce", "$window", "$timeout"
    ($scope, xTabs, xUrlHelper, $http, $interval, $sce, $window, $timeout) ->

        $scope.tabs = xTabs.getTabs()
        $scope.removeTab = xTabs.removeTab
        $scope.setActiveTab = xTabs.setActive
        $scope.closeAllTabs = xTabs.closeAll
        $scope.offAllTabs = xTabs.offAll
        $scope.activeIndex = xTabs.activeIndex

        $scope.menuTabsEnabled = false
        $scope.showingMenu = false
        $scope.welcomeTab = ""
        $scope.limitTabs = 9999999

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

        $scope.$on "onModifyTabs",
            () ->
                temp = angular.element('#angular-content > .hbox-panel > .tabs-container')
                containerPosition = temp.offset()
                container = angular.element '#angular-content > .hbox-panel > .tabs-container > ul.ui-tabs-nav'
                containerWidth = temp.width()
                contents = angular.element '#angular-content > .hbox-panel > .tabs-container > ul.ui-tabs-nav > li'
                contentsWidth = 0
                rtContainer = (angular.element($window).width() - (containerPosition.left + temp.outerWidth()))
                contents.each (index, element) ->
                    return if index == 0
                    contentsWidth += angular.element(element).width() + 2
                if containerWidth - 30 < contentsWidth
                    container.css "left", (containerWidth - contentsWidth - 30) + "px"
                    $scope.menuTabsEnabled = true
                else
                    container.css "left", "0px"
                    $scope.menuTabsEnabled = false
                if $scope.activeIndex() == $scope.tabs.length - 1
                    $scope.limitTabs = $scope.activeIndex() + 1
                return



        $scope.$on 'onChangeActiveTab',
            () ->
                return if !$scope.menuTabsEnabled
                containerPosition = angular.element('#angular-content > .hbox-panel > .tabs-container').offset()
                container = angular.element '#angular-content > .hbox-panel > .tabs-container > ul'
                rtContainer = (angular.element($window).width() - (containerPosition.left + angular.element('#angular-content > .hbox-panel > .tabs-container').outerWidth()))

                if $scope.activeIndex() >= 0
                    idContent = "#" + $scope.tabs[$scope.activeIndex()].id + "_tab"
                    elementPosition = angular.element(idContent).offset().left
                    if elementPosition < containerPosition.left
                        for a, i in container.find('li')
                            continue if i == 0
                            rtElement = (angular.element($window).width() - (angular.element(a).offset().left + angular.element(a).outerWidth())) - (containerPosition.left-elementPosition)
                            if rtContainer + 30 > rtElement
                                $scope.limitTabs = i-1
                                break
                        container.css "left", (parseInt(container.css("left")) + (containerPosition.left-elementPosition)) + "px"
                    else
                        rtElement = (angular.element($window).width() - (angular.element(idContent).offset().left + angular.element(idContent).outerWidth()))
                        if rtContainer + 30 > rtElement
                            $scope.limitTabs = $scope.activeIndex() + 1
                            container.css("left",(parseInt(container.css("left")) + rtElement-rtContainer-30)+"px")

                return




]