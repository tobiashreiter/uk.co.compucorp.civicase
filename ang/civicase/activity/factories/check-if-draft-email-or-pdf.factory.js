(function (_, angular) {
  var module = angular.module('civicase');

  module.factory('checkIfDraftEmailOrPDFActivity', checkIfDraftEmailOrPDFActivityFactory);

  /**
   * Check if draft email or PDF activity service.
   *
   * @param {object} ActivityType the activity type service.
   * @returns {Function} the service function.
   */
  function checkIfDraftEmailOrPDFActivityFactory (ActivityType) {
    var emailOrPdfActivityNames = ['Email', 'Print PDF Letter'];

    return checkIfDraftEmailOrPDFActivity;

    /**
     * @param {object} activity an activity object.
     * @returns {boolean} true if the activity is either an email draft or a PDF draft.
     */
    function checkIfDraftEmailOrPDFActivity (activity) {
      var activityType = ActivityType.findById(activity.activity_type_id);
      var isEmailOrPdf = _.includes(emailOrPdfActivityNames, activityType.name);
      var isDraft = activity.status_name === 'Draft';

      return isEmailOrPdf && isDraft;
    }
  }
})(CRM._, angular);
