angular.module("ximdex.main.controller").controller "XTabsCtrl", [
    "$scope", "xTabs", "xUrlHelper", "$http", "$interval", "$window", "$rootScope"
    ($scope, xTabs, xUrlHelper, $http, $interval, $window, $rootScope) ->

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
        #Initializes this value with a large number
        $scope.limitTabs = 9999999

        #Reloads welcome tab
        $scope.reloadWelcomeTab = () ->
            $rootScope.$broadcast('updateWelcomeTab')
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
                        content: data
                        nodes: nodes
                        action: null
                        command: "welcome"
                        blink: false
                        show: true
                        url: url
                    xTabs.loadCssAndJs newtab
                return
            return

        #At first, reloads welcome tab
        $scope.reloadWelcomeTab()



        #Closes the menu
        $scope.closeMenu = () ->
            $scope.showingMenu=false
            return



        rightPosition = (elem) ->
            return (angular.element($window).width() - (angular.element(elem).offset().left + angular.element(elem).outerWidth()))

        #Catches the root event updateTabsPosition
        $scope.$on 'updateTabsPosition',
            (event, deletedTab) ->
                temp = angular.element('#angular-content > .hbox-panel > .tabs-container')
                containerPosition = temp.offset()
                containerWidth = temp.width()
                container = angular.element '#angular-content > .hbox-panel > .tabs-container > ul'
                contents = angular.element '#angular-content > .hbox-panel > .tabs-container > ul.ui-tabs-nav > li'
                contentsWidth = 0
                rtContainer = rightPosition(temp)

                if $scope.activeIndex() < 0
                    container.css "left", "0px"
                    return

                idContent = "#" + $scope.tabs[$scope.activeIndex()].id + "_tab"
                if deletedTab
                    widthDeletedTab = angular.element("#" + deletedTab.id + "_tab").outerWidth()
                element = angular.element(idContent)
                elementPosition = element.offset().left
                rtElement = rightPosition(element)

                contents.each (index, element) ->
                    return if index == 0
                    contentsWidth += angular.element(element).width() + 2
                #Enable/Disable menuTabs
                if containerWidth - 30 < contentsWidth
                    $scope.menuTabsEnabled = true
                else
                    $scope.menuTabsEnabled = false
                #Update limitTabs value
                if $scope.activeIndex() == $scope.tabs.length - 1
                    $scope.limitTabs = $scope.activeIndex() + 1

                return if !$scope.menuTabsEnabled

                #Check if active tab is on the left
                if elementPosition < containerPosition.left
                    for a, i in container.find('li')
                        continue if i == 0
                        rtElement = (rightPosition(angular.element(a))) - (containerPosition.left-elementPosition)
                        if rtContainer + 30 > rtElement
                            $scope.limitTabs = i-1
                            break
                    container.css "left", (parseInt(container.css("left")) + (containerPosition.left-elementPosition)) + "px"
                #Check if active tab is on the right
                else if rtContainer + 30 > rtElement
                    $scope.limitTabs = $scope.activeIndex() + 1
                    widthDeletedTab = 0
                    newleft = parseInt(container.css("left")) + rtElement + widthDeletedTab - rtContainer - 40
                    if deletedTab
                        for c in container.find "li"
                            if c.offset().left > newleft
                                newleft = c.offset().left
                                break
                    container.css "left", newleft + "px"
                return

]