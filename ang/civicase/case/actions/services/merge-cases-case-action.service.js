(function (angular, $, _) {
  var module = angular.module('civicase');

  module.service('MergeCasesCaseAction', MergeCasesCaseAction);

  function MergeCasesCaseAction () {
    /**
     * Click event handler for the Action
     *
     * @param {Array} cases
     * @param {Object} action
     * @param {Function} callbackFn
     */
    this.doAction = function (cases, action, callbackFn) {
      var ts = CRM.ts('civicase');
      var msg = ts('Merge all activitiy records into a single case?');

      if (cases[0].case_type_id !== cases[1].case_type_id) {
        msg += '<br />' + ts('Warning: selected cases are of different types.');
      }

      if (!angular.equals(cases[0].client, cases[1].client)) {
        msg += '<br />' + ts('Warning: selected cases belong to different clients.');
      }

      CRM.confirm({ title: action.title, message: msg })
        .on('crmConfirm:yes', function () {
          callbackFn([
            [
              'Case',
              'merge',
              { case_id_1: cases[0].id, case_id_2: cases[1].id }
            ]
          ]);
        });
    };
  }
})(angular, CRM.$, CRM._);
