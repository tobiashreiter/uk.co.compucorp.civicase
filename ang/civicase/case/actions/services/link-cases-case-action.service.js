(function (angular, $, _) {
  var module = angular.module('civicase');

  module.service('LinkCasesCaseAction', LinkCasesCaseAction);

  function LinkCasesCaseAction () {
    /**
     * Click event handler for the Action
     *
     * @param {Array} cases
     * @param {Object} action
     * @param {Function} callbackFn
     */
    this.doAction = function (cases, action, callbackFn) {
      var case1 = cases[0];
      var case2 = cases[1];
      var activityTypes = CRM.civicase.activityTypes;
      var link = {
        path: 'civicrm/case/activity',
        query: {
          action: 'add',
          reset: 1,
          cid: case1.client[0].contact_id,
          atype: _.findKey(activityTypes, { name: 'Link Cases' }),
          caseid: case1.id
        }
      };

      if (case2) {
        link.query.link_to_case_id = case2.id;
      }

      return link;
    };
  }
})(angular, CRM.$, CRM._);
