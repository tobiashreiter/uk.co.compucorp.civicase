(function (angular, $, _) {
  var module = angular.module('civicase');

  module.service('GoToWebformCaseAction', GoToWebformCaseAction);

  function GoToWebformCaseAction ($window) {
    /**
     * Click event handler for the Action
     *
     * @param {Array} cases
     * @param {Object} action
     * @param {Function} callbackFn
     */
    this.doAction = function (cases, action, callbackFn) {
      var window;
      var urlObject = { case1: cases[0].id };

      if (action.clientID) {
        urlObject['cid' + action.clientID] = cases[0].client[0].contact_id;
      }

      window = $window.open(CRM.url(action.path, urlObject), '_blank');
      window.focus();
    };
  }
})(angular, CRM.$, CRM._);
