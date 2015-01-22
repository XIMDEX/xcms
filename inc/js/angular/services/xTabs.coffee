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
            actionView: this, hay que mirarlo
            browser: this.browser, liquidado
            context: this.content,
            url: this.url,
            action: action,
            nodes: this.nodes,
            tabId: this.tabId()
        })###

        xtab = {}

        xtab.getTabIndex = (tabId) ->
            for tab, i in tabs
                return i if tab.id == tabId
            return -1

        xtab.bindFormEvents = (indexTab, tab) ->
            $timeout(
                () ->
                    forms = angular.element("form","#"+tab.id+"_content")
                    for form, i in forms
                        fm = new X.FormsManager(
                            actionView:
                                action: tab.action
                            tabId: tab.id
                            actionContainer: angular.element("#"+tab.id+"_content"),
                            form: angular.element(form)
                        )
            ,
                0
            )
            return

        xtab.submitForm = (args) ->
            $http(
                url: args.url
                responseType: if args.reload then "" else "json"
                method: "POST"
                data: args.data
                headers:
                    "Content-Type": "application/x-www-form-urlencoded"
            ).success((data) ->
                if data
                    index = xtab.getTabIndex args.tabId
                    return if index < 0
                    if args.reload == true
                        tabs[index].content_untrusted = data
                        tabs[index].content = $sce.trustAsHtml(data)
                        xtab.loadCssAndJs tabs[index]
                        xtab.bindFormEvents index, tabs[index]
                    args.callback({data: data, tab: tabs[index]}) if args.callback
                return
            ).error (error) ->
                args.callback({error: true}) if args.callback
                return
            return

        #$rootScope.$on("onSubmitForm", xtab.submitForm);

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
                    title: "#" + tab.id + "_tab"
                    context: "#"+tab.id+"_content",
                    url: tab.url,
                    action: tab.action,
                    nodes: nodeids,
                    tab: tab
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
                method: action.method
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
                    xtab.bindFormEvents newlength - 1, newtab
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

        xtab.removeTabById = (tabId) ->
            index = xtab.getTabIndex tabId
            xtab.removeTab index if index >= 0

        xtab.reloadTab = (index) ->
            tab = tabs[tabId]
            url = xUrlHelper.getAction(
                action: tab.action.command
                nodes: tab.nodes
                module: tab.action.module
                method: tab.action.method
            )
            $http.get(url).success (data) ->
                if data
                    tab.content_untrusted = data
                    tab.content = $sce.trustAsHtml(data)
                    xtab.loadCssAndJs tab
                    xtab.bindFormEvents index, tab
                return
            return

        xtab.reloadTabById = (idTab) ->
            index = xtab.getTabIndex idTab
            xtab.reloadTab index if index >= 0
            return


        return xtab
]