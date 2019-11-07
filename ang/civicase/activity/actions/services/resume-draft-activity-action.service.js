(function (angular) {
  var module = angular.module('civicase');

  module.service('ResumeDraftActivityAction', ResumeDraftActivityAction);

  /**
   * Resume Draft Action
   *
   * @param {object} $rootScope rootscope object
   * @param {object} viewInPopup common factory to open an activity in a popup
   */
  function ResumeDraftActivityAction ($rootScope, viewInPopup) {
    /**
     * Check if the Action is enabled
     *
     * @param {object} $scope scope object
     * @returns {boolean} if the action is enabled
     */
    this.isActionEnabled = function ($scope) {
      var isBulkAction = $scope.mode === 'case-activity-bulk-action';
      if (!isBulkAction) {
        var isDraftCommunicationTypeActivity = $scope.selectedActivities[0].category.indexOf('communication') >= 0 &&
        $scope.selectedActivities[0].status_name === 'Draft';

        return isDraftCommunicationTypeActivity;
      }
    };

    /**
     * Perform the action
     *
     * @param {object} $scope scope object
     */
    this.doAction = function ($scope) {
      viewInPopup(null, $scope.selectedActivities[0])
        .on('crmFormSuccess', function () {
          $rootScope.$broadcast('civicase::activity::updated');
        });
    };
  }
})(angular);
