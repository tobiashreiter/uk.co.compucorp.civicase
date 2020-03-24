(function (angular, $, _) {
  var module = angular.module('civicase');

  module.service('WebformsCaseAction', WebformsCaseAction);

  /**
   * Webforms action for cases.
   *
   * @param {object} $window - window object.
   *
   * @class
   */
  function WebformsCaseAction ($window) {
    /**
     * Check if action is allowed.
     *
     * @param {object} action - action data.
     * @param {object} cases - cases.
     * @param {object} attributes - item attributes.
     *
     * @returns {boolean} - true if action is allowed, false otherwise.
     */
    this.isActionAllowed = function (action, cases, attributes) {
      // Allow this action on Case details page only.
      return attributes && attributes.mode === 'case-details';
    };
  }
})(angular, CRM.$, CRM._);
