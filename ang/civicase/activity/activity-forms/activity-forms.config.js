(function (angular) {
  var module = angular.module('civicase');

  module.config(activityFormsConfiguration);

  /**
   * Configures the list of activity forms services that will be available
   * when displaying a form for a particular activity.
   *
   * @param {object} ActivityFormsProvider the activity forms provider.
   */
  function activityFormsConfiguration (ActivityFormsProvider) {
    var addActivityFormConfigs = [
      {
        name: 'DraftPdfActivityForm',
        weight: 0
      },
      {
        name: 'DraftEmailActivityForm',
        weight: 1
      },
      {
        name: 'ActivityPopupForm',
        weight: 2
      },
      {
        name: 'ViewActivityForm',
        weight: 3
      }
    ];

    ActivityFormsProvider.addActivityForms(addActivityFormConfigs);
  }
})(angular);
