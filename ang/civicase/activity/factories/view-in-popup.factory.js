(function (angular, $, _, CRM) {
  var module = angular.module('civicase');

  module.factory('viewInPopup', function () {
    /**
     * View given activity in a popup
     *
     * @param {object} $event event
     * @param {*} activity activity to be viewed
     * @returns {object} jQuery object
     */
    function viewInPopup ($event, activity) {
      if ($event && $($event.target).is('a, a *, input, button, button *')) {
        return;
      }

      return CRM.loadForm(CRM.url(getFormUrl(activity), getFormParams(activity)));
    }

    /**
     * Get Form url for the given activity
     *
     * @param {object} activity activity for which url needs to be generated
     * @returns {string} form url
     */
    function getFormUrl (activity) {
      var activityFormUrl = 'civicrm/activity';

      if (activity.case_id) {
        activityFormUrl = 'civicrm/case/activity';
      }

      return activityFormUrl;
    }

    /**
     * Get Form parameters for the given activity
     *
     * @param {object} activity activity for which parameters needs to be generated
     * @returns {object} form parameters
     */
    function getFormParams (activity) {
      var activityFormParams = {
        action: 'update',
        id: activity.id,
        reset: 1
      };

      if (activity.case_id) {
        activityFormParams.caseid = activity.case_id;
      }

      return activityFormParams;
    }

    return viewInPopup;
  });
})(angular, CRM.$, CRM._, CRM);
