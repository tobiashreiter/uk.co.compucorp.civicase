(function (_, angular) {
  var module = angular.module('civicase');

  module.service('DraftEmailActivityForm', DraftEmailActivityForm);

  /**
   * Draft email activity form service.
   *
   * @param {Function} checkIfDraftActivity the check if draft activity function.
   * @param {Function} civicaseCrmUrl crm url service.
   */
  function DraftEmailActivityForm (checkIfDraftActivity, civicaseCrmUrl) {
    this.canChangeStatus = false;
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
     * @param {object} options a list of options to display the form.
     * @returns {string} the form URL for activities that are email drafts.
     */
    function getActivityFormUrl (activity, options) {
      var action = options && options.action === 'view'
        ? 'view'
        : 'add';
      var targetContactId = _.first(activity.target_contact_id);
      var draftFormParameters = {
        action: action,
        atype: activity.activity_type_id,
        cid: targetContactId,
        draft_id: activity.id,
        id: activity.id,
        reset: '1'
      };

      if (activity.case_id) {
        draftFormParameters.caseid = activity.case_id;
      }

      var viewUrl = 'civicrm/activity/email/';
      var addUrl = 'civicrm/case/email/';
      var url = action === 'view' ? viewUrl : addUrl;

      return civicaseCrmUrl(
        url + action,
        draftFormParameters
      );
    }
  }
})(CRM._, angular);
