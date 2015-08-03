angular.module('ximdex.main.controller')
    .controller('XMainCtrl', ['$scope', '$attrs', 'xEventRelay', '$timeout',
        '$http', '$window', 'xUrlHelper', '$modal', '$log',
        function ($scope, $attrs, xEventRelay, $timeout, $http, $window, xUrlHelper, $modal, $log) {

            //Removes ximdex splash
            $timeout(
                function () {
                    angular.element("#ximdex-splash").remove();
                }
                ,
                3200
            );

            //Cache for node actions
            $window.com.ximdex.nodeActions = [];

            //Global method to empty the actions cache
            $window.com.ximdex.emptyActionsCache = function () {
                $window.com.ximdex.nodeActions = [];
            }

            //Gets preferences
            $http.get(xUrlHelper.getAction({
                action: "browser3",
                method: "getPreferences"
            })).success(function (data) {
                if (data) {
                    $window.com.ximdex.preferences = data.preferences;
                }
            });

            $scope.openModal = function () {
                $modal.open({
                    animation: $scope.animationsEnabled,
                    templateUrl: $window.X.baseUrl + '/inc/js/angular/templates/advancedSearchModal.html',
                    controller: 'AdvancedSearchModalCtrl',
                    size: "lg",
                    resolve: {}
                });
            };
        }])





    .directive('ngRightClick',['$parse', function($parse) {
        return function(scope, element, attrs) {
            var fn = $parse(attrs.ngRightClick);
            element.bind('contextmenu', function(event) {
                scope.$apply(function() {
                    event.preventDefault();
                    fn(scope, {$event:event});
                });
            });
        };
    }]);