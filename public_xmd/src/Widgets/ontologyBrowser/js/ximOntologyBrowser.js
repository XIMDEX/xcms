if (angular.module('ximdex').notRegistred && angular.module('ximdex').notRegistred('ximOntologyBrowser')){
    angular.module('ximdex')
        .compileProvider.directive('ximOntologyBrowser', ['$window', function ($window) {
            return {
                replace: true,
                scope: {
                    selectedItems: '=ximSelectedList',
                    onSelect: '&ximOnSelect',
                    onUnSelect: '&ximOnUnSelect'
                },
                restrict: 'A',
                link: function (scope, element, attrs) {
                    selected = [];
                    for (i = 0, len = scope.selectedItems.length; i < len; i++) {
                        selected.push(scope.selectedItems[i].Name);
                    }
                    $element = $window.jQuery(element);
                    $element.ontologywidget({
                        selected: selected,
                        onSelect: function(el) {
                            scope.$apply(function(){
                                scope.onSelect({ontology:el});
                            });
                        },
                        offSelect: function(name) {
                            scope.$apply(function(){
                                scope.onUnSelect({ontology:{name:name}});
                            });
                        }
                    });

                    //Watch for removed structured items
                    scope.$watch('selectedItems', function(newVal, oldVal){
                        for (i = 0, len = oldVal.length; i < len; i++) {
                            var tag = oldVal[i];
                            if (tag.structured){
                                var deleted = true;
                                for (_i = 0, _len = newVal.length; _i < _len; _i++) {
                                    if (tag.Name == newVal[_i].Name) {
                                        deleted = false;
                                    }                              
                                }
                                if (deleted) {
                                    $element.ontologywidget('unselectNode', tag.Name);
                                }
                            }
                        }
                    }, true);    
                }
            }
        }]);
    angular.module('ximdex').registerItem('ximOntologyBrowser');
}