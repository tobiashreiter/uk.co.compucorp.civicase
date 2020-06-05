(function (angular, CRM, _) {
  var module = angular.module('civicase');

  module.service('DownloadAllActivityAction', DownloadAllActivityAction);

  /**
   * Edit Activity Action
   *
   * @param {object} $window window object
   */
  function DownloadAllActivityAction ($window) {
    /**
     * Check if the Action is enabled
     *
     * @param {object} $scope scope object
     * @returns {boolean} if the action is enabled
     */
    this.isActionEnabled = function ($scope) {
      return ($scope.mode === 'case-activity-feed' &&
          $scope.selectedActivities[0].type === 'File Upload') ||
        ($scope.mode === 'case-files-activity-bulk-action');
    };

    /**
     * Perform the action
     *
     * @param {object} $scope scope object
     */
    this.doAction = function ($scope) {
      var downloadAllParams = {};

      if ($scope.mode === 'case-activity-feed') {
        downloadAllParams.activity_ids = [$scope.selectedActivities[0].id];
      } else if ($scope.mode === 'case-files-activity-bulk-action') {
        if ($scope.isSelectAll) {
          downloadAllParams.searchParams = $scope.params;
        } else {
          downloadAllParams.activity_ids = $scope.selectedActivities.map(function (activity) {
            return activity.id;
          });
        }
      }

      $window.open(CRM.url('civicrm/case/activity/download-all-files', downloadAllParams), '_blank');
    };
  }
})(angular, CRM, CRM._);
