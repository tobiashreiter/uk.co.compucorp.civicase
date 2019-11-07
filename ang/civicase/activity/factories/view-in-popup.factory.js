(function (angular, $, _, CRM) {
  var module = angular.module('civicase');

  module.factory('viewInPopup', function () {
    return function ($event, activity) {
      var activityFormUrl = 'civicrm/activity';
      var activityFormParams = {
        action: 'update',
        id: activity.id,
        reset: 1
      };

      if ($event && $($event.target).is('a, a *, input, button, button *')) {
        return;
      }

      if (activity.case_id) {
        activityFormUrl = 'civicrm/case/activity';
        activityFormParams.caseid = activity.case_id;
      }

      return CRM.loadForm(CRM.url(activityFormUrl, activityFormParams));
    };
  });
})(angular, CRM.$, CRM._, CRM);
