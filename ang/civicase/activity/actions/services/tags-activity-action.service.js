(function (angular, $, _) {
  var module = angular.module('civicase');

  module.service('TagsActivityAction', TagsActivityAction);

  /**
   * Tags Activity Action Service
   *
   * @param {object} $rootScope rootscope object
   * @param {object} crmApi service to use civicrm api
   * @param {object} dialogService service to open dialog box
   */
  function TagsActivityAction ($rootScope, crmApi, dialogService) {
    /**
     * Check if the Action is enabled
     *
     * @param {object} $scope scope object
     * @returns {boolean} if the action is enabled
     */
    this.isActionEnabled = function ($scope) {
      return $scope.mode === 'case-activity-bulk-action';
    };

    /**
     * Perform the action
     *
     * @param {object} $scope scope object
     * @param {object} action action object
     */
    this.doAction = function ($scope, action) {
      manageTags(action.operation, $scope.selectedActivities, $scope.isSelectAll, $scope.params, $scope.totalCount);
    };

    /**
     * Add/Remove tags to activities
     *
     * @param {string} operation add or remove operation
     * @param {Array} activities list of activities
     * @param {boolean} isSelectAll if select all checkbox is true
     * @param {object} params search parameters for activities to be moved/copied
     * @param {number} totalCount total number of activities, used when isSelectAll is true
     */
    function manageTags (operation, activities, isSelectAll, params, totalCount) {
      var title, saveButtonLabel;
      title = saveButtonLabel = 'Tag Activities';

      if (operation === 'remove') {
        title += ' (Remove)';
        saveButtonLabel = 'Remove tags from Activities';
      }

      getTags()
        .then(function (tags) {
          var model = setModelObjectForModal(tags, activities.length || totalCount);

          openTagsModal(model, title, saveButtonLabel, operation, {
            selectedActivities: activities,
            isSelectAll: isSelectAll,
            searchParams: params
          });
        });
    }

    /**
     * Set the model object to be used in the modal
     *
     * @param {Array} tags tags
     * @param {number} numberOfActivities number of activities
     * @returns {object} model object for the dialog box
     */
    function setModelObjectForModal (tags, numberOfActivities) {
      var model = {};

      model.selectedActivitiesLength = numberOfActivities;
      model.genericTags = prepareGenericTags(tags);
      model.tagSets = prepareTagSetsTree(tags);

      model.selectedGenericTags = [];
      model.toggleGenericTags = toggleGenericTags;

      return model;
    }

    /**
     * Toggle the State of Generic tags
     *
     * @param {object} model model object for dialog box
     * @param {string} tagID id of the tag
     */
    function toggleGenericTags (model, tagID) {
      if (model.selectedGenericTags.indexOf(tagID) === -1) {
        model.selectedGenericTags.push(tagID);
      } else {
        model.selectedGenericTags = _.reject(model.selectedGenericTags, function (tag) {
          return tag === tagID;
        });
      }
    }

    /**
     * Opens the modal for addition/removal of tags
     *
     * @param {object} model model object for dialog box
     * @param {string} title title of the dialog box
     * @param {string} saveButtonLabel label for the save button
     * @param {string} operation name of the operation, add/ remove
     * @param {object} activitiesObject object containing configuration of activities
     */
    function openTagsModal (model, title, saveButtonLabel, operation, activitiesObject) {
      dialogService.open('TagsActivityAction', '~/civicase/activity/actions/services/tags-activity-action.html', model, {
        autoOpen: false,
        height: 'auto',
        width: '40%',
        title: title,
        buttons: [{
          text: saveButtonLabel,
          icons: operation === 'add' ? { primary: 'fa-check' } : false,
          click: function () {
            addRemoveTagsConfirmationHandler.call(this, operation, activitiesObject, model);
          }
        }]
      });
    }

    /**
     * Add/Remove tags confirmation handler
     *
     * @param {string} operation name of the operation, add/ remove
     * @param {object} activitiesObject object containing configuration of activities
     * @param {object} model model object for dialog box
     */
    function addRemoveTagsConfirmationHandler (operation, activitiesObject, model) {
      var tagIds = model.selectedGenericTags;

      _.each(model.tagSets, function (tag) {
        if (tag.selectedTags) {
          tagIds = tagIds.concat(JSON.parse('[' + tag.selectedTags + ']'));
        }
      });

      var apiCalls = prepareApiCalls(operation, activitiesObject, tagIds);

      crmApi(apiCalls)
        .then(function () {
          $rootScope.$broadcast('civicase::activity::updated');
        });

      $(this).dialog('close');
    }

    /**
     * Prepare the API calls for the Add/Remove operation
     *
     * @param {string} operation name of the operation, add/ remove
     * @param {object} activitiesObject object containing configuration of activities
     * @param {Array} tagIds list of tag ids
     * @returns {Array} configuration for the api call
     */
    function prepareApiCalls (operation, activitiesObject, tagIds) {
      var action = operation === 'add' ? 'createByQuery' : 'deleteByQuery';

      if (activitiesObject.isSelectAll) {
        return [['EntityTag', action, {
          entity_table: 'civicrm_activity',
          tag_id: tagIds,
          params: activitiesObject.searchParams
        }]];
      } else {
        return [['EntityTag', action, {
          entity_table: 'civicrm_activity',
          tag_id: tagIds,
          entity_id: activitiesObject.selectedActivities.map(function (activity) {
            return activity.id;
          })
        }]];
      }
    }

    /**
     * Get the tags for Activities from API end point
     *
     * @returns {Promise} api call promise
     */
    function getTags () {
      return crmApi('Tag', 'get', {
        sequential: 1,
        used_for: { LIKE: '%civicrm_activity%' }
      }).then(function (data) {
        return data.values;
      });
    }

    /**
     * Recursive function to prepare the generic tags
     *
     * @param {Array} tags tags
     * @param {string} parentID id of the parent tag
     * @param {number} level level of tag
     * @returns {Array} tags list
     */
    function prepareGenericTags (tags, parentID, level) {
      var returnArray = [];

      level = typeof level !== 'undefined' ? level : 0;
      parentID = typeof parent !== 'undefined' ? parentID : undefined;

      var filteredTags = _.filter(tags, function (child) {
        return child.parent_id === parentID && child.is_tagset === '0';
      });

      if (_.isEmpty(filteredTags)) {
        return [];
      }

      _.each(filteredTags, function (tag) {
        returnArray.push(tag);
        tag.indentationLevel = level;
        returnArray = returnArray.concat(prepareGenericTags(tags, tag.id, level + 1));
      });

      return returnArray;
    }

    /**
     * Prepares the tag sets tree
     *
     * @param {Array} tags list of tags
     * @returns {Array} tags tree
     */
    function prepareTagSetsTree (tags) {
      var returnArray = [];

      var filteredTags = _.filter(tags, function (child) {
        return !child.parent_id && child.is_tagset === '1';
      });

      if (_.isEmpty(filteredTags)) {
        return [];
      }

      _.each(filteredTags, function (tag) {
        var children = _.filter(tags, function (child) {
          if (child.parent_id === tag.id && child.is_tagset === '0') {
            child.text = child.name;
            return true;
          }
        });

        if (children.length > 0) {
          tag.children = children;
        }

        returnArray.push(tag);
      });

      return returnArray;
    }
  }
})(angular, CRM.$, CRM._);
