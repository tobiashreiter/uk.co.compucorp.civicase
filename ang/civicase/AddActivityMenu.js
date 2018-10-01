(function ($, _, angular, activityTypes, caseTypes) {
  var module = angular.module('civicase');

  module.directive('civicaseAddActivityMenu', function () {
    return {
      restrict: 'E',
      scope: {
        case: '='
      },
      controller: 'civicaseAddActivityMenuController',
      templateUrl: '~/civicase/AddActivityMenu.html'
    };
  });

  module.controller('civicaseAddActivityMenuController', function ($scope, getCaseQueryParams) {
    var activityTypes = $scope.activityTypes = CRM.civicase.activityTypes;
    var definition = CRM.civicase.caseTypes[$scope.case.case_type_id].definition;

    $scope.newActivityUrl = newActivityUrl;

    (function init () {
      if (_.isEmpty($scope.case.activity_count)) {
        $scope.case.activity_count = getActivitiesCount();
        $scope.availableActivityTypes = getAvailableActivityTypes(
          $scope.case.activity_count, definition);
      } else {
        $scope.$watch('case.definition', function (definition) {
          if (!definition) {
            return;
          }

          $scope.availableActivityTypes = getAvailableActivityTypes(
            $scope.case.activity_count, definition);
        });
      }
    })();

    /**
     * Returns the current activities count using all the activities included in the case.
     *
     * @return {Object} in the form of { activity_type_id: count }
     */
    function getActivitiesCount () {
      return _.transform($scope.case.allActivities, function (activitiesCount, activity) {
        var currentCount = activitiesCount[activity.activity_type_id] || 0;
        activitiesCount[activity.activity_type_id] = currentCount + 1;
      }, {});
    }

    /**
     * Returns a list of activity types that can be created for the case. Cases
     * activities can have a maximum count which must be respected.
     *
     * @param {Object} activityCount the list of activity types and their count for the given case.
     * @param {Object} definition the case type definition for the given case.
     * @return {Array}
     */
    function getAvailableActivityTypes (activityCount, definition) {
      var ret = [];
      var exclude = ['Change Case Status', 'Change Case Type'];

      _.each(definition.activityTypes, function (actSpec) {
        if (exclude.indexOf(actSpec.name) < 0) {
          var actTypeId = _.findKey(activityTypes, {name: actSpec.name});
          ret.push($.extend({id: actTypeId}, activityTypes[actTypeId]));
        }
      });

      return _.sortBy(ret, 'label');
    }

    /**
     * Returns the URL with the form necessary to create a particular activity for the case.
     *
     * @param {Object} actType
     * @return {String}
     */
    function newActivityUrl (actType) {
      var caseQueryParams = JSON.stringify(getCaseQueryParams($scope.case.id));
      var path = 'civicrm/case/activity';
      var args = {
        action: 'add',
        reset: 1,
        cid: $scope.case.client[0].contact_id,
        caseid: $scope.case.id,
        atype: actType.id,
        civicase_reload: caseQueryParams
      };

      // CiviCRM requires nonstandard urls for a couple special activity types
      if (actType.name === 'Email') {
        path = 'civicrm/activity/email/add';
        args.context = 'standalone';
        delete args.cid;
      }

      if (actType.name === 'Print PDF Letter') {
        path = 'civicrm/activity/pdf/add';
        args.context = 'standalone';
      }

      return CRM.url(path, args);
    }
  });
})(CRM.$, CRM._, angular, CRM.civicase.activityTypes, CRM.civicase.caseTypes);
