angular.module("ximdex.common.directive").directive "ximList", [
    "$window"
    ($window) ->
        base_url = $window.X.baseUrl
        return (
            templateUrl: base_url+'assets/js/angular/templates/ximList.html'
            restrict: "E"
        )
]