(function (angular) {
  var module = angular.module('civicase');

  module.service('ViewActivityAction', ViewActivityAction);

  /**
   * View Activity Action
   *
   * @param {object} ActivityType activity type service
   */
  function ViewActivityAction (ActivityType) {
    /**
     * Check if the Action is enabled
     *
     * @param {object} $scope scope object
     * @returns {boolean} if the action is enabled
     */
    this.isActionEnabled = function ($scope) {
      var isBulkAction = $scope.mode === 'case-activity-bulk-action';

      var activityTypeName = ActivityType.findById($scope.selectedActivities[0].activity_type_id).name;

      var isDraftEmailOrPdfTypeActivity =
        (activityTypeName === 'Email' || activityTypeName === 'Print PDF Letter') &&
        $scope.selectedActivities[0].status_name === 'Draft';

      return !isBulkAction && !isDraftEmailOrPdfTypeActivity;
    };
  }
})(angular);
