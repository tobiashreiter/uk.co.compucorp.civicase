(function (_, angular, checkPerm, loadForm, getCrmUrl) {
  var module = angular.module('civicase');

  module.controller('AddCaseDashboardActionButtonController', AddCaseDashboardActionButtonController);

  /**
   * Add Case Dashboard Action Button Controller
   *
   * @param {object} $scope scope object
   * @param {object} ts ts
   * @param {object} $location the location service.
   * @param {object} $window the window service.
   * @param {string} currentCaseCategory the current case type category configuration value.
   * @param {string} CaseCategoryWebformSettings service to fetch case category webform settings
   */
  function AddCaseDashboardActionButtonController ($scope, ts, $location, $window,
    currentCaseCategory, CaseCategoryWebformSettings) {
    var webformSettings = CaseCategoryWebformSettings.getSettingsFor(currentCaseCategory);

    $scope.ts = ts;

    $scope.clickHandler = clickHandler;
    $scope.isVisible = isVisible;

    /**
     * Displays a form to add a new case. If a custom "Add Case" webform url has been configured,
     * it will redirect to it. Otherwise it will open a CRM form popup to add a new case.
     */
    function clickHandler () {
      var hasCustomNewCaseWebformUrl = !!webformSettings.newCaseWebformUrl;

      hasCustomNewCaseWebformUrl
        ? redirectToCustomNewCaseWebformUrl()
        : openNewCaseForm();
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

    /**
     * Will display the button if the user can add cases.
     *
     * @returns {boolean} returns true when the user can add cases.
     */
    function isVisible () {
      var canAddCases = checkPerm('add cases');

      return canAddCases;
    }

    /**
     * Opens a new CRM form popup to add new cases. If a case type category was defined we
     * use it to limit the type of cases that can be created by this category.
     */
    function openNewCaseForm () {
      var caseTypeCategory = getCaseTypeCategory();

      var formUrl = getCrmUrl('civicrm/case/add', {
        action: 'add',
        case_type_category: caseTypeCategory || currentCaseCategory,
        context: 'standalone',
        reset: 1
      });

      loadForm(formUrl);
    }

    /**
     * Redirects the user to the custom webform URL as defined in the configuration.
     */
    function redirectToCustomNewCaseWebformUrl () {
      $window.location.href = webformSettings.newCaseWebformUrl;
    }
  }
})(CRM._, angular, CRM.checkPerm, CRM.loadForm, CRM.url);
