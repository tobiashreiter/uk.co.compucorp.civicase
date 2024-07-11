(function (angular, $, _) {
  var module = angular.module('civicase-features');

  module.directive('invoicesList', function () {
    return {
      restrict: 'E',
      controller: 'invoicesListController',
      templateUrl: '~/civicase-features/invoices/directives/invoices-list.directive.html',
      scope: {}
    };
  });

  module.controller('invoicesListController', invoicesListController);

  /**
   * @param {object} $scope the controller scope
   * @param {object} $location the location service
   * @param {object} $window window object of the browser
   */
  function invoicesListController ($scope, $location, $window) {
    $scope.contributionURL = async () => {
      let url = CRM.url('/contribute/add?reset=1&action=add&context=standalone');
      const caseId = $location.search().caseId;
      if (caseId) {
        url += `&caseId=${caseId}`;
      }

      $window.location.href = url;
    };
  }
})(angular, CRM._);
