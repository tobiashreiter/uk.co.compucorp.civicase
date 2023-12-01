(function (angular, $, _) {
  var module = angular.module('civicase');

  module.directive('civicaseContactCaseTabCaseDetails', function () {
    return {
      controller: 'CivicaseContactCaseTabCaseDetailsController',
      restrict: 'EA',
      replace: true,
      templateUrl: '~/civicase/case/contact-case-tab/directives/contact-case-tab-case-details.directive.html',
      scope: {
        item: '=selectedCase',
        caseTypeCategory: '=',
        refreshCases: '=refreshCallback'
      }
    };
  });

  module.controller('CivicaseContactCaseTabCaseDetailsController', CivicaseContactCaseTabCaseDetailsController);

  /**
   * @param {object} $scope the scope reference.
   * @param {Function} CaseTypeCategory case type category service.
   * @param {Function} ts translation service
   * @param {object} civicaseCrmUrl civicrm url service
   */
  function CivicaseContactCaseTabCaseDetailsController ($scope, CaseTypeCategory,
    ts, civicaseCrmUrl) {
    $scope.getCaseDetailsUrl = getCaseDetailsUrl;
    $scope.ts = ts;

    /**
     * Returns the URL needed to open the case details page for the given case.
     * The status id parameter is appended otherwise non "Opened" cases would be hidden
     * since the details page filters by "Opened" cases by default when no status is sent.
     *
     * @param {object} caseItem the case data.
     * @returns {string} the case details page url.
     */
    function getCaseDetailsUrl (caseItem) {
      var caseTypeCategoryId = caseItem['case_type_id.case_type_category'];

      return civicaseCrmUrl('civicrm/case/a/', 'case_type_category=' + caseTypeCategoryId +
        '#/case/list?caseId=' + caseItem.id + '&focus=1&cf={"id":' + caseItem.id + ',"case_type_category":' + caseTypeCategoryId + '}');
    }
  }
})(angular, CRM.$, CRM._);
