###
   Service to control the tabs
### #

angular.module("ximdex.common.service").factory "xTabs", ["$window", "$timeout", "$http",
                                                          "xUrlHelper", "$sce", "$rootScope"
    ($window, $timeout, $http, xUrlHelper, $sce, $rootScope) ->
        tabs = []
        visitedTabs = []
        activeTab = -1

        ###$window.com.ximdex.triggerActionLoaded({
            actionView: this,
            browser: this.browser,
            context: this.content,
            url: this.url,
            action: action,
            nodes: this.nodes,
            tabId: this.tabId()
        })###

        xtab = {}

        #Returns the index of the active tab
        xtab.activeIndex = () -> return activeTab
        #Loads the css and js of a content
        xtab.loadCssAndJs = (tab) ->
            cssArr = []
            content = angular.element(tab.content_untrusted)
            content.first().children().each (index, item) ->
                cssArr.push angular.element(item).html()
                return
            Object.loadCss cssArr
            jsArr = []
            content.first().next().children().each (index, item) ->
                jsArr.push angular.element(item).html()
                return
            nodeids = []
            for n in tab.nodes
                nodeids.push n.nodeid

            callback = () ->
                $window.com.ximdex.triggerActionLoaded({
                    #actionView: this,
                    #browser: this.browser,
                    context: "#"+tab.id+"_content",
                    url: tab.url,
                    action: tab.action,
                    nodes: nodeids,
                    tabId: tabs.length + 1
                })

            jsObj =
                onComplete: callback
                js: jsArr
            if jsArr.length > 0
                Object.loadScript jsObj
            else
                callback()
            return
        ###
            Pushes a new tab
                action: object action
                nodes: array of nodes
        ###
        xtab.pushTab = (action, nodes) ->
            newid = ""
            for n in nodes
                newid += n.nodeid + "_"
            newid += action.command
            for tab, i in tabs
                if tab.id == newid
                    this.highlightTab i
                    return
            that = this
            url = xUrlHelper.getAction(
                action: action.command
                nodes: nodes
                module: action.module
            )
            $http.get(url).success (data) ->
                if data
                    newtab =
                        id: newid
                        name: action.name
                        content_untrusted: data
                        content: $sce.trustAsHtml(data)
                        nodes: nodes
                        action: action
                        command: action.command
                        blink: false
                        show: true
                        url: url
                    that.loadCssAndJs newtab
                    newlength = tabs.push(newtab)
                    that.setActive newlength - 1
                    $timeout(
                        () ->
                            $rootScope.$broadcast('onModifyTabs')
                    ,
                        0
                    )

                return
            return
        ###
            Returns the tabs
        ###
        xtab.getTabs = () ->
            return tabs
        xtab.removeTab = (index) ->
            visitedIndex = visitedTabs.indexOf index
            if visitedIndex >= 0
                visitedTabs.splice visitedIndex, 1
                for tab, i in visitedTabs
                    if visitedTabs[i] > index
                        visitedTabs[i] = visitedTabs[i] - 1
            tabs.splice index, 1
            if visitedTabs.length > 0
                activeTab = visitedTabs[0]
                $timeout(
                    () ->
                        $rootScope.$broadcast('onChangeActiveTab')
                ,
                    0
                )
            else
                activeTab = -1
            $timeout(
                () ->
                    $rootScope.$broadcast('onModifyTabs')
            ,
                400
            )
            return
        ###
            Set active a tab
                index: the index of the tab
        ###
        xtab.setActive = (index) ->
            activeTab = index
            visitedIndex = visitedTabs.indexOf index
            if visitedIndex >= 0
                visitedTabs.splice visitedIndex, 1
            visitedTabs.unshift index
            $timeout(
                () ->
                    $rootScope.$broadcast('onChangeActiveTab')
            ,
                0
            )
            return
        ###
            Highlights a tab (usually when we open a existing tab)
                index: the index of the tab
        ###
        xtab.highlightTab = (index) ->
            return if tabs[index].blink == true
            tabs[index].blink = true
            $timeout(
                () ->
                    tabs[index].blink = false
            ,
                2000
            )
        ###
            Closes all tabs
        ###
        xtab.closeAll = () ->
            tabs.splice 0, tabs.length
            activeTab = -1
            visitedTabs = []
            $timeout(
                () ->
                    $rootScope.$broadcast('onModifyTabs')
            ,
                400
            )
            return
        ###
            Deactivates all tabs
        ###
        xtab.offAll = () ->
            activeTab = -1
            return

        return xtab

]