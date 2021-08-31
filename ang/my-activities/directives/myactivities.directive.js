(function (angular, _) {
  var module = angular.module('my-activities');

  module.directive('civicaseMyActivities', function () {
    return {
      restrict: 'EA',
      controller: 'CivicaseMyActivitiesController',
      templateUrl: '~/my-activities/directives/myactivities.directive.html',
      scope: {}
    };
  });

  module.controller('CivicaseMyActivitiesController', CivicaseMyActivitiesController);

  /**
   * @param {object} $scope the controller scope
   * @param {object} Contact Contact global service
   * @param {object} ActivityStatus Activity Status service
   */
  function CivicaseMyActivitiesController ($scope, Contact, ActivityStatus) {
    var INCOMPLETE_ACTIVITY_STATUS_CATEGORY = '0';
    var isPermissionAvailableToSeeCasesActivities =
      (CRM.checkPerm('access my cases and activities') ||
      CRM.checkPerm('access all cases and activities'));

    $scope.displayOptions = {
      include_case: isPermissionAvailableToSeeCasesActivities
    };
    $scope.filters = {
      $contact_id: Contact.getCurrentContactID(),
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
        return status.filter === INCOMPLETE_ACTIVITY_STATUS_CATEGORY;
      }).map(function (status) {
        return status.value;
      });
    }
  }
})(angular, CRM._);
