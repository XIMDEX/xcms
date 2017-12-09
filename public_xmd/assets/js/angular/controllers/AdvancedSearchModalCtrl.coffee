angular.module('ximdex.main.controller').controller 'AdvancedSearchModalCtrl', [
    '$scope'
    '$modalInstance'
    '$filter'
    '$http'
    'xUrlHelper'
    '$window'
    '$modal'
    'xMenu'
    'xTabs'
    ($scope, $modalInstance, $filter, $http, xUrlHelper, $window, $modal, xMenu, xTabs) ->
        $scope.filters = []
        $scope.condition = 'and'
        $scope.results = null
        $scope.selected = []
        $scope.lastSearches = $window.com.ximdex.session.get('last.searches') or []
        $scope.savedFilters = []
        url = xUrlHelper.getAction(
            action: 'browser3'
            method: 'search')
        urlToSave = xUrlHelper.getAction(
            action: 'browser3'
            method: 'addFilter')
        urlListFilters = xUrlHelper.getAction(
            action: 'browser3'
            method: 'listFilters')
        urlDeleteSavedFilter = xUrlHelper.getAction(
            action: 'browser3'
            method: 'deleteFilter')

        $scope.addFilter = ->
            $scope.filters.push
                'field': 'name'
                'comparation': 'contains'
                'nodetype_comparation': 'equal'
                'date_comparation': 'equal'
                'content': ''
                'nodetype_content': '5022'
                'date_content': $filter('date')(new Date, 'dd/MM/yyyy')
                'date_content_to': $filter('date')(new Date, 'dd/MM/yyyy')
            return

        $scope.deleteFilter = (index) ->
            $scope.filters.splice index, 1
            if $scope.filters.length == 0
                $scope.addFilter()
            return

        $scope.addFilter()

        $scope.ok = ->
            $modalInstance.close()
            return

        $scope.cancel = ->
            $modalInstance.dismiss 'cancel'
            return

        $scope.updateSavedFilters = ->
            $http(
                method: 'GET'
                url: urlListFilters
            ).success((data, status) ->
                $scope.savedFilters = data
                return
            ).error (data, status) ->
                return

        $scope.updateSavedFilters()

        $scope.deleteSavedFilter = (id) ->
            modalInstance = $modal.open(
                animation: true
                templateUrl: $window.X.baseUrl + '/public_xmd/assets/js/angular/templates/confirmDeleteFilterModal.html'
                controller: 'ConfirmDeleteFilterModalCtrl'
                size: 'sm'
                resolve: {}
                windowClass: "confirm-delete-filter"
            )
            modalInstance.result.then ((name) ->
                $http(
                    method: 'POST'
                    url: urlDeleteSavedFilter
                    data: $.param("filterid": id)
                    headers: 'Content-Type': 'application/x-www-form-urlencoded'
                ).success((data, status) ->
                    $scope.updateSavedFilters()
                    return
                ).error (data, status) ->
                    return
            ), ->
                return


        $scope.saveQuery = ->
            modalInstance = $modal.open(
                animation: true
                templateUrl: $window.X.baseUrl + '/public_xmd/assets/js/angular/templates/enterNameFilterModal.html'
                controller: 'EnterNameFilterModalCtrl'
                size: 'sm'
                resolve:
                    condition: ->
                        $scope.condition
                    filters: ->
                        $scope.filters
                    urlToSave: ->
                        urlToSave
                windowClass: "enter-name"
            )
            modalInstance.result.then (() ->
                $scope.updateSavedFilters()
                return
            ), ->
                return

        queryToString = (q) ->
            res = ''
            for f, k in q.query.filters
                if k != 0
                    res += ' '
                res += f.field + ' ' + f.comparation
                if f.to
                    res += ' ' + f.from
                else
                    res += ' ' + f.content
                if k != q.query.filters.length - 1
                    res += ' ' + q.query.condition
            res

        $scope.search = (query) ->
            stringQuery = ''
            q =
                handler: 'SQL'
                output: 'JSON'
                query:
                    'parentid': '10000'
                    'depth': '0'
                    'items': '50'
                    'page': '1'
                    'view': 'gridview'
                    'condition': $scope.condition
                    'filters': []
            if typeof query == 'string'
                stringQuery = query
            else if typeof query == 'object'
                q.query.filters = query.filters
                q.query.condition = query.condition
                stringQuery = $.param q
                $scope.filters = angular.copy query.filters
            else

                for f of $scope.filters
                    if $scope.filters.hasOwnProperty(f)
                        filter = {}
                        switch $scope.filters[f].field
                            when 'name', 'path', 'content', 'tag', 'url', 'desc'
                                filter.field = $scope.filters[f].field
                                filter.comparation = $scope.filters[f].comparation
                                filter.content = $scope.filters[f].content
                                filter.from = $scope.filters[f].content
                            when 'nodetype'
                                filter.field = $scope.filters[f].field
                                filter.comparation = $scope.filters[f].nodetype_comparation
                                filter.content = $scope.filters[f].nodetype_content
                                filter.from = $scope.filters[f].nodetype_content
                            when 'creation', 'versioned', 'publication'
                                filter.field = $scope.filters[f].field
                                filter.comparation = $scope.filters[f].date_comparation
                                filter.content = $scope.filters[f].date_content
                                filter.from = $scope.filters[f].date_content
                                filter.to = $scope.filters[f].date_content_to
                        q.query.filters.push filter
                stringQuery = $.param(q)
            $http(
                method: 'POST'
                url: url
                data: stringQuery
                headers: 'Content-Type': 'application/x-www-form-urlencoded'
            ).success((data, status) ->
                $scope.selectNone()
                $scope.results = data
                title = queryToString(q)
                if typeof query == 'undefined' && ($scope.lastSearches.length == 0 || $scope.lastSearches[0].title != title)
                    $scope.lastSearches.unshift
                        title: title
                        filter:
                            condition: $scope.condition
                            filters: $scope.filters
                    if $scope.lastSearches.length > 6
                        $scope.lastSearches.pop()
                    $window.com.ximdex.session.set 'last.searches', $scope.lastSearches, '1d'
                return
            ).error (data, status) ->
                return

        $scope.updateView = ->
            $http(
                method: 'POST'
                url: url
                data: $.param(
                    handler: 'SQL'
                    output: 'JSON'
                    query: $scope.results.query)
                headers: 'Content-Type': 'application/x-www-form-urlencoded'
            ).success((data, status) ->
                $scope.selectNone()
                $scope.results = data
                return
            ).error (data, status) ->
                return

        $scope.selectNode = (node, event) ->
            if event.ctrlKey
                if $scope.isSelected(node)
                    for n of $scope.selected
                        if $scope.selected.hasOwnProperty(n) and $scope.selected[n].nodeid == node.nodeid
                            $scope.selected.splice n, 1
                            return
                else
                    $scope.selected.push node
            else
                $scope.selected = [ node ]
            return

        $scope.isSelected = (node) ->
            for n of $scope.selected
                if $scope.selected.hasOwnProperty(n) and $scope.selected[n].nodeid == node.nodeid
                    return true
            false

        $scope.selectAll = ->
            `var n`
            for n of $scope.results.data
                if $scope.results.data.hasOwnProperty(n) and $scope.isSelected($scope.results.data[n])
                    $scope.selected.splice n, 1
                    return
            for n of $scope.results.data
                if $scope.results.data.hasOwnProperty(n)
                    $scope.selected.push $scope.results.data[n]
            return

        $scope.selectNone = ->
            $scope.selected = []
            return

        $scope.invertSelection = ->
            newSelection = []
            for n of $scope.results.data
                if $scope.results.data.hasOwnProperty(n)
                    if !$scope.isSelected($scope.results.data[n])
                        newSelection.push $scope.results.data[n]
            $scope.selected = newSelection
            return

        $scope.openMenu = (node, event) ->
            $scope.selectNode node, event
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

        $scope.isLoading = ->
            $http.pendingRequests.length > 0

        return
]
angular.module('ximdex.main.controller').controller 'EnterNameFilterModalCtrl', [
    '$scope'
    '$modalInstance'
    '$http'
    'condition'
    'filters'
    'urlToSave'
    ($scope, $modalInstance, $http, condition, filters, urlToSave) ->
        $scope.name = ''
        $scope.error = false
        $scope.ok = ->
            $http(
                method: 'POST'
                url: urlToSave
                data: $.param(
                    filter:
                        condition: condition
                        filters: filters
                    handler: 'SQL'
                    output: 'JSON'
                    name: $scope.name
                )
                headers: 'Content-Type': 'application/x-www-form-urlencoded'
            ).success((data, status) ->
                if data[0]?.message? && data[0].message.length > 0
                    $scope.error = true
                else
                    $modalInstance.close()
                return
            ).error (data, status) ->
            return

        $scope.cancel = ->
            $modalInstance.dismiss 'cancel'
            return

        return
]

angular.module('ximdex.main.controller').controller 'ConfirmDeleteFilterModalCtrl', [
    '$scope'
    '$modalInstance'
    ($scope, $modalInstance) ->

        $scope.ok = ->
            $modalInstance.close $scope.name
            return

        $scope.cancel = ->
            $modalInstance.dismiss 'cancel'
            return

        return
]