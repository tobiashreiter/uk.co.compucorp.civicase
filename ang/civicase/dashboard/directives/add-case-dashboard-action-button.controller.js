(function (_, angular, checkPerm, loadForm, getCrmUrl) {
  var module = angular.module('civicase');

  module.controller('AddCaseDashboardActionButtonController', AddCaseDashboardActionButtonController);

  /**
   * Add Case Dashboard Action Button Controller
   *
   * @param {object} $scope scope object
   * @param {object} ts ts
   * @param {object} $location the location service
   * @param {object} AddCase Add Case Service
   */
  function AddCaseDashboardActionButtonController ($scope, ts, $location, AddCase) {
    $scope.ts = ts;

    $scope.clickHandler = clickHandler;
    $scope.isVisible = AddCase.isVisible;

    /**
     * Click handler for the Add Case Dashboard button.
     */
    function clickHandler () {
      AddCase.clickHandler({
        caseTypeCategoryName: getCaseTypeCategory()
      });
    }

    /**
     * Returns the case type category as defined in the URL parameters.
     *
     * @returns {string} the case type category
     */
    function getCaseTypeCategory () {
      var currentUrlParams = $location.search();

      return currentUrlParams.case_type_category;
    }
  }
})(CRM._, angular, CRM.checkPerm, CRM.loadForm, CRM.url);
