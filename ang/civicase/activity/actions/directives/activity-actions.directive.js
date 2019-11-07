(function ($, _, angular) {
  var module = angular.module('civicase');

  module.directive('civicaseActivityActions', function () {
    return {
      scope: {
        mode: '@',
        selectedActivities: '=',
        isSelectAll: '=',
        totalCount: '=',
        params: '='
      },
      require: '?^civicaseCaseDetails',
      controller: civicaseActivityActionsController,
      templateUrl: '~/civicase/activity/actions/directives/activity-actions.directive.html',
      restrict: 'A',
      link: civicaseActivityActionsLink
    };

    /**
     * Angular JS's link function for the directive civicaseActivityActions
     *
     * @param {object} $scope angular scope
     * @param {object} attrs attributes
     * @param {object} element element
     * @param {object} caseDetails case details service
     */
    function civicaseActivityActionsLink ($scope, attrs, element, caseDetails) {
      if (caseDetails) {
        // TODO - Unit test pending
        $scope.isCaseSummaryPage = true;
        $scope.getEditActivityUrl = caseDetails.getEditActivityUrl;
        $scope.getPrintActivityUrl = caseDetails.getPrintActivityUrl;
      }
    }
  });

  module.controller('civicaseActivityActionsController', civicaseActivityActionsController);

  /**
   * @param {object} $injector injector service
   * @param {object} $scope scope object
   * @param {object} ts ts
   * @param {Array} ActivityActions activity actions
   */
  function civicaseActivityActionsController ($injector, $scope, ts, ActivityActions) {
    $scope.ts = ts;
    $scope.isActionEnabled = isActionEnabled;
    $scope.doAction = doAction;
    $scope.activityActions = ActivityActions;

    /**
     * Get Case Action Service
     *
     * @param {string} serviceName name of the service
     * @returns {object/null} action service
     */
    function getActionService (serviceName) {
      try {
        return $injector.get(serviceName);
      } catch (e) {
        return null;
      }
    }

    /**
     * Check if action is enabled
     *
     * @param {object} action action object
     * @returns {boolean} if action is enabled
     */
    function isActionEnabled (action) {
      var service = getActionService(action.serviceName);
      var isActionEnabledFn = service ? service.isActionEnabled : false;

      return isActionEnabledFn ? isActionEnabledFn($scope) : true;
    }

    /**
     * Perform Action
     *
     * @param {object} action action object
     */
    function doAction (action) {
      var service = getActionService(action.serviceName);

      if (service) {
        service.doAction($scope, action);
      }
    }
  }
})(CRM.$, CRM._, angular);
