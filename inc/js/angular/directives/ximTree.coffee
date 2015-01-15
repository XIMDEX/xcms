angular.module("ximdex.common.directive").directive "ximTree", [
    "$window"
    ($window) ->
        base_url = $window.X.baseUrl
        return (
            templateUrl: base_url+'/inc/js/angular/templates/ximTree.html'
            restrict: "E"
        )
]