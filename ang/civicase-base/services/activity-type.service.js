(function (angular, $, _, CRM) {
  var module = angular.module('civicase-base');

  module.service('ActivityType', ActivityType);

  /**
   * Activity Types Service
   */
  function ActivityType () {
    var activityTypes = CRM['civicase-base'].activityTypes;

    this.getAll = getAll;
    this.findById = findById;

    /**
     * Get all Activity types
     *
     * @returns {Array} all activity types
     */
    function getAll () {
      return activityTypes;
    }

    /**
     * Get Activity object by id
     *
     * @param {string/number} id activity id
     * @returns {object} activity object matching sent id
     */
    function findById (id) {
      return activityTypes[id];
    }
  }
})(angular, CRM.$, CRM._, CRM);
