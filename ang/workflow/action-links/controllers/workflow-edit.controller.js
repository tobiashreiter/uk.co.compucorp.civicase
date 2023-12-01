(function ($, _, angular) {
  var module = angular.module('workflow');

  module.controller('WorkflowEditController', WorkflowEditController);

  /**
   * @param {object} $scope scope object
   * @param {object} $window browsers window object
   * @param {object} $injector injector service of angular
   * @param {object} pascalCase service to convert a string to pascal case
   * @param {object} CaseTypeCategory case type category service
   * @param {Function} civicaseCrmUrl crm url service
   */
  function WorkflowEditController ($scope, $window, $injector, pascalCase,
    CaseTypeCategory, civicaseCrmUrl) {
    $scope.clickHandler = clickHandler;

    /**
     * Redirects to the Edit Workflow page
     *
     * @param {string/number} workflow workflow object
     */
    function clickHandler (workflow) {
      var instanceName = CaseTypeCategory
        .getCaseTypeCategoryInstance(workflow.case_type_category).name;

      var url, fragment;

      [url, fragment] = getServiceForInstance(instanceName).getEditWorkflowURL(workflow).split('#');
      
      $window.location.href = civicaseCrmUrl(url) + '#' + fragment;
    }

    /**
     * Searches for a angularJS service for the current case type category
     * instance, if not found, returns the service for case management service
     * as default.
     *
     * @param {string} instanceName name of the instance
     * @returns {object/null} service
     */
    function getServiceForInstance (instanceName) {
      var CASE_MANAGEMENT_INSTACE_NAME = 'case_management';

      try {
        return $injector.get(
          pascalCase(instanceName) + 'Workflow'
        );
      } catch (e) {
        return $injector.get(
          pascalCase(CASE_MANAGEMENT_INSTACE_NAME) + 'Workflow'
        );
      }
    }
  }
})(CRM.$, CRM._, angular);
