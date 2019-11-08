(function (angular, $, _, CRM) {
  var module = angular.module('civicase-base');

  module.service('ActivityType', ActivityType);

  /**
   * Activity Types Service
   */
  function ActivityType () {
    var activityTypes = CRM['civicase-base'].activityTypes;

    this.getAll = function () {
      return activityTypes;
    };

    this.findById = function (id) {
      return activityTypes[id];
    };
  }
})(angular, CRM.$, CRM._, CRM);
