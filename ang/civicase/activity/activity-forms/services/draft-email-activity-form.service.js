(function (_, angular, getCrmUrl) {
  var module = angular.module('civicase');

  module.service('DraftEmailActivityForm', DraftEmailActivityForm);

  /**
   * Draft email activity form service.
   *
   * @param {Function} checkIfDraftActivity the check if draft activity function.
   */
  function DraftEmailActivityForm (checkIfDraftActivity) {
    this.canHandleActivity = checkIfDraftEmailOrPDFActivity;
    this.getActivityFormUrl = getActivityFormUrl;

    /**
     * @param {object} activity an activity object.
     * @returns {boolean} true when the activity status is draft and the type is an email.
     */
    function checkIfDraftEmailOrPDFActivity (activity) {
      return checkIfDraftActivity(activity, ['Email']);
    }

    /**
     * @param {object} activity an activity object.
     * @param {object} [optionsWithoutDefaults={action: 'add'}]
     *   a list of options to display the form.
     * @returns {string} the form URL for activities that are email drafts.
     */
    function getActivityFormUrl (activity, optionsWithoutDefaults) {
      var options = _.defaults({}, optionsWithoutDefaults, { action: 'add' });

      return getCrmUrl('civicrm/activity/email/' + options.action, {
        action: options.action,
        caseId: activity.case_id,
        context: 'standalone',
        draft_id: activity.id,
        reset: '1'
      });
    }
  }
})(CRM._, angular, CRM.url);
