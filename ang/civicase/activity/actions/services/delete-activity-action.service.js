(function (angular, $, _) {
  var module = angular.module('civicase');

  module.service('DeleteActivityAction', DeleteActivityAction);

  function DeleteActivityAction ($rootScope, crmApi, dialogService) {
    var ts = CRM.ts('civicase');

    /**
     * Delete Activities
     *
     * @param {Array} activities
     * @param {Boolean} isSelectAll
     * @param {Object} params
     * @param {int} totalCount
     */
    this.deleteActivity = function (activities, isSelectAll, params, totalCount) {
      var activityLength = isSelectAll ? totalCount : activities.length;

      CRM.confirm({
        title: ts('Delete Activity'),
        message: ts('Permanently delete %1 activit%2?', {1: activityLength, 2: activityLength > 1 ? 'ies' : 'y'})
      }).on('crmConfirm:yes', function () {
        var apiCalls = prepareApiCalls(activities, isSelectAll, params);

        crmApi(apiCalls)
          .then(function () {
            $rootScope.$broadcast('civicase::activity::updated');
          });
      });
    };

    /**
     * Prepare the API calls for the delete operation
     *
     * @param {Array} activities
     * @param {Boolean} isSelectAll
     * @param {Object} params
     * @return {Array}
     */
    function prepareApiCalls (activities, isSelectAll, params) {
      var apiCalls = [];

      if (isSelectAll) {
        apiCalls.push(['Activity', 'deletebyquery', {
          params: params
        }]);
      } else {
        apiCalls.push(['Activity', 'deletebyquery', {
          id: activities.map(function (activity) {
            return activity.id;
          })
        }]);
      }

      return apiCalls;
    }
  }
})(angular, CRM.$, CRM._);
