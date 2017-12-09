angular.module('ximdex.common.directive').directive 'datepicker', ->
    {
    restrict: 'A'
    require: 'ngModel'
    link: (scope, element, attrs, ngModelCtrl) ->
        $ ->
            element.datepicker
                dateFormat: 'dd/mm/yy'
                onSelect: (date) ->
                    scope.$apply ->
                        ngModelCtrl.$setViewValue date
                        return
                    return
            return
        return

    }