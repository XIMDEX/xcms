angular.module("ximdex.common.directive").directive "ximAssocNodes", [
    "xTranslate", "$window", "$http"
    "xUrlHelper", "xMenu", "$document", "$timeout", "$q", "xTabs", "$rootScope"
    (xTranslate, $window, $http, xUrlHelper, xMenu, $document, $timeout, $q, xTabs, $rootScope) ->
        base_url = $window.X.baseUrl
        return (
            templateUrl: base_url+'/public_xmd/assets/js/angular/templates/ximAssocNodes.html'
            restrict: "E"
            replace: true
            scope:
                donothing: '@donothing'
            controller: "AssocNodesCtrl"
            controllerAs: "ctrl1"
        )
]