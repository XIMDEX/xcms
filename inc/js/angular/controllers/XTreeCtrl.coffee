###*
\details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]

Ximdex a Semantic Content Management System (CMS)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published
by the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

See the Affero GNU General Public License for more details.
You should have received a copy of the Affero GNU General Public License
version 3 along with Ximdex (see LICENSE file).

If not, visit http://gnu.org/licenses/agpl-3.0.html.

@author Ximdex DevTeam <dev@ximdex.com>
@version $Revision$
###
angular.module("ximdex.main.controller").controller "XTreeCtrl", [
    "$scope", "$attrs", "xBackend"
    "xTranslate", "$window", "$http"
    "xUrlHelper", "xMenu", "$document", "$timeout", "$q"
    ($scope, $attrs, xBackend, xTranslate, $window, $http, xUrlHelper, xMenu, $document, $timeout, $q) ->

        $scope.projects = null
        $scope.ccenter = null
        $scope.modules = null

        $scope.nodeActions = []
        $scope.selectedNodes = []
        $scope.selectedTab = 1
        dragStartPosition=0;
        expanded = true
        size = 0
        listenHidePanel = true

        canceler = $q.defer()

        loadAction = (action, nodes) ->
            console.log "LOADING", action
            ###openAction(
                label: action.name,
                name:  action.name,
                command: action.command,
                params: 'method='+action.command+'&nodeid='+node.nodeid,
                nodes: node.nodeid,
                url: X.restUrl + '?action='+action.command+'&nodes[]='+node.nodeid+'&nodeid='+node.nodeid,
                bulk: action.bulk
            ,
                node.nodeid
            )
            $('#bw1').browserwindow(
                'openAction'
            ,
                label: action.name,
                name:  action.name,
                command: action.command,
                params: 'method='+action.command+'&nodeid='+node.nodeid,
                nodes: node.nodeid,
                url: X.restUrl + '?action='+action.command+'&nodes[]='+node.nodeid+'&nodeid='+node.nodeid,
                bulk: action.bulk
            ,
                node.nodeid
            )###

            return

        $scope.twoLevelLoad = true
        $http.get(xUrlHelper.getAction(
            action: "browser3"
            method: "nodetypes"
        )).success (data) ->
            if data and data.nodetypes
                $scope.nodetypes = data.nodetypes
                $scope.nodetypes = {}
                i = data.nodetypes.length - 1

                while i >= 0
                    $scope.nodetypes[data.nodetypes[i].idnodetype] = data.nodetypes[i]
                    i--
            return


        #TODO: Get initial nodeid from backend
        $http.get(xUrlHelper.getAction(
            action: "browser3"
            method: "read"
            id: "10000"
        )).success (data) ->
            if data
                $scope.projects = data
                $scope.projects.showNodes = true
            return

        $http.get(xUrlHelper.getAction(
            action: "browser3"
            method: "read"
            id: "2"
        )).success (data) ->
            $scope.ccenter = data  if data
            return

        $http.get(xUrlHelper.getAction(
            action: "moduleslist"
            method: "readModules"
        )).success (data) ->
            $scope.modules = data  if data
            return

        $scope.toggleNode = (node,event) ->
            event.preventDefault()
            node.showNodes = not node.showNodes
            $scope.loadChilds node  if node.showNodes and not node.collection
            return

        $scope.loadChilds = (node) ->
            $scope.loadNodeChilds node, (nodes) ->
                $scope.loadNodesChilds nodes  if $scope.twoLevelLoad
                return

            return

        $scope.loadNodeChilds = (node, callback) ->
            if node.children and not node.loading
                maxItemsPerGroup = parseInt($window.com.ximdex.preferences.MaxItemsPerGroup)
                fromTo = ""
                idToSend = node.nodeid
                if node.nodeid == "0" && node.startIndex? && node.endIndex?
                    fromTo = "&from=#{node.startIndex}&to=#{node.endIndex}"
                    idToSend = node.parentid
                node.loading = true
                $http.get(xUrlHelper.getAction(
                    action: "browser3"
                    method: "read"
                    id: idToSend
                )+"&items=#{maxItemsPerGroup}"+fromTo).success((data) ->
                    node.loading = false
                    if data
                        node.collection = data.collection
                        callback node.collection  if callback
                    return
                ).error (data) ->
                    node.loading = false
                    return

            return

        $scope.loadNodesChilds = (nodes) ->
            if nodes.length < 10
                i = nodes.length - 1

                while i >= 0
                    $scope.loadNodeChilds nodes[i]
                    i--
            return

        $scope.loadActions = (node,event) ->
            return if event.target.classList[0] == "xim-actions-dropdown" && event.type == "press"
            event.srcEvent?.stopPropagation()
            event.stopPropagation?()
            #event.preventDefault?()

            $scope.select node, event
            return if !$scope.selectedNodes[0].nodeid? | !$scope.selectedNodes[0].nodetypeid? | $scope.selectedNodes[0].nodeid == "0"
            nodeToSearch = $scope.selectedNodes[0].nodeid
            if $scope.selectedNodes.length > 1
                for n in $scope.selectedNodes[1..]
                    if $scope.selectedNodes[0].nodetypeid != n.nodetypeid
                        return
                    else
                        nodeToSearch += "-#{}"
            if not $scope.nodeActions[nodeToSearch]?
                $http.get(xUrlHelper.getAction(
                    action: "browser3"
                    method: "cmenu"
                    nodes: $scope.selectedNodes
                )).success (data) ->
                    if data
                        $scope.nodeActions[nodeToSearch] = data
                        if event.pointers?
                            data.left = event.pointers[0].clientX + (if $window.document.documentElement.scrollLeft then $window.document.documentElement.scrollLeft else $window.document.body.scrollLeft)
                            data.top = event.pointers[0].clientY + (if $window.document.documentElement.scrollTop then $window.document.documentElement.scrollTop else $window.document.body.scrollTop)
                        data.expanded = "true"
                        if event.clientX
                            data.left = event.clientX + (if $window.document.documentElement.scrollLeft then $window.document.documentElement.scrollLeft else $window.document.body.scrollLeft)
                            data.top = event.clientY + (if $window.document.documentElement.scrollTop then $window.document.documentElement.scrollTop else $window.document.body.scrollTop)
                        if event.type == "tap"
                            data.expanded = "false"
                        xMenu.open data, $scope.selectedNodes, loadAction
                    return
            else
                data = $scope.nodeActions[nodeToSearch]
                if event.pointers?
                    data.left = event.pointers[0].clientX + $window.document.body.scrollLeft
                    data.top = event.pointers[0].clientY + $window.document.body.scrollTop
                data.expanded = "true"
                if event.clientX
                    data.left = event.clientX + $window.document.body.scrollLeft
                    data.top = event.clientY + $window.document.body.scrollTop
                if event.type == "tap"
                    data.expanded = "false"
                xMenu.open data, $scope.selectedNodes, loadAction

            return false

        $window.com.ximdex.emptyActionsCache = () ->
            $scope.nodeActions = []
            return

        $scope.select = (node,event) ->
            if event.ctrlKey
                for k, n of $scope.selectedNodes
                    if (!n.nodeFrom? && !node.nodeFrom? && !n.nodeTo? && !node.nodeTo? && n.nodeid == node.nodeid) | (n.nodeFrom? && node.nodeFrom? && n.nodeTo? && node.nodeTo? && n.nodeFrom == node.nodeFrom && n.nodeTo == node.nodeTo)
                        $scope.selectedNodes.splice k, 1 if event.button == 0
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
            return

        $scope.reloadNode = () ->
            if $scope.selectedNodes.length == 1
                $scope.selectedNodes[0].showNodes = true
                $scope.selectedNodes[0].collection = []
                $scope.loadChilds $scope.selectedNodes[0]

        $scope.doFilter = () ->
            if $scope.filter == ""
                canceler.resolve()
                canceler = $q.defer()
                $scope.projects.collection = []
                $scope.projects.loading = true
                $scope.projects.showNodes = true
                query = $http.get(xUrlHelper.getAction(
                    action: "browser3"
                    method: "read"
                    id: "10000"
                ), {timeout: canceler.promise}).success (data) ->
                    if data
                        $scope.projects = data
                        $scope.projects.showNodes = true
                    query = null
                    return
            else if $scope.filter.length>2
                canceler.resolve()
                canceler = $q.defer()
                $scope.projects.collection = []
                $scope.projects.loading = true
                $scope.projects.showNodes = true
                url=xUrlHelper.getAction(
                        action: "browser3"
                        method: "readFiltered"
                        id: "10000"
                    ) + "&query=" + $scope.filter
                query = $http.get(url, {timeout: canceler.promise}).success (data) ->
                    $scope.projects = data  if data
                    cancel = null
                    return
            $scope.selectedNodes = []
            return

        $scope.dragStart = (event) ->
            if expanded
                dragStartPosition = angular.element('#angular-tree').width()

        $scope.drag = (e,width) ->
            if expanded
                x = e.deltaX + dragStartPosition
                x = $document.width()-17  if  x > $document.width()-17
                x = 220  if x < 220
                angular.element(e.target).css left: x + "px"
                angular.element('#angular-tree').css width: x + "px"
                angular.element('#angular-content').css left: (x + parseInt(width)) + "px"
                return true

        $scope.toggleTree = (e) ->
            angular.element(e.target).toggleClass "hide"
            angular.element(e.target).toggleClass "tie"
            angular.element('#angular-tree').toggleClass "hideable"
            angular.element('#angular-content').toggleClass "hideable"
            angular.element(e.target).toggleClass "hideable"
            expanded = !expanded
            size = angular.element('#angular-tree').width()
            if !expanded
                $scope.hideTree()

        $scope.hideTree = () ->
            if !expanded && listenHidePanel
                a=7
                b=10+a
                angular.element('#angular-tree').css left: (-size-7) + "px"
                angular.element('#angular-content').css left: (b-7) + "px"
                $timeout(
                    () ->
                        listenHidePanel = false
                ,
                    500
                )
            return

        $scope.showTree = () ->
            if !expanded && !listenHidePanel
                angular.element('#angular-tree').css left: 0 + "px"
                angular.element('#angular-content').css left: (size+10+7) + "px"
                $timeout(
                    () ->
                        listenHidePanel = true
                ,
                    500
                )
            return
]

angular.module("ximdex.main.controller").filter "nodeSelected", () ->
    (input, arr) ->
        for a in arr
            return true if (!a.nodeFrom? && !a.nodeTo? && !input.nodeFrom? && !input.nodeTo? && a.nodeid == input.nodeid) | (a.nodeFrom? && a.nodeTo? && input.nodeFrom? && input.nodeTo? && a.nodeFrom == input.nodeFrom && a.nodeTo == input.nodeTo)
        return false
