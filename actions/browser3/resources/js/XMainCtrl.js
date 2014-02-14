angular.module('ximdex.main.controller')
	.controller('XMainCtrl', ['$scope', '$attrs', 'xEventRelay', 'xTree', function($scope, $attrs, xEventRelay, xTree){
		$scope.$on('nodeModified', function(event, data){
			console.log("Event recieved", data);
		});
	}]);