angular.module('ximdex.common.directive').directive "compileTemplate", ($compile, $parse) ->
    restrict: "A"
    link: (scope, element, attr) ->
        parsed = $parse(attr.ngBindHtml)

        #Recompile if the template changes
        scope.$watch (->
            (parsed(scope) or "").toString()
        ), ->
            $compile(element, null, -9999) scope #The -9999 makes it skip directives so that we do not recompile ourselves
            return

        return