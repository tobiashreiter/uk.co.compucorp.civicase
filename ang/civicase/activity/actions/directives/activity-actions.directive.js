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
     * @param {Object} $scope
     * @param {Object} attrs
     * @param {Object} element
     * @param {Object} caseDetails
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

  function civicaseActivityActionsController ($window, $rootScope, $scope, crmApi, getActivityFeedUrl, MoveCopyActivityAction, TagsActivityAction, DeleteActivityAction, ts) {
    $scope.ts = ts;
    $scope.getActivityFeedUrl = getActivityFeedUrl;
    $scope.deleteActivity = DeleteActivityAction.deleteActivity;
    $scope.moveCopyActivity = MoveCopyActivityAction.moveCopyActivities;
    $scope.manageTags = TagsActivityAction.manageTags;
    $scope.isActivityEditable = isActivityEditable;
    $scope.printReport = printReport;

    /**
     * Print a report for the sent activities
     *
     * @param {Array} selectedActivities
     */
    function printReport (selectedActivities) {
      var url = $scope.getPrintActivityUrl(selectedActivities);

      $window.open(url, '_blank').focus();
    }

    /**
     * Checks if the sent activity is enabled
     *
     * @param {Object} activity
     */
    function isActivityEditable (activity) {
      var activityType = CRM.civicase.activityTypes[activity.activity_type_id].name;
      var nonEditableActivityTypes = [
        'Email',
        'Print PDF Letter'
      ];

      return !_.includes(nonEditableActivityTypes, activityType) && $scope.getEditActivityUrl;
    }
  }
})(CRM.$, CRM._, angular);
