(function (angular, $, _, CRM) {
  var module = angular.module('civicase-base');

  module.service('IndividualPrefix', IndividualPrefix);

  /**
   * Tag Service
   */
  function IndividualPrefix () {
    this.getAll = function () {
      return _.chain(CRM['civicase-base'].individualPrefix)
        .values()
        .pluck('name')
        .value();
    };
  }
})(angular, CRM.$, CRM._, CRM);
