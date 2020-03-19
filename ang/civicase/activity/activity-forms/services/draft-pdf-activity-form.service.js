(function (_, angular, getCrmUrl) {
  var module = angular.module('civicase');

  module.service('DraftPdfActivityForm', DraftPdfActivityForm);

  /**
   * Draft PDF activity form service.
   *
   * @param {Function} checkIfDraftActivity the check if draft activity function.
   */
  function DraftPdfActivityForm (checkIfDraftActivity) {
    this.canHandleActivity = checkIfDraftPdfLetter;
    this.getActivityFormUrl = getActivityFormUrl;

    /**
     * @param {object} activity an activity object.
     * @returns {boolean} true when the activity status is draft and the type is a PDF letter.
     */
    function checkIfDraftPdfLetter (activity) {
      return checkIfDraftActivity(activity, ['Print PDF Letter']);
    }

    /**
     * @param {object} activity an activity object.
     * @returns {string} the form URL for activities that are PDF letter drafts.
     */
    function getActivityFormUrl (activity) {
      return getCrmUrl('civicrm/activity/pdf/add', {
        action: 'add',
        cid: getActivityContacts(activity),
        context: 'standalone',
        draft_id: activity.id,
        reset: '1'
      });
    }

    /**
     * @param {object} activity an activity object.
     * @returns {number[]} a list of contacts associated to the activity. For case activities
     *   it returns the case contacts, otherwise it returns the current logged in user.
     */
    function getActivityContacts (activity) {
      return activity['case_id.contacts']
        ? _.map(activity['case_id.contacts'], 'contact_id')
        : [CRM.config.user_contact_id];
    }
  }
})(CRM._, angular, CRM.url);
