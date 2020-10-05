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
   * @param {object} civicaseCrmApi service to use civicrm api
   * @param {object[]} WorkflowListActionItems list of workflow list action items
   */
  function casetypeListController ($scope, ts, civicaseCrmApi,
    WorkflowListActionItems) {
    $scope.ts = ts;
    $scope.caseTypes = [];
    $scope.actionItems = WorkflowListActionItems;

    (function init () {
      getCaseTypes($scope.caseTypeCategory)
        .then(function (caseTypes) {
          $scope.caseTypes = caseTypes;
        });
    }());

    /**
     * Get list of case types for the sent case type category
     *
     * @param {string} caseTypeCategory case type category
     * @returns {Promise} list of case types
     */
    function getCaseTypes (caseTypeCategory) {
      return civicaseCrmApi('CaseType', 'get', {
        sequential: 1,
        case_type_category: caseTypeCategory,
        options: { limit: 0 }
      }).then(function (data) {
        return data.values;
      });
    }
  }
})(CRM.$, CRM._, angular);
