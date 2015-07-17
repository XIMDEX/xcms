#Service to control the tabs
angular.module("ximdex.common.service").factory "xTabs", ["$window", "$timeout", "$http",
                                                          "xUrlHelper", "$rootScope", "$compile",
                                                          "angularLoad"
    ($window, $timeout, $http, xUrlHelper, $rootScope, $compile, angularLoad) ->

        scopeWelcomeTab = null

        #Array of current tabs
        tabs = []
        #Visited tabs history
        visitedTabs = []
        #The index of the currently open tab
        activeIndex = -1

        xtab = {}

        # Bind the jQuery form events for a tab
        #
        # @private
        # @param tab [Tab object] The tab object
        # @return [Integer] The index of the tab
        bindFormEvents = ( tab) ->
            forms = angular.element("form","#"+tab.id+"_content")
            if forms.length == 0
                new X.FormsManager(
                    actionView:
                        action: tab.action
                    tabId: tab.id
                    actionContainer: angular.element("#"+tab.id+"_content")
                )
            else
                for form, i in forms
                    new X.FormsManager(
                        actionView:
                            action: tab.action
                        tabId: tab.id
                        actionContainer: angular.element("#"+tab.id+"_content")
                        form: angular.element(form)
                    )
            gobackButton = angular.element('fieldset.buttons-form .goback-button',"#"+tab.id+"_content")
            gobackButton.bind "click", () ->
                tab.history.pop()
                tab.url = tab.history[tab.history.length - 1]
                xtab.reloadTabById(tab.id)
            return



        # Gets the index of a tab
        #
        # @param tabId [String] The id of the tab
        # @return [Integer] The index of the tab or -1 if tab doesn't exist
        #
        xtab.getTabIndex = (tabId) ->
            for tab, i in tabs
                return i if tab.id == tabId
            return -1


        # Submit a form
        #
        # @param args.url [String] The url of the action
        # @param args.reload [Boolean] Indicates if it is a reload request
        # @param args.data [String] The data request
        # @param args.idTab [String] The tab id
        # @param args.callback [Function] A function to be executed at the end
        #
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
                    tabs[index].history.push(args.url)
                    tabs[index].url = args.url
                    if args.reload == true
                        tabs[index].content = data
                        xtab.loadCssAndJs tabs[index]
                    args.callback({data: data, tab: tabs[index]}) if args.callback
                return
            ).error (error) ->
                args.callback({error: true}) if args.callback
                return
            return



        postLoadJs = (tab, nodeids) ->
            container = angular.element("#"+tab.id+"_content")

            if tab.id != "10000_welcome"
                scope = container.scope()
            else
                if scopeWelcomeTab?
                    scopeWelcomeTab.$destroy()
                scope = container.scope().$new()

            compiled = $compile(tab.content)(scope)
            if tab.id == "10000_welcome"
                scopeWelcomeTab = scope
            container.html(compiled)
            bindFormEvents(tab)
            $window.com.ximdex.triggerActionLoaded(
                title: "#" + tab.id + "_tab"
                context: "#"+tab.id+"_content",
                url: tab.url,
                action: tab.action,
                nodes: nodeids,
                tab: tab
            )


        postLoadCssAndJs = (tab) ->
            cssArr = []
            content = angular.element(tab.content)
            content.first().children().each (index, item) ->
                cssArr.push angular.element(item).html()
                return
            #Object.loadCss cssArr
            for css in cssArr
                angularLoad.loadCSS(css)
            jsArr = []
            content.first().next().children().each (index, item) ->
                jsArr.push angular.element(item).html()
                return
            nodeids = []
            for n in tab.nodes
                nodeids.push n.nodeid

            cont = 0
            callback = () ->
                if ++cont == jsArr.length
                    postLoadJs(tab, nodeids)
                return
            if jsArr.length > 0
                for js in jsArr
                    angularLoad.loadScript(js).then(->
                        callback()
                        return
                    ).catch ->
                        console.log "Error loading JS"
                        return
            else
                postLoadJs(tab, nodeids)

        # Loads the css and js of a tab. It triggers the window.com.ximdex.triggerActionLoaded event
        # with the following data:
        #   -title: the id of tab title
        #   -context: the id of tab content
        #   -url: the url used to load the tab
        #   -action: the action object
        #   -nodes: array of nodeids
        #   -tab: the tab object
        #
        # @param tab [Tab object] The tab object
        #
        xtab.loadCssAndJs = (tab) ->
            $timeout(
                () -> postLoadCssAndJs(tab)
            ,
                0
            )
            return

        # Pushes a new tab. It triggers the onModifyTabs event.
        #
        # @param action [Action object] The action object
        # @param nodes [Array] An array of node objects
        #

        xtab.pushTab = (action, nodes) ->
            newid = ""
            for n in nodes
                newid += n.nodeid + "_"
            newid += action.command
            for tab, i in tabs
                if tab.id == newid
                    xtab.setActiveTab i
                    xtab.highlightTab i
                    return
            url = xUrlHelper.getAction(
                action: action.command
                nodes: nodes
                module: action.module
                method: action.method
                options: action.params
            )
            $http.get(url).success (data) ->
                if data
                    newtab =
                        id: newid
                        name: action.name
                        content: data
                        nodes: nodes
                        action: action
                        command: action.command
                        blink: false
                        show: true
                        url: url
                        history: [url]
                    xtab.loadCssAndJs newtab
                    newlength = tabs.push(newtab)
                    ###$timeout(
                        () ->
                            $rootScope.$broadcast('updateTabsPosition')
                    ,
                        0
                    )###
                    xtab.setActiveTab newlength - 1

                return
            return

        # Return the current tabs.
        #
        # @return [Array] Array of tab objects
        #
        xtab.getTabs = () ->
            return tabs


        # Returns the index of the current active tab
        #
        # @return [Integer] The index of the active tab
        #
        xtab.activeIndex = () -> return activeIndex

        triggerUpdateTabsPosition = (deletedTab) ->
            if deletedTab?
                $rootScope.$broadcast('updateTabsPosition', deletedTab)
            else
                $rootScope.$broadcast('updateTabsPosition')

        # Removes a tab.
        #
        # @param index [Integer] The tab index
        #
        xtab.removeTab = (index) ->
            visitedIndex = visitedTabs.indexOf index
            if visitedIndex >= 0
                visitedTabs.splice visitedIndex, 1
                for tab, i in visitedTabs
                    if visitedTabs[i] > index
                        visitedTabs[i] = visitedTabs[i] - 1
            deletedTab = (tabs.splice index, 1)[0]
            if visitedTabs.length > 0
                activeIndex = visitedTabs[0]
                $timeout(
                    () ->
                        triggerUpdateTabsPosition(deletedTab)
                ,
                    0
                )
            else
                activeIndex = -1
            ###$timeout(
                () ->
                    $rootScope.$broadcast('updateTabsPosition')
            ,
                400
            )###
            return

        # Set a tab as active.
        #
        # @param index [Integer] The tab index
        #
        xtab.setActiveTab = (index) ->
            activeIndex = index
            visitedIndex = visitedTabs.indexOf index
            if visitedIndex >= 0
                visitedTabs.splice visitedIndex, 1
            visitedTabs.unshift index
            $timeout(
                triggerUpdateTabsPosition
            ,
                0
            )
            return

        # Highlights a tab (usually when we open a existing tab)
        #
        # @param index [Integer] The tab index
        #
        xtab.highlightTab = (index) ->
            return if tabs[index].blink == true
            tabs[index].blink = true
            $timeout(
                () ->
                    tabs[index].blink = false
            ,
                2000
            )

        # Closes all tabs
        xtab.closeAllTabs = () ->
            tabs.splice 0, tabs.length
            activeIndex = -1
            visitedTabs = []
            $timeout(
                triggerUpdateTabsPosition
            ,
                400
            )
            return

        # Sets all tabs as no disable
        xtab.offAllTabs = () ->
            activeIndex = -1
            return

        # Removes a tab
        #
        # @param tabId [String] The tab id
        #
        xtab.removeTabById = (tabId) ->
            index = xtab.getTabIndex tabId
            xtab.removeTab index if index >= 0

        # Reloads a tab
        #
        # @param index [Integer] The tab index
        #
        xtab.reloadTab = (index) ->
            tab = tabs[index]
            url = xUrlHelper.getAction(
                action: tab.action.command
                nodes: tab.nodes
                module: tab.action.module
                method: tab.action.method
                options: [
                    actionReload: true
                ]
            )
            $http.get(url).success (data) ->
                if data
                    tab.content = data
                    xtab.loadCssAndJs tab
                return
            return

        # Reloads a tab
        #
        # @param tabId [String] The tab id
        #
        xtab.reloadTabById = (tabId) ->
            index = xtab.getTabIndex tabId
            xtab.reloadTab index if index >= 0
            return

        xtab.setTabNode = (tabId,nodes) ->
            index = xtab.getTabIndex tabId
            if index >= 0
                tabs[index].nodes = nodes
            return

        xtab.setActiveTabById = (tabId) ->
            index = xtab.getTabIndex tabId
            xtab.setActiveTab index if index >= 0
            return

        xtab.getActiveTab = () ->
            return tabs[activeIndex] if activeIndex >= 0
            return null

        xtab.openAction = (action, nodes) ->
            nodesArray = []
            if Array.isArray nodes
                for n in nodes
                    newNode =
                        nodeid: n
                    nodesArray.push newNode
            else if nodes
                nodesArray.push {nodeid: nodes}
            xtab.pushTab(action, nodesArray)
            return

        return xtab
]