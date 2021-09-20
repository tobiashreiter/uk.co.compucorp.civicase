(function (angular, _) {
  var module = angular.module('civicase-base');

  module.directive('civicaseAccessDenied', function () {
    return {
      restrict: 'E',
      templateUrl: '~/civicase-base/directives/access-denied.directive.html'
    };
  });
}(angular, CRM._));
