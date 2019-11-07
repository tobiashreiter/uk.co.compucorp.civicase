(function (angular) {
  var module = angular.module('civicase');

  module.service('ViewActivityAction', ViewActivityAction);

  /**
   * View Activity Action
   */
  function ViewActivityAction () {
    /**
     * Check if the Action is enabled
     *
     * @param {object} $scope scope object
     * @returns {boolean} if the action is enabled
     */
    this.isActionEnabled = function ($scope) {
      var isBulkAction = $scope.mode === 'case-activity-bulk-action';
      var isDraftCommunicationTypeActivity = $scope.selectedActivities[0].category.indexOf('communication') >= 0 &&
        $scope.selectedActivities[0].status_name === 'Draft';

      return !isBulkAction && !isDraftCommunicationTypeActivity;
    };
  }
})(angular);
