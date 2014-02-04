angular.module('ximdex.common.directive')
    .directive('ximButton', ['$window', function ($window) {
        return {
            replace: true,
            scope: {},
            restrict: 'E',
            template: '<div></div>',
            controller: ['$scope', '$attrs', function($scope, $attrs) {
            	console.log("CONTROLING TREE");
            }],
            link: function postLink(scope, element, attrs) {
            	console.log("LONKING TREE");    
            }
        }
	}]);