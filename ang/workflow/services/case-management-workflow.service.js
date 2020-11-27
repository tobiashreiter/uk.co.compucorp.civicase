(function (_, angular) {
  var module = angular.module('workflow');

  module.service('CaseManagementWorkflow', CaseManagementWorkflow);

  /**
   * Service for case management workflows
   *
   * @param {Function} civicaseCrmApi civicrm api service
   */
  function CaseManagementWorkflow (civicaseCrmApi) {
    this.createDuplicate = createDuplicate;
    this.getWorkflowsList = getWorkflowsList;

    /**
     * Returns workflows list for case management
     *
     * @param {object} caseTypeCategoryName case type category name
     * @returns {Array} api call parameters
     */
    function getWorkflowsList (caseTypeCategoryName) {
      return civicaseCrmApi('CaseType', 'get', {
        sequential: 1,
        case_type_category: caseTypeCategoryName,
        options: { limit: 0 }
      }).then(function (data) {
        return data.values;
      });
    }

    /**
     * @param {object} workflow workflow object
     * @returns {Array} api call parameters
     */
    function createDuplicate (workflow) {
      return civicaseCrmApi([
        ['CaseType', 'create', _.extend({}, workflow, { id: null })]
      ]);
    }
  }
})(CRM._, angular);
