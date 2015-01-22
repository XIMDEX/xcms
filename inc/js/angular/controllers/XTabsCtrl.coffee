angular.module("ximdex.main.controller").controller "XTabsCtrl", [
    "$scope", "xTabs", "xUrlHelper", "$http", "$interval", "$sce", "$window", "$timeout"
    ($scope, xTabs, xUrlHelper, $http, $interval, $sce, $window, $timeout) ->

        #Communication with the service xTabs
        $scope.tabs = xTabs.getTabs()
        $scope.removeTab = xTabs.removeTab
        $scope.setActiveTab = xTabs.setActive
        $scope.closeAllTabs = xTabs.closeAll
        $scope.offAllTabs = xTabs.offAll
        $scope.activeIndex = xTabs.activeIndex
        $scope.submitForm = xTabs.submitForm
        $scope.closeTabById = xTabs.removeTabById
        $scope.reloadTabById = xTabs.reloadTabById
        $scope.openAction = (action, nodes) ->
            nodesArray = []
            if Array.isArray nodes
                for n in nodes
                    newNode =
                        nodeid: n
                    nodesArray.push newNode
            else if nodes
                nodesArray.push {nodeid: nodes}
            xTabs.pushTab(action, nodesArray)
            return

        #Toggles the menu button
        $scope.menuTabsEnabled = false
        #Toggles the context menu
        $scope.showingMenu = false
        #Initializes content of welcomeTab
        $scope.welcomeTab = ""
        #Initializes this value with a large number
        $scope.limitTabs = 9999999

        #Reloads welcome tab
        reloadWelcomeTab = () ->
            nodes = [{nodeid: 10000}]
            url = xUrlHelper.getAction(
                action: "welcome"
                nodes: nodes
            )
            $http.get(url).success (data) ->
                if data
                    newtab =
                        id: "10000_welcome"
                        name: "welcome"
                        content_untrusted: data
                        content: $sce.trustAsHtml(data)
                        nodes: nodes
                        action: null
                        command: "welcome"
                        blink: false
                        show: true
                        url: url
                    xTabs.loadCssAndJs newtab
                    $scope.welcomeTab = newtab.content
            return

        #At first, reloads welcome tab
        reloadWelcomeTab()
        ###
        #Reloads welcome tab every 30 seconds if welcome tab is active
        $interval(
                () ->
                    return if $scope.tabs.length > 0
                    reloadWelcomeTab()
            ,
                30000
        )
        ###
        #Closes the menu
        $scope.closeMenu = () ->
            $scope.showingMenu=false
            return

        #Catches the root event onModifyTabs
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



        #Catches the root event onChangeActiveTab
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