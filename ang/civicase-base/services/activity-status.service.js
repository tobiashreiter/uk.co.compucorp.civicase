(function (angular, $, _, CRM) {
  var module = angular.module('civicase-base');

  module.service('ActivityStatus', ActivityStatus);

  /**
   * Activity Status Service
   *
   * @param {Function} isTruthy service to check if value is truthy
   */
  function ActivityStatus (isTruthy) {
    var allActivityStatuses = CRM['civicase-base'].activityStatuses;
    var activeActivityStatuses = _.chain(allActivityStatuses)
      .filter(function (activityStatus) {
        return isTruthy(activityStatus.is_active);
      })
      .indexBy('value')
      .value();

    this.getAll = getAll;

    /**
     * Get all Activity statuses
     *
     * @param {Array} includeInactive if disabled option values also should be returned
     * @returns {Array} all activity statuses
     */
    function getAll (includeInactive) {
      var returnValue = includeInactive ? allActivityStatuses : activeActivityStatuses;

      return returnValue;
    }
  }
})(angular, CRM.$, CRM._, CRM);
