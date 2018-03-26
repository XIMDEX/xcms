###
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
###

if angular.module('ximdex').notRegistred('listPostsCtrl')
    angular.module('ximdex')
    .controllerProvider.register 'listPostsCtrl', ['$scope', '$http', 'xUrlHelper', '$interval', "xTabs",
        ($scope, $http, xUrlHelper, $interval, xTabs) ->
            $scope.posts = [];
            $scope.showPublished = true
            $scope.showModified = true
            $scope.textQuery = ""
            $scope.params = null
            stop = null
            $scope.query = (item) ->
                return false if !$scope.showPublished and item.Published == 1
                return false if !$scope.showModified and item.Published == 2
                return false if !containsCaseInsensitive(item.title, $scope.textQuery) && !containsCaseInsensitive(item.intro, $scope.textQuery)
                return true

            containsCaseInsensitive = (str, pattern) ->
                return true if pattern.length == 0
                return str.toLowerCase().indexOf(pattern.toLowerCase()) != -1

            $scope.updateQuery = () ->
                if !$scope.showPublished and !$scope.showModified
                    $scope.query.Published = (value) ->
                        return value != 1 && value != 2
                else if !$scope.showPublished
                    $scope.query.Published = "!1"
                else if !$scope.showModified
                    $scope.query.Published = "!2"
                else
                    $scope.query.Published = ""

            loadPostFromParams = (params) ->
                () ->
                    urlParams =
                        action: params.action.command
                        id: params.nodes[0]
                        module: params.action.module
                        method: 'getPosts'
                    url = xUrlHelper.getAction(urlParams);

                    $http.get(url).success((data) ->
                        $scope.posts = data
                    )

            #  Retrieve options to build the form
            $scope.init = (params) ->
                $scope.params = params
                getPosts = loadPostFromParams(params)
                getPosts()
                stop = $interval(
                        getPosts
                    ,
                        4000
                )

            $scope.$on "$destroy", () ->
                $interval.cancel(stop)

            $scope.createnew = (documentsid) ->
                action =
                    command: "createxhtml5container"
                    name: "Create new post"
                    module: "xBlog"
                    params: "nodeid=" + documentsid
                nodes = [
                    nodeid: documentsid
                ]
                xTabs.pushTab action, nodes

            $scope.edit = (nodeid) ->
                action =
                    command: "xhtml5editor"
                    name: "Edit Post"
                    module: "xBlog"
                nodes = [
                    nodeid: nodeid
                ]
                xTabs.pushTab action, nodes

            $scope.tag = (nodeid) ->
                action =
                    command: "setmetadata"
                    name: "Semantic tags"
                    module: "ximTAGS"
                nodes = [
                    nodeid: nodeid
                ]
                xTabs.pushTab action, nodes

            $scope.preview = (nodeid) ->
                action =
                    command: "rendernode"
                    name: "Preview"
                nodes = [
                    nodeid: nodeid
                ]
                xTabs.pushTab action, nodes

            $scope.publish = (nodeid) ->
                action =
                    command: "workflow_forward"
                    name: "Publish"
                nodes = [
                    nodeid: nodeid
                ]
                xTabs.pushTab action, nodes

            $scope.expire = (nodeid) ->
                action =
                    command: "expiredoc"
                    name: "Expire"
                nodes = [
                    nodeid: nodeid
                ]
                xTabs.pushTab action, nodes
    ]
    angular.module('ximdex').registerItem('listPostsCtrl');
X.actionLoaded (event, fn, params) ->
    scope = $(params.context).find("div.welcome").first().scope()
    scope.init(params)
