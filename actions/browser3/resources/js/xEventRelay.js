angular.module('ximdex.common.service')
    .factory('xEventRelay', ['$window', '$rootScope', function($window, $rootScope) {
		var $relay = $window.jQuery('#angular-event-relay');
    	var repeatEvent = function(event, data) {
    		$rootScope.$broadcast(event.type, data);
    	}

    	$relay.on('openAction', repeatEvent);
}]);