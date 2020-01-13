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
        name: 'ActivityPopupForm',
        weight: 0
      },
      {
        name: 'DraftEmailOrPdfActivityForm',
        weight: 1
      },
      {
        name: 'ViewActivityForm',
        weight: 2
      }
    ];

    ActivityFormsProvider.addActivityForms(addActivityFormConfigs);
  }
})(angular);
