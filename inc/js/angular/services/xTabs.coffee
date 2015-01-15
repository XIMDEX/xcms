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

        return {
            activeIndex: () -> return activeTab
            loadCssAndJs: (content) ->
                cssArr = []
                angular.element(content).first().children().each (index, item) ->
                    cssArr.push angular.element(item).html()
                    return
                Object.loadCss cssArr
                ###jsArr = []
                angular.element(content).first().next().children().each (index, item) ->
                    jsArr.push angular.element(item).html()
                    return

                jsObj =
                    onComplete: _onAssetsCompleted()
                    js: jsArr
                if jsArr.length > 0
                    Object.loadScript jsObj
                else
                    this._onAssetsCompleted()###
                return
            pushTab: (action, nodes) ->
                newid = ""
                for n in nodes
                    newid += n.nodeid + "_"
                newid += action.command
                for tab, i in tabs
                    if tab.id == newid
                        this.highlightTab i
                        return
                that = this
                $http.get(xUrlHelper.getAction(
                    action: action.command
                    nodes: nodes
                    module: action.module
                )).success (data) ->
                    if data
                        newtab =
                            id: newid
                            name: action.name
                            content_untrusted: data
                            content: $sce.trustAsHtml(data)
                            nodes: nodes
                            command: action.command
                            blink: false
                            show: true
                        that.loadCssAndJs data
                        newlength = tabs.push(newtab)
                        that.setActive newlength-1
                        $timeout(
                            () ->
                                $rootScope.$broadcast('onModifyTabs')
                        ,
                            0
                        )

                    return
                return
            getTabs: () ->
                return tabs
            removeTab: (index) ->
                visitedIndex = visitedTabs.indexOf index
                if visitedIndex >= 0
                    visitedTabs.splice visitedIndex, 1
                    for tab, i in visitedTabs
                        if visitedTabs[i] > index
                            visitedTabs[i] = visitedTabs[i]-1
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
                ###$timeout(
                    () ->
                        $rootScope.$broadcast('onModifyTabs')
                ,
                    400
                )###
                return
            setActive: (index) ->
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
            highlightTab: (index) ->
                return if tabs[index].blink == true
                tabs[index].blink = true
                $timeout(
                    () ->
                        tabs[index].blink = false
                    ,
                        2000
                )
            closeAll: () ->
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
            offAll: () ->
                activeTab = -1
                return
        }




]