###*
#  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
#
#  Ximdex a Semantic Content Management System (CMS)
#
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU Affero General Public License as published
#  by the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Affero General Public License for more details.
#
#  See the Affero GNU General Public License for more details.
#  You should have received a copy of the Affero GNU General Public License
#  version 3 along with Ximdex (see LICENSE file).
#
#  If not, visit http://gnu.org/licenses/agpl-3.0.html.
#
#  @author Ximdex DevTeam <dev@ximdex.com>
#  @version $Revision$
###

angular.module('ximdex.common.directive').directive 'ximGrid', [ ->
    {
    replace: true
    restrict: 'E'
    scope:
        list: '=ximList'
        filterText: '=ximFilter'
        url: '=ximUrl'
        page: '=ximActualPage'
        pages: '=ximTotalPages'
        upPage: '=ximUpPage'
        downPage: '=ximDownPage'
        searching: '=ximSearching'
    templateUrl: 'public_xmd/assets/js/angular/templates/ximGrid.html'
    controller: [
        '$scope'
        '$element'
        '$attrs'
        '$http'
        'xUrlHelper'
        '$window'
        'xMenu'
        'xTabs'
        ($scope, $element, $attrs, $http, xUrlHelper, $window, xMenu, xTabs) ->
            if $attrs.ximInitFields
                $scope.fields = angular.fromJson($attrs.ximInitFields)
            $scope.selected = []
            $attrs.ximList = $scope.list
            url = xUrlHelper.baseUrl() + '/xmd/loadaction.php'
            $scope.lastpage
            $scope.page = 1
            #$scope.pages=$scope.list.pages;
            $scope.searching = false

            $scope.JSONtoParams = (json) ->
                res =
                    action: 'browser3'
                    handler: 'SQL'
                    method: 'search'
                    output: 'JSON'
                for i of json
                    if json.hasOwnProperty(i)
                        if typeof json[i] != 'string' & isNaN(json[i])
                            for j of json[i]
                                if json[i].hasOwnProperty(j)
                                    if typeof json[i][j] != 'string' & isNaN(json[i][j])
                                        for k of json[i][j]
                                            if json[i][j].hasOwnProperty(k)
                                                if typeof json[i][j][k] == 'string' or !isNaN(json[i][j][k])
                                                    res['query[' + i + '][' + j + '][' + k + ']'] = json[i][j][k]
                                    else
                                        res['query[' + i + '][' + j + ']'] = json[i][j]
                        else
                            res['query[' + i + ']'] = json[i]
                res

            $scope.updateGrid = (page) ->
                $scope.searching = true
                $scope.showFieldsSelector = false
                $scope.list.query.page = $scope.page
                $http(
                    url: url
                    method: 'POST'
                    params: $scope.JSONtoParams $scope.list.query
                ).success((data, status, headers, config) ->
                    $scope.filterText = ''
                    $attrs.ximFilter = ''
                    $scope.list = data
                    $scope.searching = false
                    return
                ).error (data, status, headers, config) ->
                    if page
                        $scope.page = $scope.lastpage
                    $scope.searching = false
                    return
                return

            $scope.upPage = ->
                if !$scope.searching & $scope.page < $scope.list.pages
                    $scope.lastpage = $scope.page
                    $scope.page++
                    $scope.updateGrid true
                return

            $scope.downPage = ->
                if !$scope.searching & $scope.page > 1
                    $scope.lastpage = $scope.page
                    $scope.page--
                    $scope.updateGrid true
                return

            $attrs.ximUpPage = $scope.upPage
            $attrs.ximDownPage = $scope.downPage

            $scope.selectItem = (item, event) ->
                event.preventDefault()
                if !$scope.searching
                    if event.ctrlKey
                        if $scope.isSelected(item.nodeid)
                            return if event.button == 2
                            for n of $scope.selected
                                if $scope.selected.hasOwnProperty(n) and $scope.selected[n].nodeid == item.nodeid
                                    $scope.selected.splice n, 1
                                    return
                        else
                            $scope.selected.push item
                    else
                        $scope.selected = [ item ]
                return


            $scope.isSelected = (itemId) ->
                for i in $scope.selected
                    if i.nodeid == itemId
                        return true
                return false

            $scope.sort = (field) ->
                if !$scope.searching
                    if $scope.list.query.sorts[0].field != field.target
                        $scope.list.query.sorts[0].field = field.target
                        $scope.list.query.sorts[0].order = 'asc'
                        $scope.page = 1
                    else
                        if $scope.list.query.sorts[0].order == 'asc'
                            $scope.list.query.sorts[0].order = 'desc'
                        else
                            $scope.list.query.sorts[0].order = 'asc'
                    $attrs.ximList.query = $scope.list.query
                    $scope.updateGrid false
                return

            $scope.$watch 'fields', (->
                $scope.$broadcast 'ui-refresh'
                return
            ), true
            $scope.$watch 'filterText', ->
                $scope.$broadcast 'ui-refresh'
                return
            $scope.$on 'toggleFieldsSelector', (event) ->
                if !$scope.searching
                    $scope.showFieldsSelector = !$scope.showFieldsSelector
                return

            $scope.openMenu = (node, event) ->
                event.stopImmediatePropagation()
                $scope.selectItem node, event
                if $scope.selected[0].nodeid == null | $scope.selected[0].nodetypeid == null | $scope.selected[0].nodeid == '0'
                    return
                nodeToSearch = $scope.selected[0].nodeid
                if $scope.selected.length > 1
                    ref = $scope.selected.slice(1)
                    i = 0
                    len = ref.length
                    while i < len
                        n = ref[i]
                        nodeToSearch += '-' + n.nodeid
                        i++
                if not $window.com.ximdex.nodeActions[nodeToSearch]?
                    $http.get(xUrlHelper.getAction(
                        action: 'browser3'
                        method: 'cmenu'
                        nodes: $scope.selected)).success (data) ->
                            if data
                                $window.com.ximdex.nodeActions[nodeToSearch] = data
                                postLoadActions data, event, $scope.selected
                            return
                else
                    data = $window.com.ximdex.nodeActions[nodeToSearch]
                    postLoadActions data, event, $scope.selected
                false

            postLoadActions = (data, event, selectedNodes) ->
                return if not data?
                if event.pointers?
                    data.left = event.pointers[0].clientX + (if $window.document.documentElement.scrollLeft then $window.document.documentElement.scrollLeft else $window.document.body.scrollLeft)
                    data.top = event.pointers[0].clientY + (if $window.document.documentElement.scrollTop then $window.document.documentElement.scrollTop else $window.document.body.scrollTop)
                if event.clientX?
                    data.left = event.clientX + (if $window.document.documentElement.scrollLeft then $window.document.documentElement.scrollLeft else $window.document.body.scrollLeft)
                    data.top = event.clientY + (if $window.document.documentElement.scrollTop then $window.document.documentElement.scrollTop else $window.document.body.scrollTop)
                xMenu.open data, selectedNodes, xTabs.pushTab
                data = null
                return
    ]
    }
]