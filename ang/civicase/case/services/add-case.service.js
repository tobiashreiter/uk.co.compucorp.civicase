(function (_, angular, checkPerm, loadForm, getCrmUrl) {
  var module = angular.module('civicase');

  module.service('AddCase', AddCaseService);

  /**
   * Add Case Service
   *
   * @param {object} ts ts
   * @param {object} $location the location service.
   * @param {object} $window the window service.
   * @param {string} CaseCategoryWebformSettings service to fetch case category webform settings
   */
  function AddCaseService (ts, $location, $window, CaseCategoryWebformSettings) {
    this.clickHandler = clickHandler;
    this.isVisible = isVisible;

    /**
     * Displays a form to add a new case. If a custom "Add Case" webform url has been configured,
     * it will redirect to it. Otherwise it will open a CRM form popup to add a new case.
     *
     * @param {string} caseTypeCategory case type category
     * @param {string} contactId contact id
     * @param {Function} callback callback function
     */
    function clickHandler (caseTypeCategory, contactId, callback) {
      var webformSettings = CaseCategoryWebformSettings.getSettingsFor(caseTypeCategory);
      var hasCustomNewCaseWebformUrl = !!webformSettings.newCaseWebformUrl;

      hasCustomNewCaseWebformUrl
        ? redirectToCustomNewCaseWebformUrl(webformSettings, contactId)
        : openNewCaseForm(caseTypeCategory, contactId, callback);
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
     *
     * @param {string} caseTypeCategory case type category
     * @param {string} contactId contact id
     * @param {Function} callback callback function
     */
    function openNewCaseForm (caseTypeCategory, contactId, callback) {
      var formParams = {
        action: 'add',
        case_type_category: caseTypeCategory,
        context: 'standalone',
        reset: 1
      };

      if (contactId) {
        formParams.civicase_cid = contactId;
      }

      var formUrl = getCrmUrl('civicrm/case/add', formParams);

      loadForm(formUrl)
        .on('crmFormSuccess crmPopupFormSuccess', callback);
    }

    /**
     * Redirects the user to the custom webform URL as defined in the configuration.
     *
     * @param {string} webformSettings web form settings
     * @param {string} contactId contact id
     */
    function redirectToCustomNewCaseWebformUrl (webformSettings, contactId) {
      var url = webformSettings.newCaseWebformUrl;

      if (contactId) {
        url += '?' + webformSettings.newCaseWebformClient + '=' + contactId;
      }
      $window.location.href = url;
    }
  }
})(CRM._, angular, CRM.checkPerm, CRM.loadForm, CRM.url);
