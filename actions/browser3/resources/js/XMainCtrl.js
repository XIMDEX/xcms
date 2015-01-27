angular.module('ximdex.main.controller')
	.controller('XMainCtrl', ['$scope', '$attrs', 'xEventRelay', '$timeout', function($scope, $attrs, xEventRelay, $timeout){
        $timeout(
                function() {
                    angular.element("#ximdex-splash").remove();
                }
            ,
                3200
        );
	}]);
