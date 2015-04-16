angular.module('ximdex.main.controller')
	.controller('XMainCtrl', ['$scope', '$attrs', 'xEventRelay', '$timeout',
        '$http', '$window', 'xUrlHelper',
        function($scope, $attrs, xEventRelay, $timeout, $http, $window, xUrlHelper){

            //Removes ximdex splash
            $timeout(
                    function() {
                        angular.element("#ximdex-splash").remove();
                    }
                ,
                    3200
            );

            //Cache for node actions
            $window.com.ximdex.nodeActions = [];

            //Global method to empty the actions cache
            $window.com.ximdex.emptyActionsCache = function() {
                $window.com.ximdex.nodeActions = [];
            }

            //Gets preferences
            $http.get(xUrlHelper.getAction({
                action: "browser3",
                method: "getPreferences"
            })).success(function(data) {
                if (data) {
                    $window.com.ximdex.preferences = data.preferences;
                }
            });
	}]);
