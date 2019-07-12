var angular = require('angular');

require('angular-hammer');

angular.module('hmTime', ['hmTouchEvents'])
  .controller('hmCtrl', ['$scope', function ($scope) {
    $scope.eventType = "No events yet";
    $scope.onHammer = function onHammer (event) {
      $scope.eventType = event.type;
    };
  }]);