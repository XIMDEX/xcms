angular.module("ximdex.common.directive").directive "ximTree", [
    "$window"
    ($window) ->
        base_url = $window.X.baseUrl
        return (
            templateUrl: base_url+'/public_xmd/assets/js/angular/templates/ximTree.html'
            restrict: "E"
        )
]