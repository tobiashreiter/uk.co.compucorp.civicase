(function (angular, getCrmUrl) {
  var module = angular.module('civicase');

  module.service('ActivityPopupForm', ActivityPopupForm);

  /**
   * Activity Popup Form service.
   */
  function ActivityPopupForm () {
    this.canHandleActivity = canHandleActivity;
    this.getActivityFormUrl = getActivityFormUrl;

    /**
     * Only handles activity forms that will be displayed in a popup.
     * It supports both stand-alone activities and case activities.
     *
     * @param {object} activity an activity object.
     * @param {object} [options] form options.
     * @returns {boolean} true when it can handle the activity and form options.
     */
    function canHandleActivity (activity, options) {
      return (options && options.formType === 'popup') || false;
    }

    /**
     * @param {object} activity an activity object.
     * @returns {string} the URL for the activity form that will be displayed in a popup.
     */
    function getActivityFormUrl (activity) {
      var urlPath = 'civicrm/activity';
      var urlParams = {
        action: 'update',
        id: activity.id,
        reset: 1
      };

      if (activity.case_id) {
        urlPath = 'civicrm/case/activity';
        urlParams.caseid = activity.case_id;
      }

      return getCrmUrl(urlPath, urlParams);
    }
  }
})(angular, CRM.url);
