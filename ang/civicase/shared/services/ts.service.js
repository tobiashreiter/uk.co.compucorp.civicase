(function (angular, _, crmTs) {
  var module = angular.module('civicase');
  var CIVICASE_DOMAIN = 'uk.co.compucorp.civicase';

  module.factory('ts', function () {
    return ts;
  });

  /**
   * This service was added for use instead of the default
   * Civi ts function. This function helps to avoid passing
   * the domain parameter to the ts function anywhere it is
   * called.
   * Also fixes an issue with ts not working properly with
   * domains unless window.ts is used.
   *
   * @param {String} message
   * @param {Object} options
   */
  function ts (message, options) {
    return crmTs(message, _.assign({}, {
      domain: CIVICASE_DOMAIN
    }, options || {}));
  }
})(angular, CRM._, window.ts);
