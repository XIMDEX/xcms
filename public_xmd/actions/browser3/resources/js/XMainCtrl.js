/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

angular

.module('ximdex.main.controller').controller('XMainCtrl', ['$scope', '$attrs', 'xEventRelay', '$timeout', '$http', '$window'
, 'xUrlHelper', '$modal', '$log', 
function ($scope, $attrs, xEventRelay, $timeout, $http, $window, xUrlHelper, $modal, $log)
{
    // Removes ximdex splash
    $timeout(function (){ angular.element("#ximdex-splash").remove(); }, 3200);

    // Cache for node actions
    $window.com.ximdex.nodeActions = [];

    // Global method to empty the actions cache
    $window.com.ximdex.emptyActionsCache = function ()
    {
        $window.com.ximdex.nodeActions = [];
    }

    // Gets preferences
    $http.get(xUrlHelper.getAction({ 
    	action: "browser3", 
    	method: "getPreferences" 
    	})).success(function (data)
    {
        if (data) {
            $window.com.ximdex.preferences = data.preferences;
        }
    });
    $scope.openModal = function ()
    {
        $modal.open({
            animation: $scope.animationsEnabled,
            templateUrl: $window.X.baseUrl + 'assets/js/angular/templates/advancedSearchModal.html',
            controller: 'AdvancedSearchModalCtrl',
            size: "lg",
            resolve: {}
        });
    };
}])

.directive('ngRightClick',['$parse', function($parse)
{
    return function(scope, element, attrs)
    {
        var fn = $parse(attrs.ngRightClick);
        element.bind('contextmenu', function(event) {
            scope.$apply(function() {
                event.preventDefault();
                fn(scope, {$event:event});
            });
        });
    };
}]);