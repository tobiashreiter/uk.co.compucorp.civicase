(function (angular, $, _) {
  var module = angular.module('civicase');

  module.service('MoveCopyActivityAction', MoveCopyActivityAction);

  /**
   * Move Copy Activity Action Service
   *
   * @param {object} $rootScope rootscope object
   * @param {object} crmApi service to call civicrm api
   * @param {object} dialogService service for opening dialog box
   */
  function MoveCopyActivityAction ($rootScope, crmApi, dialogService) {
    var ts = CRM.ts('civicase');

    /**
     * Perform the action
     *
     * @param {object} $scope scope object
     * @param {object} action action object
     */
    this.doAction = function ($scope, action) {
      moveCopyActivities($scope.selectedActivities, action.operation, $scope.isSelectAll, $scope.params, $scope.totalCount);
    };

    /**
     * Move/Copy activities
     *
     * @param {Array} activities list of activities
     * @param {string} operation move or copy operation
     * @param {boolean} isSelectAll if select all checkbox is true
     * @param {object} params search parameters for activities to be moved/copied
     * @param {number} totalCount total number of activities, used when isSelectAll is true
     */
    function moveCopyActivities (activities, operation, isSelectAll, params, totalCount) {
      var activitiesCopy = _.cloneDeep(activities);
      var title = operation[0].toUpperCase() + operation.slice(1) +
        ((activities.length === 1)
          ? ts(' %1Activity', { 1: activities[0].type ? activities[0].type + ' ' : '' })
          : ts(' %1 Activities', { 1: isSelectAll ? totalCount : activities.length }));
      var model = {
        ts: ts,
        case_id: (activities.length > 1 || isSelectAll) ? '' : activitiesCopy[0].case_id,
        isSubjectVisible: activities.length === 1,
        subject: (activities.length > 1 || isSelectAll) ? '' : activitiesCopy[0].subject
      };

      dialogService.open('MoveCopyActCard', '~/civicase/activity/actions/services/move-copy-activity-action.html', model, {
        autoOpen: false,
        height: 'auto',
        width: '40%',
        title: title,
        buttons: [{
          text: ts('Save'),
          icons: { primary: 'fa-check' },
          click: function () {
            moveCopyConfirmationHandler.call(this, operation, model, {
              selectedActivities: activities,
              isSelectAll: isSelectAll,
              searchParams: params
            });
          }
        }]
      });
    }

    /**
     * Handles the click event when the move/copy operation is confirmed
     *
     * @param {string} operation move or copy operation
     * @param {object} model model object for dialog box
     * @param {object} activitiesObject object containing configuration of activities
     */
    function moveCopyConfirmationHandler (operation, model, activitiesObject) {
      var isCaseIdNew = !_.find(activitiesObject.selectedActivities, function (activity) {
        return activity.case_id === model.case_id;
      });

      if (model.case_id && isCaseIdNew) {
        var apiCalls = prepareApiCalls(activitiesObject, operation, model);

        crmApi(apiCalls)
          .then(function () {
            $rootScope.$broadcast('civicase::activity::updated');
          });
      }

      $(this).dialog('close');
    }

    /**
     * Prepare the API calls for the move/copy operation
     *
     * @param {object} activitiesObject object containing configuration of activities
     * @param {string} operation move or copy operation
     * @param {object} model model object for dialog box
     * @returns {Array} api call configuration
     */
    function prepareApiCalls (activitiesObject, operation, model) {
      if (activitiesObject.selectedActivities.length === 1) {
        return prepareAPICallsForSingleActivity(activitiesObject, operation, model);
      } else {
        return prepareAPICallsForMultipleActivities(activitiesObject, operation, model);
      }
    }

    /**
     * Prepare the API calls for the move/copy operation
     *
     * @param {object} activitiesObject object containing configuration of activities
     * @param {string} operation move or copy operation
     * @param {object} model model object for dialog box
     * @returns {Array} api call configuration
     */
    function prepareAPICallsForSingleActivity (activitiesObject, operation, model) {
      var activity = activitiesObject.selectedActivities[0];

      if (operation === 'copy') {
        delete activity.id;
      }

      activity.subject = model.subject;
      activity.case_id = model.case_id;

      return [['Activity', 'create', activity]];
    }

    /**
     * Prepare the API calls for the move/copy operation
     *
     * @param {object} activitiesObject object containing configuration of activities
     * @param {string} operation move or copy operation
     * @param {object} model model object for dialog box
     * @returns {Array} api call configuration
     */
    function prepareAPICallsForMultipleActivities (activitiesObject, operation, model) {
      var action = operation === 'copy' ? 'copybyquery' : 'movebyquery';

      if (activitiesObject.isSelectAll) {
        return [['Activity', action, {
          params: activitiesObject.searchParams,
          case_id: model.case_id
        }]];
      } else {
        return [['Activity', action, {
          case_id: model.case_id,
          id: activitiesObject.selectedActivities.map(function (activity) {
            return activity.id;
          }),
          subject: model.subject
        }]];
      }
    }
  }
})(angular, CRM.$, CRM._);
