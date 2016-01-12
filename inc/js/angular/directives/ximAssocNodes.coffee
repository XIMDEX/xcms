angular.module("ximdex.common.directive").directive "ximAssocNodes", [
    "xTranslate", "$window", "$http"
    "xUrlHelper", "xMenu", "$document", "$timeout", "$q", "xTabs", "$rootScope"
    (xTranslate, $window, $http, xUrlHelper, xMenu, $document, $timeout, $q, xTabs, $rootScope) ->
        base_url = $window.X.baseUrl
        return (
            templateUrl: base_url+'/inc/js/angular/templates/ximTree.html'
            restrict: "E"
            replace: true
            controller: "AssocNodesCtrl"
            controllerAs: "ctrl1"
        )
]