(function (angular, $, _) {
  var module = angular.module('civicase');

  module.directive('civicaseMyActivitiesTab', function () {
    return {
      restrict: 'EA',
      controller: 'CivicaseMyActivitiesTabController',
      templateUrl: '~/civicase/myactivities/directives/my-activities-tab.directive.html',
      scope: {}
    };
  });

  module.controller('CivicaseMyActivitiesTabController', CivicaseMyActivitiesTabController);

  /**
   * @param {object} $scope the controller scope
   * @param {object} Contact Contact global service
   * @param {object} ActivityStatus Activity Status service
   */
  function CivicaseMyActivitiesTabController ($scope, Contact, ActivityStatus) {
    $scope.displayOptions = { include_case: true };
    $scope.filters = {
      $contact_id: Contact.getContactIDFromUrl(),
      '@involvingContact': 'myActivities',
      status_id: getIncompleteActivityStatusIDs()
    };

    /**
     * Get Activity Status IDs where status category is 'Incomplete'
     *
     * @returns {Array} list of ids
     */
    function getIncompleteActivityStatusIDs () {
      return _.filter(ActivityStatus.getAll(), function (status) {
        return status.filter === '0';
      }).map(function (status) {
        return status.value;
      });
    }
  }
})(angular, CRM.$, CRM._);
