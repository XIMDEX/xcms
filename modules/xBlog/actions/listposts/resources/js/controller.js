// Generated by CoffeeScript 1.10.0

/*
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
 */
if (angular.module('ximdex').notRegistred('listPostsCtrl')) {
  angular.module('ximdex').controllerProvider.register('listPostsCtrl', [
    '$scope', '$http', 'xUrlHelper', '$interval', "xTabs", function($scope, $http, xUrlHelper, $interval, xTabs) {
      var containsCaseInsensitive, loadPostFromParams, stop;
      $scope.posts = [];
      $scope.showPublished = true;
      $scope.showModified = true;
      $scope.textQuery = "";
      $scope.params = null;
      stop = null;
      $scope.query = function(item) {
        if (!$scope.showPublished && item.Published === 1) {
          return false;
        }
        if (!$scope.showModified && item.Published === 2) {
          return false;
        }
        if (!containsCaseInsensitive(item.title, $scope.textQuery) && !containsCaseInsensitive(item.intro, $scope.textQuery)) {
          return false;
        }
        return true;
      };
      containsCaseInsensitive = function(str, pattern) {
        if (pattern.length === 0) {
          return true;
        }
        return str.toLowerCase().indexOf(pattern.toLowerCase()) !== -1;
      };
      $scope.updateQuery = function() {
        if (!$scope.showPublished && !$scope.showModified) {
          return $scope.query.Published = function(value) {
            return value !== 1 && value !== 2;
          };
        } else if (!$scope.showPublished) {
          return $scope.query.Published = "!1";
        } else if (!$scope.showModified) {
          return $scope.query.Published = "!2";
        } else {
          return $scope.query.Published = "";
        }
      };
      loadPostFromParams = function(params) {
        return function() {
          var url, urlParams;
          urlParams = {
            action: params.action.command,
            id: params.nodes[0],
            module: params.action.module,
            method: 'getPosts'
          };
          url = xUrlHelper.getAction(urlParams);
          return $http.get(url).success(function(data) {
            return $scope.posts = data;
          });
        };
      };
      $scope.init = function(params) {
        var getPosts;
        $scope.params = params;
        getPosts = loadPostFromParams(params);
        getPosts();
        return stop = $interval(getPosts, 4000);
      };
      $scope.$on("$destroy", function() {
        return $interval.cancel(stop);
      });
      $scope.createnew = function(documentsid) {
        var action, nodes;
        action = {
          command: "createxhtml5container",
          name: "Create new post",
          module: "xBlog",
          params: "nodeid=" + documentsid
        };
        nodes = [
          {
            nodeid: documentsid
          }
        ];
        return xTabs.pushTab(action, nodes);
      };
      $scope.edit = function(nodeid) {
        var action, nodes;
        action = {
          command: "xhtml5editor",
          name: "Edit Post",
          module: "xBlog"
        };
        nodes = [
          {
            nodeid: nodeid
          }
        ];
        return xTabs.pushTab(action, nodes);
      };
      $scope.tag = function(nodeid) {
        var action, nodes;
        action = {
          command: "setmetadata",
          name: "Semantic tags"
        };
        nodes = [
          {
            nodeid: nodeid
          }
        ];
        return xTabs.pushTab(action, nodes);
      };
      $scope.preview = function(nodeid) {
        var action, nodes;
        action = {
          command: "rendernode",
          name: "Preview"
        };
        nodes = [
          {
            nodeid: nodeid
          }
        ];
        return xTabs.pushTab(action, nodes);
      };
      $scope.publish = function(nodeid) {
        var action, nodes;
        action = {
          command: "workflow_forward",
          name: "Publish"
        };
        nodes = [
          {
            nodeid: nodeid
          }
        ];
        return xTabs.pushTab(action, nodes);
      };
      return $scope.expire = function(nodeid) {
        var action, nodes;
        action = {
          command: "expiredoc",
          name: "Expire"
        };
        nodes = [
          {
            nodeid: nodeid
          }
        ];
        return xTabs.pushTab(action, nodes);
      };
    }
  ]);
  angular.module('ximdex').registerItem('listPostsCtrl');
}

X.actionLoaded(function(event, fn, params) {
  var scope;
  scope = $(params.context).find("div.welcome").first().scope();
  return scope.init(params);
});
