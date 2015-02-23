angular.module("ximdex.common.directive").directive "ximTabs", [
    "xTabs", "xUrlHelper", "$http", "$interval", "$window", "$timeout"
    (xTabs, xUrlHelper, $http, $interval, $window, $timeout) ->
        base_url = $window.X.baseUrl
        return (
            templateUrl: base_url+'/inc/js/angular/templates/ximTabs.html'
            restrict: "E"
            replace: true
            scope:
                xid: "@ximId"
            controller: ($scope) ->
                #Communication with the service xTabs
                $scope.tabs = xTabs.getTabs()
                $scope.removeTab = xTabs.removeTab
                $scope.setActiveTab = xTabs.setActiveTab
                $scope.setActiveTabById = xTabs.setActiveTabById
                $scope.closeAllTabs = xTabs.closeAllTabs
                $scope.offAllTabs = xTabs.offAllTabs
                $scope.activeIndex = xTabs.activeIndex
                $scope.submitForm = xTabs.submitForm
                $scope.removeTabById = xTabs.removeTabById
                $scope.reloadTabById = xTabs.reloadTabById
                $scope.getActiveTab = xTabs.getActiveTab

                #Toggles the menu button
                $scope.menuTabsEnabled = false
                #Toggles the context menu
                $scope.showingMenu = false
                #Initializes this value with a large number
                $scope.limitTabs = 9999999

                #Reloads welcome tab
                $scope.reloadWelcomeTab = (firstTime) ->
                    nodes = [{nodeid: 10000}]
                    if firstTime
                        url = xUrlHelper.getAction(
                            action: "welcome"
                            nodes: nodes
                        )
                    else
                        url = xUrlHelper.getAction(
                            action: "welcome"
                            nodes: nodes
                            options: [
                                actionReload: true
                            ]
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
                                history: [url]
                            xTabs.loadCssAndJs newtab
                        return
                    return

                #At first, reloads welcome tab
                $scope.reloadWelcomeTab(true)

                $scope.$on 'nodemodified', (event, nodeId) ->
                    $scope.reloadWelcomeTab()
                    return

                #Closes the menu
                $scope.closeMenu = () ->
                    $scope.showingMenu=false
                    return



                rightPosition = (elem) ->
                    return (angular.element($window).width() - (angular.element(elem).offset().left + angular.element(elem).outerWidth()))

                #Catches the root event updateTabsPosition
                $scope.$on 'updateTabsPosition',
                    (event, deletedTab) ->
                        newleft = 0

                        temp = angular.element('#angular-content > .hbox-panel > .tabs-container')
                        containerPosition = temp.offset()
                        containerWidth = temp.width()
                        container = angular.element '#angular-content > .hbox-panel > .tabs-container > ul'
                        contents = angular.element '#angular-content > .hbox-panel > .tabs-container > ul.ui-tabs-nav > li'
                        contentsWidth = 0
                        rtContainer = rightPosition(temp)

                        actualLeft = parseInt(container.css("left"))

                        if $scope.activeIndex() < 0
                            container.css "left", "0px"
                            return

                        idContent = "#" + $scope.tabs[$scope.activeIndex()].id + "_tab"
                        widthDeletedTab = 0
                        if deletedTab
                            widthDeletedTab = angular.element("#" + deletedTab.id + "_tab").outerWidth()
                        element = angular.element(idContent)
                        elementPosition = element.offset().left
                        rtElement = rightPosition(element)

                        contents.each (index, element) ->
                            return if index == 0
                            contentsWidth += angular.element(element).width() + 2
                        #Enable/Disable menuTabs
                        if containerWidth - 40 < contentsWidth
                            $scope.menuTabsEnabled = true
                        else
                            $scope.menuTabsEnabled = false
                            $scope.showingMenu = false
                        #Update limitTabs value
                        if $scope.activeIndex() == $scope.tabs.length - 1
                            $scope.limitTabs = $scope.activeIndex() + 1

                        return if !$scope.menuTabsEnabled

                        #Check if active tab is on the left
                        if elementPosition < containerPosition.left
                            for a, i in container.find('li')
                                continue if i == 0
                                rtElement = (rightPosition(angular.element(a))) - (containerPosition.left-elementPosition)
                                $scope.limitTabs = i-1
                                if rtContainer + 40 > rtElement
                                    break
                            newleft = containerPosition.left-elementPosition
                            actualElement = element
                            acumWidth = element.outerWidth() + 2 - widthDeletedTab
                            while acumWidth + actualElement.next().outerWidth() + 2 < containerWidth - 40
                                actualElement = actualElement.next()
                                acumWidth += actualElement.outerWidth() + 2

                            newleft += containerWidth - 40 - acumWidth
                            #Check if active tab is on the right
                        else if rtContainer + 40 > rtElement + 2
                            $scope.limitTabs = $scope.activeIndex() + 1
                            newleft = 2 + rtElement + widthDeletedTab - rtContainer - 40
                            #Active tab is shown
                        else
                            if deletedTab && contents[0]?
                                actualElement = element
                                cont = 0
                                while actualElement.next().length != 0 && rightPosition(actualElement.next()) + 2 > rtContainer - 40
                                    actualElement = actualElement.next()
                                    cont++
                                newleft = rightPosition(actualElement) - rtContainer - 40 - 2
                                if newleft < 0
                                    newleft = 0
                                else
                                    $scope.limitTabs = $scope.activeIndex() + (cont+1)

                        contentsWidth = if deletedTab then - widthDeletedTab - 2 else 0
                        for e, i in contents
                            if i > $scope.limitTabs+1
                                break
                            contentsWidth += angular.element(e).outerWidth() + 2

                        moveLeft = actualLeft + Math.floor(newleft)

                        if moveLeft < (contentsWidth - (containerWidth - 40)) * -1
                            moveLeft = ((contentsWidth - (containerWidth - 40)) * -1)

                        if moveLeft > 0
                            container.css "left", "0px"
                        else
                            container.css "left", moveLeft + "px"
                        return
        )
]
