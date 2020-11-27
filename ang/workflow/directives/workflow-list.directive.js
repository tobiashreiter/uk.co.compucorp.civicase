(function ($, _, angular) {
  var module = angular.module('workflow');

  module.directive('workflowList', function () {
    return {
      scope: {
        caseTypeCategory: '@'
      },
      controller: 'workflowListController',
      templateUrl: '~/workflow/directives/workflow-list.directive.html',
      restrict: 'E'
    };
  });

  module.controller('workflowListController', workflowListController);

  /**
   * @param {object} $scope scope object
   * @param {object} $injector injector service of angular
   * @param {object} ts translation service
   * @param {object[]} WorkflowListColumns list of workflow list columns
   * @param {object[]} WorkflowListActionItems list of workflow list action items
   * @param {object} CaseTypeCategory case type catgory service
   * @param {object} pascalCase service to convert a string to pascal case
   * @param {object[]} WorkflowListFilters list of workflow filters
   */
  function workflowListController ($scope, $injector, ts,
    WorkflowListColumns, WorkflowListActionItems, CaseTypeCategory,
    pascalCase, WorkflowListFilters) {
    $scope.ts = ts;
    $scope.isLoading = false;
    $scope.workflows = [];
    $scope.actionItems = WorkflowListActionItems;
    $scope.tableColumns = filterArrayForCurrentInstance(WorkflowListColumns);
    $scope.filters = filterArrayForCurrentInstance(WorkflowListFilters);
    $scope.selectedFilters = {};
    $scope.refreshWorkflowsList = refreshWorkflowsList;
    $scope.redirectToWorkflowCreationScreen = redirectToWorkflowCreationScreen;

    (function init () {
      applyDefaultValueToFilters();
      refreshWorkflowsList();

      $scope.$on('workflow::list::refresh', refreshWorkflowsList);
    }());

    /**
     * Apply default value to filters
     */
    function applyDefaultValueToFilters () {
      _.each($scope.filters, function (filter) {
        $scope.selectedFilters[filter.filterIdentifier] = filter.defaultValue;
      });
    }

    /**
     * Apply default value to filters
     */
    function redirectToWorkflowCreationScreen () {
      var categoryObject = CaseTypeCategory.findByName($scope.caseTypeCategory);
      var instanceName = CaseTypeCategory.getCaseTypeCategoryInstance(categoryObject.value).name;

      getServiceForInstance(instanceName)
        .redirectToWorkflowCreationScreen(categoryObject);
    }

    /**
     * Preapres visibility settings for the sent array
     *
     * @param {object[]} arrayList array list
     * @returns {object[]} list
     */
    function filterArrayForCurrentInstance (arrayList) {
      return _.filter(arrayList, function (arrayItem) {
        return !arrayItem.onlyVisibleForInstance ||
          CaseTypeCategory.isInstance(
            $scope.caseTypeCategory,
            arrayItem.onlyVisibleForInstance
          );
      });
    }

    /**
     * Refresh workflows list
     */
    function refreshWorkflowsList () {
      $scope.isLoading = true;

      getWorkflows($scope.caseTypeCategory)
        .then(function (workflows) {
          $scope.workflows = workflows;
        })
        .finally(function () {
          $scope.isLoading = false;
        });
    }

    /**
     * Get list of workflows for the sent case type category
     *
     * @param {string} caseTypeCategory case type category
     * @returns {Promise} list of workflows
     */
    function getWorkflows (caseTypeCategory) {
      var categoryObject = CaseTypeCategory.findByName(caseTypeCategory);
      var instanceName = CaseTypeCategory.getCaseTypeCategoryInstance(categoryObject.value).name;

      return getServiceForInstance(instanceName)
        .getWorkflowsList($scope.caseTypeCategory, $scope.selectedFilters);
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
