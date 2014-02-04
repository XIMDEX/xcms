angular.module('ximdex.main.controller')
	.controller('XMainCtrl', ['$scope', '$attrs', 'xEventRelay', function($scope, $attrs, xEventRelay){
		$scope.$on('openAction', function(event, data){
			console.log("Event recieved", data);
		});
	}]);