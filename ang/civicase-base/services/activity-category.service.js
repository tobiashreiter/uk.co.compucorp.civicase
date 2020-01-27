(function (angular, $, _, CRM) {
  var module = angular.module('civicase-base');

  module.service('ActivityCategory', ActivityCategory);

  /**
   * Activity Category Service
   */
  function ActivityCategory () {
    var allActivityCategories = CRM['civicase-base'].activityCategories;
    var activeActivityCategories = _.chain(allActivityCategories)
      .filter(function (activityCategory) {
        return activityCategory.is_active === '1';
      })
      .indexBy('value')
      .value();

    this.getAll = getAll;

    /**
     * Get all Activity categories
     *
     * @param {Array} keepDisabled if disabled option values also should be returned
     * @returns {Array} all activity categories
     */
    function getAll (keepDisabled) {
      var returnValue = keepDisabled ? allActivityCategories : activeActivityCategories;

      return returnValue;
    }
  }
})(angular, CRM.$, CRM._, CRM);
