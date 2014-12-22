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
    "xUrlHelper", "xMenu"
    ($scope, $attrs, xBackend, xTranslate, $window, $http, xUrlHelper, xMenu) ->

        $scope.nodetypeActions = []
        $scope.selectedNodes = []
        $scope.selectedTab = 1

        loadAction = (action, node) ->
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
            $scope.projects = data  if data
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

        $scope.toggleNode = (node) ->
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
                node.loading = true
                $http.get(xUrlHelper.getAction(
                    action: "browser3"
                    method: "read"
                    id: node.nodeid
                )).success((data) ->
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
            if not $scope.nodetypeActions[node.nodetypeid]?
                $http.get(xUrlHelper.getAction(
                    action: "browser3"
                    method: "cmenu"
                    id: node.nodeid
                )).success (data) ->
                    if data
                        $scope.nodetypeActions[node.nodetypeid] = data
                        data.left = event.clientX
                        data.top = event.clientY
                        if event.button == 2
                            data.expanded = "true"
                        else
                            data.expanded = "false"
                        xMenu.open data, node, loadAction
                    return
            else
                data = $scope.nodetypeActions[node.nodetypeid]
                data.left = event.clientX
                data.top = event.clientY
                if event.button == 2
                    data.expanded = "true"
                else
                    data.expanded = "false"
                xMenu.open data, node, loadAction
            event.stopPropagation()
            return

        $window.com.ximdex.emptyActionsCache = () ->
            $scope.nodetypeActions = []
            return

        $scope.select = (node,event) ->
            if event.ctrlKey
                for k, n of $scope.selectedNodes
                    if n.nodeid == node.nodeid
                        $scope.selectedNodes.splice k, 1
                        return
                $scope.selectedNodes.push node
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
                $http.get(xUrlHelper.getAction(
                    action: "browser3"
                    method: "read"
                    id: "10000"
                )).success (data) ->
                    $scope.projects = data  if data
                    return
            else if $scope.filter.length>2
                url=xUrlHelper.getAction(
                        action: "browser3"
                        method: "readFiltered"
                        id: "10000"
                    ) + "&query=" + $scope.filter
                $http.get(url).success (data) ->
                    $scope.projects = data  if data
                    return
            $scope.selectedNodes = []
            return

]

angular.module("ximdex.main.controller").filter "nodeInArrayProp", () ->
    (input, arr, prop) ->
        for a in arr
            return true if a[prop] == input
        return false
