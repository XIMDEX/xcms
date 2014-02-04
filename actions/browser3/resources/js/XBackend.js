angular.module('ximdex.common.service')//Abstraction for server communications. TODO: Expose to client a REST like interface
    .factory('xBackend', ['$http', '$rootScope', 'xTree', function($http, $rootScope, xTree) {
        return {
            sendFormData: function(formData, url, callback){
                $http({
                        method  : 'POST',
                        url     : url,
                        data    : $.param(formData),  // pass in data as strings
                        headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                }).success(function(data) {         
                        if (formData.IDParent || formData.id)
                            $rootScope.$broadcast('nodeModified', formData.IDParent || formData.id);
                        callback(data);
                });
            }
        }
	}]);

//TODO: put this hacky service in its own file
angular.module('ximdex.common.service')
    .factory('xTree', ['$window', '$rootScope', function($window, $rootScope) { 
        //Listen for node modification events to update the tree
        $rootScope.$on('nodeModified', function(event, nodeId){
            $window.jQuery('li#treeview-nodeid-'+nodeId)
                .closest('div.xim-treeview-container')
                .treeview('refresh', nodeId);
        });
}]);

//DIRECTIVES
        angular.module('ximdex.common.directive')
            .directive('ximButton', ['$window', function ($window) {
                return {
                    replace: true,
                    scope: {
                        state: '=ximState',
                        disabled: '=ximDisabled',
                        label: '=ximLabel',
                        progress: '=ximProgress'
                    },
                    restrict: 'A',
                    template: '<button type="button" class="button ladda-button" data-style="slide-up" data-size="xs" ng-disabled="disabled">'+
                            '<span class="ladda-label">[[label]][[progress]]</span>'+
                        '</button>',
                    link: function postLink(scope, element, attrs) {
                        var loader = $window.Ladda.create(element[0]);
                        scope.$watch('state', function(newState, oldState){
                            console.log("stating", newState);
                            switch (newState) {
                                case 'submitting':
                                case 'pending':
                                    loader.start();
                                    break;
                                case 'resolved':
                                    loader.stop();
                                    break;
                            }
                        });
                        scope.$watch('progress', function(newValue, oldValue){
                            console.log("progress", newValue);
                            if (oldValue != newValue)
                                loader.setProgress(newValue)
                        });
                    }
                }
        }]);

        //FILTERS
        angular.module('ximdex.common.filter')
            .filter('ximBytes', function(){
                return function(bytes){
                    if (isNaN(parseFloat(bytes)) || !isFinite(bytes))
                        return ''
                    units = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'];
                    number = Math.floor(Math.log(bytes) / Math.log(1024));
                    return (bytes / Math.pow(1024, Math.floor(number))).toFixed(2) +' '+ units[number];
                }
        });