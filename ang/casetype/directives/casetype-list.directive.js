(function ($, _, angular) {
  var module = angular.module('casetype');

  module.directive('casetypeList', function () {
    return {
      scope: {
        caseTypeCategory: '@'
      },
      controller: 'casetypeListController',
      templateUrl: '~/casetype/directives/casetype-list.directive.html',
      restrict: 'E'
    };
  });

  module.controller('casetypeListController', casetypeListController);

  /**
   * @param {object} $scope scope object
   * @param {object} ts ts
   */
  function casetypeListController ($scope, ts) {
    $scope.ts = ts;

    (function init () {
    }());
  }
})(CRM.$, CRM._, angular);
