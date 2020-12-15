(function (angular, $, _, CRM) {
  var module = angular.module('civicase-base');

  module.service('IndividualSuffix', IndividualSuffix);

  /**
   * Individual Suffix Service
   */
  function IndividualSuffix () {
    this.getAll = function () {
      return _.chain(CRM['civicase-base'].individualSuffix)
        .values()
        .pluck('name')
        .value();
    };
  }
})(angular, CRM.$, CRM._, CRM);
