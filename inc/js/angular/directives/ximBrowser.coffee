angular.module('ximdex.common.filter').filter "nodeSelected", () ->
    (input, arr) ->
        for a in arr
            return true if (!a.nodeFrom? && !a.nodeTo? && !input.nodeFrom? && !input.nodeTo? && a.nodeid == input.nodeid) | (a.nodeFrom? && a.nodeTo? && input.nodeFrom? && input.nodeTo? && a.nodeFrom == input.nodeFrom && a.nodeTo == input.nodeTo)
        return false

angular.module("ximdex.common.directive").directive "ximBrowser", [
    "xTranslate", "$window", "$http"
    "xUrlHelper", "xMenu", "$document", "$timeout", "$q", "xTabs", "$rootScope"
    (xTranslate, $window, $http, xUrlHelper, xMenu, $document, $timeout, $q, xTabs, $rootScope) ->
        base_url = $window.X.baseUrl
        return (
            templateUrl: base_url+'/inc/js/angular/templates/ximBrowser.html'
            restrict: "E"
            replace: true
            scope:
                xid: "@ximId"
                mode: "@ximMode"
            controller: ["$scope", ($scope) ->
                if $scope.mode == "sidebar"
                    delete Hammer.defaults.cssProps.userSelect
                    Hammer.defaults.touchAction = "pan-y"
                    dragStartPosition=0
                    $scope.expanded = true
                    size = 0
                    listenHidePanel = true
                    #Nodes for ccenter tab
                    $scope.ccenter = null
                    #Nodes for project tab
                    $scope.modules = null
                    #Tab selected in the sidebar
                    $scope.selectedTab = 1

                $scope.filter = ''

                #Nodes for project tab
                $scope.projects = null
                #Initial node for list view
                $scope.initialNodeList = null
                #The path to $scope.initialNodeList in array format
                $scope.breadcrumbs = []

                #if true, TreeView is displayed else ListView
                $scope.treeMode = true

                #Current selected nodes
                $scope.selectedNodes = []

                #Indicates the filter status
                $scope.filterMode = false

                canceler = $q.defer()

                actualFilter = ""

                #Load initial values
                $http.get(xUrlHelper.getAction(
                    action: "browser3"
                    method: "read"
                    id: "10000"
                )).success (data) ->
                    if data
                        $scope.projects = data
                        $scope.projects.showNodes = true
                    data = null
                    return

                ###
                    ONLY ON SIDEBAR MODE
                ###

                if $scope.mode == "sidebar"
                    #Load a new action in a new tab for some nodes
                    loadAction = (action, nodes) ->
                        xTabs.pushTab action, nodes
                        return

                    $http.get(xUrlHelper.getAction(
                        action: "browser3"
                        method: "read"
                        id: "2"
                    )).success (data) ->
                        $scope.ccenter = data  if data
                        data = null
                        return

                    $http.get(xUrlHelper.getAction(
                        action: "moduleslist"
                        method: "readModules"
                    )).success (data) ->
                        $scope.modules = data  if data
                        data = null
                        return

                    #Load the actions of a node and opens a context menu. It does a request if the actions aren't in cache.
                    $scope.loadActions = (node, event) ->
                        $scope.select node, event
                        return if !$scope.selectedNodes[0].nodeid? | !$scope.selectedNodes[0].nodetypeid? | $scope.selectedNodes[0].nodeid == "0"
                        nodeToSearch = $scope.selectedNodes[0].nodeid
                        if $scope.selectedNodes.length > 1
                            for n in $scope.selectedNodes[1..]
                                if $scope.selectedNodes[0].nodetypeid != n.nodetypeid
                                    return
                                else
                                    nodeToSearch += "-#{n.nodeid}"
                        if not $window.com.ximdex.nodeActions[nodeToSearch]?
                            $http.get(xUrlHelper.getAction(
                                action: "browser3"
                                method: "cmenu"
                                nodes: $scope.selectedNodes
                            )).success (data) ->
                                if data
                                    $window.com.ximdex.nodeActions[nodeToSearch] = data
                                    postLoadActions(data, event, $scope.selectedNodes)
                                return
                        else
                            data = $window.com.ximdex.nodeActions[nodeToSearch]
                            postLoadActions(data, event, $scope.selectedNodes)
                        return false

                    postLoadActions = (data, event, selectedNodes) ->
                        return if data.length == 0
                        if event.pointers?
                            data.left = event.pointers[0].clientX + (if $window.document.documentElement.scrollLeft then $window.document.documentElement.scrollLeft else $window.document.body.scrollLeft)
                            data.top = event.pointers[0].clientY + (if $window.document.documentElement.scrollTop then $window.document.documentElement.scrollTop else $window.document.body.scrollTop)
                        if event.clientX
                            data.left = event.clientX + (if $window.document.documentElement.scrollLeft then $window.document.documentElement.scrollLeft else $window.document.body.scrollLeft)
                            data.top = event.clientY + (if $window.document.documentElement.scrollTop then $window.document.documentElement.scrollTop else $window.document.body.scrollTop)
                        #if event.type == "tap"
                        xMenu.open data, selectedNodes, loadAction
                        data = null
                        return

                    #Catches event dragstart on resizer bar
                    $scope.dragStart = (event) ->
                        if $scope.expanded && !isPanelHide
                            dragStartPosition = angular.element('#angular-tree').width()
                            angular.element('body').addClass 'noselect'
                        return

                    #Catches event drag on resizer bar
                    $scope.drag = (e,width) ->
                        if $scope.expanded && !isPanelHide
                            x = e.deltaX + dragStartPosition
                            x = $document.width()-17  if  x > $document.width()-17
                            x = 270  if x < 270
                            angular.element(e.target).css left: x + "px"
                            angular.element('#angular-tree').css width: x + "px"
                            angular.element('#angular-content').css left: (x + parseInt(width)) + "px"
                            x = null
                        return

                    $scope.dragEnd = () ->
                        if $scope.expanded && !isPanelHide
                            angular.element('body').removeClass 'noselect'
                            if $window.getSelection
                                if $window.getSelection().empty
                                    # Chrome
                                    $window.getSelection().empty()
                                else if $window.getSelection().removeAllRanges
                                    # Firefox
                                    $window.getSelection().removeAllRanges()
                            else if document.selection
                                # IE?
                                document.selection.empty()
                            $rootScope.$broadcast('updateTabsPosition')
                        return


                    #Toggle autohide on sidebar
                    $scope.toggleTree = () ->

                        if $scope.expanded
                            $scope.hideTree()
                        else
                            $scope.showTree()
                        button = angular.element('#angular-tree-toggle')
                        button.toggleClass "tie"
                        $scope.expanded = !$scope.expanded
                        return

                    postShowHidePanel = () ->
                        listenHidePanel = !listenHidePanel
                        $rootScope.$broadcast('updateTabsPosition')

                    firstHide = true
                    isPanelHide = false
                    $scope.hideTree = () ->
                        if listenHidePanel
                            isPanelHide = true
                            size = angular.element('#angular-tree').width()
                            button = angular.element('#angular-tree-toggle')
                            button.addClass "hide"
                            button.addClass "hideable"
                            angular.element('#angular-tree').addClass "hideable"
                            angular.element('#angular-content').addClass "hideable"
                            a=7
                            b=10+a
                            angular.element('#angular-tree').css left: (-size-7) + "px"
                            angular.element('#angular-content').css left: (b-7) + "px"
                            a = b = null
                            $timeout(
                                postShowHidePanel
                            ,
                                500
                            )
                            if firstHide
                                firstHide = false
                                content = document.getElementById('angular-content')
                                hm_content = new Hammer(content)
                                hm_content.on "swiperight", $scope.showTree

                        return

                    $scope.showTree = () ->
                        if !listenHidePanel
                            angular.element('#angular-tree').css left: 0 + "px"
                            angular.element('#angular-content').css left: (size+10+7) + "px"
                            button = angular.element('#angular-tree-toggle')
                            button.removeClass "hide"
                            $timeout(
                                () ->
                                    postShowHidePanel()
                                    isPanelHide = false
                                    angular.element('#angular-tree').removeClass "hideable"
                                    angular.element('#angular-content').removeClass "hideable"
                                    button.removeClass "hideable"
                            ,
                                500
                            )
                        return

                    $scope.openModuleAction = (node) ->
                        action =
                            command: "moduleslist"
                            name: node.name
                            method: "opentab"
                            params: [
                                modsel: node.name
                            ]
                        nodes = [
                            nodeid: node.id
                        ]
                        xTabs.pushTab action, nodes
                        return

                    allowedHotkey = true
                    $scope.$parent.keydown = (event) ->
                        return if !allowedHotkey
                        if event.altKey && event.ctrlKey && event.keyCode == 73 && $scope.selectedNodes.length > 0
                            action =
                                command: 'infonode'
                                method: 'index'
                                name: _("Node Info")
                            for n in $scope.selectedNodes
                                xTabs.pushTab action, [n] if n.nodeid? && n.nodeid != "0"
                            allowedHotkey = false
                            event.stopPropagation();
                            event.preventDefault();
                        return
                    $scope.$parent.keyup = (event) ->
                        allowedHotkey = true if !allowedHotkey
                        return

                ###
                    FINISH ONLY ON SIDEBAR MODE
                ###

                #Open/Close a node in the TreeView
                $scope.toggleNode = (node,event) ->
                    #event.preventDefault()
                    if node.isdir == "0"
                        action =
                            command: 'infonode'
                            method: 'index'
                            name: _("Node Info")
                        loadAction action, [node]
                        return
                        ###if not $window.com.ximdex.nodeActions[node.nodeid]?
                            $http.get(xUrlHelper.getAction(
                                action: "browser3"
                                method: "cmenu"
                                nodes: $scope.selectedNodes
                            )).success (data) ->
                                if data
                                    $window.com.ximdex.nodeActions[node.nodeid] = data
                                    loadAction data[0], [node]
                                return
                        else
                            data = $window.com.ximdex.nodeActions[node.nodeid]
                            loadAction data[0], [node]
                        return###
                    node.showNodes = not node.showNodes

                    $scope.loadNodeChildren node  if node.showNodes and not node.collection
                    return


                postLoadNodeChildren = (data,callback,node) ->
                    node.loading = false
                    if data and data.collection? and data.collection.length>0
                        node.collection = data.collection
                        node.children = data.children
                        node.state = data.state
                        if $scope.treeMode == false && $scope.selectedTab == 1
                            $scope.initialNodeList = node
                        callback node.collection  if callback
                        callback = null
                    $scope.initialNodeList = node
                    prepareBreadcrumbs()
                    data = null
                    cancel = null
                    return

                #Load the children of a node. It can execute a callback function later
                $scope.loadNodeChildren = (node, callback) ->
                    if node.loading | node.isdir == "0"
                        if $scope.treeMode == false
                            $scope.initialNodeList = node
                            prepareBreadcrumbs()
                        return
                    node.loading = true
                    node.showNodes = true
                    canceler.resolve()
                    canceler = $q.defer()
                    if $scope.filterMode and $scope.selectedTab == 1
                        node.collection = []
                        url=xUrlHelper.getAction(
                                action: "browser3"
                                method: "readFiltered"
                                id: node.nodeid
                            ) + "&query=" + actualFilter
                        $http.get(url, {timeout: canceler.promise}).success(
                            (data) ->
                                postLoadNodeChildren(data,callback,node)
                        ).error (data) ->
                            node.loading = false
                            cancel = null
                            return

                    else
                        maxItemsPerGroup = parseInt($window.com.ximdex.preferences.MaxItemsPerGroup)
                        fromTo = ""
                        idToSend = node.nodeid
                        if node.nodeid == "0" && node.startIndex? && node.endIndex?
                            fromTo = "&from=#{node.startIndex}&to=#{node.endIndex}"
                            idToSend = node.parentid
                        $http.get(xUrlHelper.getAction(
                            action: "browser3"
                            method: "read"
                            id: idToSend
                        )+"&items=#{maxItemsPerGroup}"+fromTo, {timeout: canceler.promise}).success(
                            (data) ->
                                postLoadNodeChildren(data,callback,node)
                        ).error (data) ->
                            node.loading = false
                            cancel = null
                            return
                        idToSend = null
                        fromTo = null
                        maxItemsPerGroup = null
                    return

                #Set a node as selected
                $scope.select = (node,event) ->
                    ctrl = if event.srcEvent? then event.srcEvent.ctrlKey else event.ctrlKey
                    if ctrl
                        for k, n of $scope.selectedNodes
                            if (!n.nodeFrom? && !node.nodeFrom? && !n.nodeTo? && !node.nodeTo? && n.nodeid == node.nodeid) | (n.nodeFrom? && node.nodeFrom? && n.nodeTo? && node.nodeTo? && n.nodeFrom == node.nodeFrom && n.nodeTo == node.nodeTo)
                                $scope.selectedNodes.splice k, 1 if (event.button? && event.button == 0) || (event.srcEvent? && event.srcEvent.button == 0)
                                return
                        pushed = false
                        for k, n of $scope.selectedNodes
                            if n.nodeid > node.nodeid
                                $scope.selectedNodes.splice k, 0, node
                                pushed = true
                                break
                        if !pushed
                            $scope.selectedNodes.splice $scope.selectedNodes.length, 0, node
                    else
                        $scope.selectedNodes = [node]
                    ctrl = null
                    return

                #Reloads the children of a node
                $scope.reloadNode = (nodeId, callback) ->
                    if nodeId?
                        n = findNodeById nodeId, $scope.projects
                        n = findNodeById nodeId, $scope.ccenter if n == null
                        return if n == null
                    else if $scope.selectedNodes.length == 1
                        n = $scope.selectedNodes[0]
                    else
                        return
                    if n.isdir == "0"
                        action =
                            command: 'infonode'
                            method: 'index'
                            name: _("Node Info")
                        loadAction action, [n]
                        return
                        ### Open the first action in menu
                        if not $window.com.ximdex.nodeActions[n.nodeid]?
                            $http.get(xUrlHelper.getAction(
                                action: "browser3"
                                method: "cmenu"
                                nodes: $scope.selectedNodes
                            )).success (data) ->
                                if data
                                    $window.com.ximdex.nodeActions[n.nodeid] = data
                                    loadAction data[0], [n]
                                return
                        else
                            data = $window.com.ximdex.nodeActions[n.nodeid]
                            loadAction data[0], [n]
                        return
                        ###

                    n.showNodes = true
                    n.collection = []

                    $scope.loadNodeChildren n, callback

                $scope.navigateToNodeId = (nodeId) ->
                    return if !nodeId?
                    $http.get(xUrlHelper.getAction(
                        method: "getTraverseForPath"
                        id: nodeId
                        options: [
                            ajax: "json"
                        ]
                    )).success (data) ->
                        postNavigateToNodeId(data)
                    return

                postNavigateToNodeId = (data) ->
                    nodeList = data['nodes']
                    shifted = nodeList.shift()
                    if shifted?
                        $scope.reloadNode shifted.nodeid, callback
                    else
                        n = findNodeById nodeId, $scope.projects
                        n = findNodeById nodeId, $scope.ccenter if n == null
                        return if n == null
                        $scope.select n
                    data = null
                    nodeList = null

                #Search nodes with a filter
                $scope.doFilter = () ->
                    if $scope.filter.length>2 and $scope.filter.match /^[\d\w_\.-]+$/i
                        actualFilter = $scope.filter
                        $scope.filterMode = true
                        $scope.projects.showNodes = true
                        $scope.projects.collection = []
                        $scope.loadNodeChildren $scope.projects
                    else if actualFilter != ""
                        actualFilter = ""
                        $scope.filterMode = false
                        $scope.projects.showNodes = true
                        $scope.projects.collection = []
                        $scope.loadNodeChildren $scope.projects
                    $scope.selectedNodes = []
                    return

                $scope.clearFilter = () ->
                    if $scope.filter != ''
                        $scope.filter = ''
                        $scope.doFilter()
                    return

                #Toggles Treeview/ListView
                $scope.toggleView = () ->
                    $scope.treeMode = !$scope.treeMode
                    if $scope.treeMode == false && $scope.selectedTab == 1
                        if $scope.selectedNodes.length > 0 && $scope.selectedNodes[0].path.slice(0,16) == "/Ximdex/Projects"
                            $scope.loadNodeChildren $scope.selectedNodes[0]
                        else
                            $scope.loadNodeChildren $scope.projects
                    return

                #Go to the selected node in the breadcrumbs
                $scope.goBreadcrums = (index) ->
                    pathToNode = $scope.breadcrumbs.slice 1, index + 1
                    actualNode = $scope.projects
                    nodeFound = false
                    while pathToNode.length > 0
                        nodeFound = false
                        for n, i in actualNode.collection
                            if (n.name == pathToNode[0] && $scope.filterMode == false) | (n.originalName == pathToNode[0] && $scope.filterMode == true)
                                actualNode = n
                                pathToNode.splice 0, 1
                                nodeFound = true
                                break
                        return if nodeFound == false
                    $scope.loadNodeChildren actualNode
                    return

                #Transform a node path to array for the breadcrumbs
                prepareBreadcrumbs = () ->
                    if $scope.initialNodeList.nodeid == "0"
                        path = getFolderPath $scope.initialNodeList.collection[0].path
                    else
                        path = $scope.initialNodeList.path
                    if path.slice(-1) == "/"
                        path = path.substring(0, path.length-1)
                    if path.slice(0,1) == "/"
                        path = path.substring(1, path.length)
                    b = path.split("/")
                    b.splice 0, 1
                    $scope.breadcrumbs = b
                    if $scope.initialNodeList.isdir == "0"
                        $scope.goBreadcrums b.length - 2
                    return

                #Gets the folder path of a path
                getFolderPath = (path) ->
                    n = path.lastIndexOf "/"
                    return path.substring 0, n if n>0
                    return path

                findNodeById = (nodeId, source) ->
                    queue = [source]
                    while queue.length > 0
                        item = queue.pop()
                        if item.nodeid == nodeId
                            return item
                        else
                            if item.collection?
                                for i in item.collection
                                    queue.push i
                    return null

                $scope.$on 'nodemodified', (event, nodeId) ->
                    node = findNodeById nodeId, $scope.projects
                    node = findNodeById nodeId, $scope.ccenter if node == null
                    return if node == null
                    return if node.isdir == "0"
                    $scope.selectedNodes = []
                    node.showNodes = true
                    node.collection = []
                    $scope.loadNodeChildren node
            ]

        )
]